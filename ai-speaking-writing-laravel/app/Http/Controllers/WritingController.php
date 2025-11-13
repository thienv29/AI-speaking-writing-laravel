<?php

namespace App\Http\Controllers;

use App\Constants\ExerciseTypes;
use App\Models\Question;
use App\Models\Exercise;
use App\Models\ExerciseType;
use App\Models\Lesson;
use Illuminate\Support\Facades\Log;

class WritingController extends Controller
{
    /**
     * Get writing exercise type IDs
     */
    private function getWritingTypeIds(): array
    {
        return ExerciseType::whereIn('code', ExerciseTypes::writingTypes())
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get lessons with writing exercises
     */
    private function getLessonsWithWritingExercises(array $writingTypes)
    {
        return Lesson::whereHas('exercises', function ($query) use ($writingTypes) {
                $query->whereIn('type_id', $writingTypes);
            })
            ->with(['exercises' => function ($query) use ($writingTypes) {
                $query->whereIn('type_id', $writingTypes)
                    ->with(['type', 'questions' => function ($q) {
                        $q->orderBy('order_index');
                    }])
                    ->orderBy('order_index');
            }])
            ->orderBy('id')
            ->get();
    }

    /**
     * Format exercises for display
     * 
     * @param \Illuminate\Support\Collection $exercises
     * @param bool $includeDifficulty
     * @return \Illuminate\Support\Collection
     */
    private function formatExercises($exercises, bool $includeDifficulty = false)
    {
        return $exercises
            ->filter(function ($exercise) {
                return $exercise->questions->isNotEmpty();
            })
            ->map(function ($exercise) use ($includeDifficulty) {
                $firstQuestion = $exercise->questions->first();
                $formatted = [
                    'id' => $exercise->id,
                    'title' => $exercise->title,
                    'type' => $exercise->type->name,
                    'code' => $exercise->type->code,
                    'instruction' => $exercise->instruction,
                    'order_index' => $exercise->order_index,
                    'first_question_id' => $firstQuestion ? $firstQuestion->id : null,
                    'questions_count' => $exercise->questions->count()
                ];
                
                if ($includeDifficulty) {
                    $formatted['difficulty'] = $exercise->difficulty;
                }
                
                return $formatted;
            })
            ->filter(function ($exercise) {
                return !is_null($exercise['first_question_id']);
            })
            ->values();
    }

    /**
     * Display writing lessons index page
     * Shows all lessons that have writing exercises
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $writingTypes = $this->getWritingTypeIds();
            
            $lessons = $this->getLessonsWithWritingExercises($writingTypes)
                ->map(function ($lesson) {
                $exercises = $this->formatExercises($lesson->exercises);
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'level' => $lesson->level,
                    'img_url' => $lesson->img_url,
                    'exercises_count' => $exercises->count(),
                    'exercises' => $exercises
                ];
            })
            ->filter(function ($lesson) {
                return $lesson['exercises_count'] > 0;
            })
            ->values();
            
            return view('pages.user.index', compact('lessons'));
        } catch (\Throwable $e) {
            Log::error('Writing index error', ['error' => $e->getMessage()]);
            abort(500, 'Unable to load writing lessons. Please try again later.');
        }
    }
    
    /**
     * Display exercises for a specific lesson
     * Shows all lessons with cards (like index page)
     * 
     * @param int|null $id Lesson ID (ignored, shows all lessons)
     * @return \Illuminate\View\View
     */
    public function showLesson($id = null)
    {
        try {
            $writingTypes = $this->getWritingTypeIds();
            $allLessons = $this->getLessonsWithWritingExercises($writingTypes)
                ->sortBy(['level', 'title'])
                ->values();
            
            return view('pages.user.lesson', compact('allLessons'));
        } catch (\Throwable $e) {
            Log::error('Writing lesson error', ['error' => $e->getMessage()]);
            abort(500, 'Unable to load lesson exercises. Please try again later.');
        }
    }
    
    /**
     * Display exercises detail for a specific lesson
     * 
     * @param int $id Lesson ID
     * @return \Illuminate\View\View
     */
    public function showLessonExercises($id)
    {
        try {
            $writingTypes = $this->getWritingTypeIds();
            $lesson = Lesson::with(['exercises' => function ($query) use ($writingTypes) {
                $query->whereIn('type_id', $writingTypes)
                    ->with(['type', 'questions' => function ($q) {
                        $q->orderBy('order_index');
                    }])
                    ->orderBy('order_index');
            }])->findOrFail($id);
            
            $exercises = $this->formatExercises($lesson->exercises, true);
            
            if ($exercises->isEmpty()) {
                abort(404, 'No writing exercises found for this lesson');
            }
            
            return view('pages.user.lesson-exercises', compact('lesson', 'exercises'));
        } catch (\Throwable $e) {
            Log::error('Writing lesson exercises error', [
                'lesson_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($e->getCode() === 404) {
                abort(404, $e->getMessage());
            }
            
            abort(500, 'Unable to load lesson exercises. Please try again later.');
        }
    }
    
    /**
     * Display a specific writing question in embed mode (for iframe)
     * 
     * @param int $id Question ID or Exercise ID
     * @return \Illuminate\View\View
     */
    public function embed($id)
    {
        try {
            // Try to find by question ID first
            $question = Question::with([
                'exercise' => function ($query) {
                    $query->with(['type', 'lesson']);
                }
            ])->find($id);
            
            // If not found, try treating as exercise ID
            if (!$question) {
                $exercise = Exercise::with(['type', 'lesson'])->find($id);
                
                if (!$exercise) {
                    abort(404, 'Question or exercise not found');
                }
                
                // Get first question for this exercise
                $question = Question::where('exercise_id', $id)
                    ->orderBy('order_index')
                    ->with(['exercise' => function ($query) {
                        $query->with(['type', 'lesson']);
                    }])
                    ->first();
                
                if (!$question) {
                    abort(404, 'No questions found for this exercise');
                }
            }
            
            $exercise = $question->exercise;
            $exerciseType = $exercise->type;
            $lesson = $exercise->lesson;
            
            // Get all questions for navigation (optional)
            $allQuestions = Question::where('exercise_id', $exercise->id)
                ->orderBy('order_index')
                ->get(['id', 'order_index']);
            
            $supportedTypeCodes = array_unique(array_merge(ExerciseTypes::writingTypes(), ['SPS']));
            $supportedTypeIds = ExerciseType::whereIn('code', $supportedTypeCodes)
                ->pluck('id', 'code');
            
            $navigationExercises = Exercise::whereIn('type_id', $supportedTypeIds->values())
                ->with([
                    'type:id,code,name',
                    'lesson:id,title',
                    'questions' => function ($q) {
                        $q->orderBy('order_index')->select('id', 'exercise_id', 'order_index');
                    }
                ])
                ->orderBy('order_index')
                ->get(['id', 'lesson_id', 'type_id', 'title', 'instruction', 'order_index']);
            
            $navigationData = $navigationExercises
                ->groupBy(function ($exerciseItem) {
                    return optional($exerciseItem->type)->code ?? 'UNKNOWN';
                })
                ->map(function ($exercisesByType) {
                    $type = $exercisesByType->first()->type ?? null;
                    if (!$type) {
                        return null;
                    }
                    
                    $lessons = $exercisesByType->groupBy('lesson_id')
                        ->map(function ($lessonExercises) {
                            $lesson = $lessonExercises->first()->lesson ?? null;
                            if (!$lesson) {
                                return null;
                            }
                            
                            $exercises = $lessonExercises->map(function ($exerciseItem) {
                                $firstQuestion = $exerciseItem->questions->first();
                                return [
                                    'id' => $exerciseItem->id,
                                    'title' => $exerciseItem->title,
                                    'instruction' => $exerciseItem->instruction,
                                    'first_question_id' => optional($firstQuestion)->id,
                                    'order_index' => $exerciseItem->order_index,
                                ];
                            })
                            ->filter(function ($exerciseData) {
                                return !is_null($exerciseData['first_question_id']);
                            })
                            ->values();
                            
                            if ($exercises->isEmpty()) {
                                return null;
                            }
                            
                            return [
                                'id' => $lesson->id,
                                'title' => $lesson->title,
                                'exercises' => $exercises,
                            ];
                        })
                        ->filter()
                        ->values();
                    
                    if ($lessons->isEmpty()) {
                        return null;
                    }
                    
                    return [
                        'code' => $type->code,
                        'name' => $type->name,
                        'lessons' => $lessons,
                    ];
                })
                ->filter()
                ->values();
            
            $currentLessonExercises = collect();
            if ($exerciseType && $lesson) {
                $typeEntry = $navigationData->firstWhere('code', $exerciseType->code ?? '');
                if ($typeEntry) {
                    $lessonEntry = collect($typeEntry['lessons'])->firstWhere('id', $lesson->id);
                    if ($lessonEntry) {
                        $currentLessonExercises = collect($lessonEntry['exercises']);
                    }
                }
            }
            
            $allExercises = $currentLessonExercises->map(function ($exerciseItem) {
                return (object) [
                    'id' => $exerciseItem['id'],
                    'title' => $exerciseItem['title'],
                    'first_question_id' => $exerciseItem['first_question_id'],
                ];
            });
            
            // Determine previous/next question ids
            $previous = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '<', $question->order_index)
                ->orderBy('order_index', 'desc')
                ->first();

            if (!$previous) {
                $previousExercise = Exercise::where('lesson_id', $lesson->id)
                    ->where('order_index', '<', $exercise->order_index)
                    ->orderBy('order_index', 'desc')
                    ->first();

                if ($previousExercise) {
                    $previous = Question::where('exercise_id', $previousExercise->id)
                        ->orderBy('order_index', 'desc')
                        ->first();
                }
            }

            $next = Question::where('exercise_id', $question->exercise_id)
                ->where('order_index', '>', $question->order_index)
                ->orderBy('order_index', 'asc')
                ->first();

            if (!$next) {
                $nextExercise = Exercise::where('lesson_id', $lesson->id)
                    ->where('order_index', '>', $exercise->order_index)
                    ->orderBy('order_index', 'asc')
                    ->first();

                if ($nextExercise) {
                    $next = Question::where('exercise_id', $nextExercise->id)
                        ->orderBy('order_index', 'asc')
                        ->first();
                }
            }

            $question->setAttribute('prev_question_id', $previous ? $previous->id : null);
            $question->setAttribute('next_question_id', $next ? $next->id : null);
            $question->exercise->setRelation('questions', $allQuestions);

            $navigationPayload = $navigationData
                ->map(function ($typeEntry) {
                    return [
                        'code' => $typeEntry['code'],
                        'name' => $typeEntry['name'],
                        'lessons' => collect($typeEntry['lessons'])->map(function ($lessonEntry) {
                            return [
                                'id' => $lessonEntry['id'],
                                'title' => $lessonEntry['title'],
                                'exercises' => collect($lessonEntry['exercises'])->map(function ($exerciseEntry) {
                                    return [
                                        'id' => $exerciseEntry['id'],
                                        'title' => $exerciseEntry['title'],
                                        'first_question_id' => $exerciseEntry['first_question_id'],
                                        'order_index' => $exerciseEntry['order_index'],
                                        'instruction' => $exerciseEntry['instruction'],
                                    ];
                                })->values()->toArray(),
                            ];
                        })->values()->toArray(),
                    ];
                })
                ->values()
                ->toArray();

            $currentContext = [
                'type' => $exerciseType->code ?? null,
                'lesson_id' => $lesson->id ?? null,
                'exercise_id' => $exercise->id ?? null,
            ];
            
            $navigationData = $navigationPayload;
            
            // Set headers to allow iframe embedding
            $response = response()->view('pages.user.embed', compact(
                'question',
                'exercise',
                'exerciseType',
                'lesson',
                'allQuestions',
                'allExercises',
                'navigationData',
                'currentContext'
            ));
            
            // Remove X-Frame-Options to allow embedding
            $response->headers->remove('X-Frame-Options');
            
            // Set Content-Security-Policy to allow embedding
            // Allow embedding from any origin, but restrict script sources to same origin
            $response->headers->set('Content-Security-Policy', "frame-ancestors *; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
            
            // Allow CORS for API calls from iframe
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization');
            
            return $response;
        } catch (\Throwable $e) {
            Log::error('Writing embed error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($e->getCode() === 404) {
                abort(404, $e->getMessage());
            }
            
            abort(500, 'Unable to load question. Please try again later.');
        }
    }
}
