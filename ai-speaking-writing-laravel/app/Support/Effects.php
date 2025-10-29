<?php

namespace App\Support;

use App\Models\Question;
use Illuminate\Support\Str;

class Effects
{
    public static function forTemplate(?string $template): array
    {
        $template = strtolower((string) $template);
        $config = config('effects.templates');

        if (isset($config[$template])) {
            return $config[$template];
        }

        return $config['generic'] ?? [];
    }

    public static function forExerciseType(?string $code): array
    {
        $code = strtoupper((string) $code);
        $config = config('effects.exercise_types');

        if (isset($config[$code])) {
            return $config[$code];
        }

        return $config['default'] ?? [];
    }

    public static function forQuestion(Question $question): array
    {
        $exercise = $question->relationLoaded('exercise') ? $question->exercise : null;
        $type = $exercise && $exercise->relationLoaded('type') ? $exercise->type : null;
        $typeCode = $type->code ?? null;

        $template = static::guessTemplateFromQuestion($question);

        return [
            'template' => $template,
            'effects' => static::forTemplate($template),
            'exercise' => static::forExerciseType($typeCode),
        ];
    }

    private static function guessTemplateFromQuestion(Question $question): ?string
    {
        $prompt = strtolower((string) $question->prompt_text);

        if (Str::contains($prompt, 'what is your name')) {
            return 'name';
        }

        if (Str::contains($prompt, 'how old') || Str::contains($prompt, 'years old')) {
            return 'age';
        }

        if (Str::contains($prompt, 'where do you live') || Str::contains($prompt, 'live in')) {
            return 'location';
        }

        if (Str::contains($prompt, 'favorite hobby')) {
            return 'hobby';
        }

        if (Str::contains($prompt, 'say hello') || Str::startsWith($prompt, 'hello')) {
            return 'greeting';
        }

        if (Str::contains($prompt, 'what time') || Str::contains($prompt, "o'clock")) {
            return 'time';
        }

        if (Str::contains($prompt, 'today is') || Str::contains($prompt, 'weather')) {
            return 'weather';
        }

        if ($prompt && strlen($prompt) < 50) {
            return 'word_usage';
        }

        return null;
    }
}
