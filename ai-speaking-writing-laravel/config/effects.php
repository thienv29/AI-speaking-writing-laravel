<?php

return [
    'templates' => [
        'name' => [
            'success' => [
                'sound' => '/assets/sounds/success-chime.mp3',
                'animation' => 'confetti',
                'message' => 'Great introduction!'
            ],
            'failure' => [
                'sound' => '/assets/sounds/try-again.mp3',
                'animation' => 'shake',
                'message' => 'Try saying “My name is …”'
            ],
        ],
        'age' => [
            'success' => [
                'sound' => '/assets/sounds/level-up.mp3',
                'animation' => 'sparkle',
                'message' => 'Nice! You told your age correctly.'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'wobble',
                'message' => 'Remember to use numbers for your age.'
            ],
        ],
        'location' => [
            'success' => [
                'sound' => '/assets/sounds/location-success.mp3',
                'animation' => 'pulse',
                'message' => 'Awesome! You shared where you live.'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'wobble',
                'message' => 'Use the format “I live in …”.'
            ],
        ],
        'hobby' => [
            'success' => [
                'sound' => '/assets/sounds/achievement.mp3',
                'animation' => 'bounce',
                'message' => 'Great! That hobby sounds fun!'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'shake',
                'message' => 'Try “My favorite hobby is …”.'
            ],
        ],
        'greeting' => [
            'success' => [
                'sound' => '/assets/sounds/hello.mp3',
                'animation' => 'wave',
                'message' => 'Hello there! Nice greeting.'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'shake',
                'message' => 'Start with “Hello,” before the name.'
            ],
        ],
        'time' => [
            'success' => [
                'sound' => '/assets/sounds/clock-success.mp3',
                'animation' => 'tick',
                'message' => 'Tick tock! Time is correct.'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'shake',
                'message' => 'Remember “It is [number] o’clock.”'
            ],
        ],
        'weather' => [
            'success' => [
                'sound' => '/assets/sounds/weather-success.mp3',
                'animation' => 'rainbow',
                'message' => 'Lovely weather description!'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'shake',
                'message' => 'Use words like sunny, rainy, cloudy…'
            ],
        ],
        'word_usage' => [
            'success' => [
                'sound' => '/assets/sounds/pop.mp3',
                'animation' => 'bounce',
                'message' => 'Nice sentence!'
            ],
            'failure' => [
                'sound' => '/assets/sounds/soft-error.mp3',
                'animation' => 'shake',
                'message' => 'Write a full sentence using the word.'
            ],
        ],
        'generic' => [
            'success' => [
                'sound' => '/assets/sounds/success-generic.mp3',
                'animation' => 'confetti',
                'message' => 'Good job!'
            ],
            'failure' => [
                'sound' => '/assets/sounds/try-again.mp3',
                'animation' => 'shake',
                'message' => 'Give it another try!'
            ],
        ],
    ],

    'exercise_types' => [
        'WAQ' => [
            'button_sound' => '/assets/sounds/click-soft.mp3',
            'background' => 'stars',
        ],
        'WCS' => [
            'button_sound' => '/assets/sounds/click-sparkle.mp3',
            'background' => 'notebook',
        ],
        'WSG' => [
            'button_sound' => '/assets/sounds/click-pop.mp3',
            'background' => 'word-cloud',
        ],
        'default' => [
            'button_sound' => '/assets/sounds/click-soft.mp3',
            'background' => 'default',
        ],
    ],
];
