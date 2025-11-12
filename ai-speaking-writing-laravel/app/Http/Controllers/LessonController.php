<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $lessons = Lesson::with(['exercises' => function ($q) {
            $q->withCount('questions'); 
        }])
        ->get()
        ->map(function ($lesson) {
            $lesson->exercises_count = $lesson->exercises->count();
            $lesson->questions_count = $lesson->exercises->sum('questions_count');
            return $lesson;
        });
        
        return response()->json($lessons);
    }

    public function indexWeb() {
        $lessons = Lesson::with(['exercises' => function ($q) {
            $q->withCount('questions'); 
        }])
        ->get()
        ->map(function ($lesson) {
            $lesson->exercises_count = $lesson->exercises->count();
            $lesson->questions_count = $lesson->exercises->sum('questions_count');
            return $lesson;
        });
        
        return view('pages.user.lessons', compact('lessons'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255', 'unique:lessons,title'],
                'description' => ['nullable', 'string'],
                'img_url' => ['nullable', 'url', 'max:2048'],
                'level' => ['required', 'string', 'max:255']
            ],
            [
                'title.required' => 'The title is required.',
                'title.string' => 'The title must be a string.',
                'title.max' => 'The title may not be greater than 255 characters.',
                'title.unique' => 'The title has already been taken.',

                'description.string'=> 'The description must be a string.',

                'img_url.url' => 'The image URL must be a valid URL.',
                'img_url.max' => 'The image URL may not be greater than 2048 characters.',
                
                'level.required' => 'The level is required.',
                'level.string' => 'The level must be a string.',
                'level.max' => 'The level may not be greater than 255 characters.'
            ]);

            $validated['title'] = trim($validated['title']);
            if (array_key_exists('description', $validated) && !is_null($validated['description'])) {
                $validated['description'] = trim($validated['description']);
            }
            if (array_key_exists('img_url', $validated) && !is_null($validated['img_url'])) {
                $validated['img_url'] = trim($validated['img_url']);
            }
            $validated['level'] = trim($validated['level']);

            $lesson = Lesson::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Lesson created successfully.',
                'data'   => $lesson
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Lesson store error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot create lesson: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(Lesson $lesson)
    {
        try {
            $lesson->load([
                'exercises' => function ($q) {
                    $q->select('id', 'lesson_id','type_id', 'title', 'instruction', 'difficulty', 'order_index')
                    ->orderBy('order_index')
                    ->with([
                        'type:id,name,code',
                        'questions' => function ($q2) {
                            $q2->select('id', 'exercise_id', 'prompt_text', 'order_index')
                                ->orderBy('order_index');
                        },
                    ])
                    ->withCount('questions'); 
                },
            ])
            ->loadCount([
                'exercises', 
                'questions', 
            ]);

            return response()->json([
                'status' => 'success',
                'data'   => $lesson,
            ]);
        } catch (\Throwable $e) {
            Log::error('Lesson show error', ['id' => $lesson->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch lesson: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showWeb($id) {
        try {
            $lesson = Lesson::with([
                'exercises' => function ($q) {
                    $q->select('id', 'lesson_id','type_id', 'title', 'instruction', 'difficulty', 'order_index')
                    ->orderBy('order_index')
                    ->with([
                        'type:id,name,code',
                        'questions' => function ($q2) {
                            $q2->select('id', 'exercise_id', 'prompt_text', 'order_index')
                                ->orderBy('order_index');
                        },
                    ])
                    ->withCount('questions'); 
                },
            ])
            ->findOrFail($id); 

            $lesson->loadCount(['exercises','questions']);

            return view('pages.user.lesson', compact('lesson'));
        } catch (\Throwable $e) {
            Log::error('Lesson show error', ['id' => $lesson->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch lesson: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Lesson $lesson)
    {
        try {
            $input = $request->all();
            if ($request->has('title')) {
                $input['title'] = trim((string) $request->input('title'));
            }
            if ($request->has('description')) {
                $input['description'] = trim((string) $request->input('description'));
            }
            if ($request->has('img_url')) {
                $input['img_url'] = trim((string) $request->input('img_url'));
            }
            if ($request->has('level')) {
                $input['level'] = trim((string) $request->input('level'));
            }

            $validated = validator(
                $input,
                [
                    'title' => ['sometimes', 'required', 'string', 'max:255',
                        Rule::unique('lessons','title')->ignore($lesson->id),
                    ],
                    'description' => ['sometimes','nullable','string','max:255'],
                    'img_url' => ['sometimes','nullable','url','max:2048'],
                    'level' => ['sometimes', 'required', 'string', 'max:255'],
                ],
                [
                    'title.required'     => 'The lesson name is required.',
                    'title.string'       => 'The lesson name must be a string.',
                    'title.max'          => 'The lesson name may not be greater than 255 characters.',
                    'title.unique'       => 'The lesson name has already been taken.',

                    'description.string'       => 'The lesson description must be a string.',
                    'description.max'          => 'The lesson description may not be greater than 255 characters.',

                    'img_url.url'       => 'The lesson image URL must be a valid URL.',
                    'img_url.max'          => 'The lesson image URL may not be greater than 2048 characters.',

                    'level.required'     => 'The lesson level is required.',
                    'level.string'       => 'The lesson level must be a string.',
                    'level.max'          => 'The lesson level may not be greater than 255 characters.',
                ]
            )->validate();

            if (empty($validated)) {
                return response()->json([
                    'status'  => 'fail',
                    'message' => 'No fields to update.',
                ], 422);
            }

            $lesson->fill($validated)->save();

            $lesson->load([
                'exercises',
            ])->loadCount(['exercises']);

            return response()->json([
                'status' => 'success',
                'data'   => $lesson,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('ExerciseType update error', ['id' => $exerciseType->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot update exercise type: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function destroy(Lesson $lesson)
    {
        try {
            if ($lesson->exercises()->exists()) {
                $lesson->delete(); 

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Lesson has related exercises → soft deleted (marked deleted_at).',
                ]);
            }

            $lesson->forceDelete(); 

            return response()->json([
                'status'  => 'success',
                'message' => 'Lesson had no related exercises → permanently deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Lesson destroy error', [
                'id' => $lesson->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete lesson: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $lesson = Lesson::withTrashed()->findOrFail($id);

            if ($lesson->trashed()) {
                $lesson->restore();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Lesson restored successfully.',
                ]);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'Lesson is already active (not deleted).',
            ]);
        } catch (\Throwable $e) {
            Log::error('Lesson restore error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot restore lesson: ' . $e->getMessage(),
            ], 500);
        }
    }
}
