<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Attempt
 *
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property string|null $user_answer
 * @property string|null $user_audio_url
 * @property bool $is_correct
 * @property int|null $score
 * @property array|null $evaluation_meta
 * @property string|null $feedback
 * @property string $created_at
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereEvaluationMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereUserAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereUserAudioUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attempt whereUserId($value)
 */
	class Attempt extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Exercise
 *
 * @property int $id
 * @property int $type_id
 * @property int $lesson_id
 * @property string $title
 * @property string|null $instruction
 * @property string|null $difficulty
 * @property string|null $img_url
 * @property int $order_index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Lesson $lesson
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Models\ExerciseType $type
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereOrderIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Exercise withoutTrashed()
 */
	class Exercise extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExerciseType
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exercise> $exercises
 * @property-read int|null $exercises_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExerciseType withoutTrashed()
 */
	class ExerciseType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Lesson
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $img_url
 * @property string|null $level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exercise> $exercises
 * @property-read int|null $exercises_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson withoutTrashed()
 */
	class Lesson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $exercise_id
 * @property string|null $img_url
 * @property string|null $audio_url
 * @property int $order_index
 * @property string|null $target_text
 * @property string|null $starter_text
 * @property string|null $prompt_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read \App\Models\Exercise $exercise
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereAudioUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOrderIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question wherePromptText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereStarterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTargetText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Question withoutTrashed()
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $dob
 * @property string|null $avatar_url
 * @property string $password
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

