<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GrammarCheckerService;
use App\Services\AttemptService;
use App\Services\TemplateValidatorService;

echo "=== TESTING LANGUAGE TOOL API WITH 15 ADVANCED TEST CASES ===\n\n";

$grammarChecker = new GrammarCheckerService();
$service = new AttemptService(new TemplateValidatorService());

// 15 test cases with various grammar errors
$testCases = [
    // 1. Wrong tense - past tense instead of present
    'I went to the store yesterday and buy some apples.' => 'Tense error',
    
    // 2. Wrong preposition
    'She is afraid from spiders and snakes.' => 'Wrong preposition (afraid of)',
    
    // 3. Missing article
    'He is good student and he study hard every day.' => 'Missing articles',
    
    // 4. Wrong word order
    'Yesterday I went to park with my friends happy.' => 'Word order error',
    
    // 5. Double negative
    'I don\'t have no time to finish this homework.' => 'Double negative',
    
    // 6. Wrong conditional form
    'If I will have time, I will visit you tomorrow.' => 'Wrong conditional (if + will)',
    
    // 7. Wrong gerund/infinitive
    'I enjoy to play basketball with my friends.' => 'Wrong form (enjoy + gerund)',
    
    // 8. Missing subject/verb agreement (plural)
    'The books on the table is very interesting.' => 'Subject-verb agreement (plural)',
    
    // 9. Wrong past participle
    'I have went to the library three times this week.' => 'Wrong past participle (gone)',
    
    // 10. Wrong modal verb usage
    'You should to study harder if you want to pass the exam.' => 'Wrong modal (should + to)',
    
    // 11. Missing relative pronoun
    'The book I bought yesterday is very interesting.' => 'Missing relative pronoun (that/which)',
    
    // 12. Wrong comparison
    'This movie is more better than the previous one.' => 'Wrong comparison (more better)',
    
    // 13. Wrong passive voice
    'The homework was did by me yesterday.' => 'Wrong passive voice (was done)',
    
    // 14. Wrong possessive
    'These are my friend\'s books and they are very old.' => 'Plural possessive (friends\')',
    
    // 15. Run-on sentence / comma splice
    'I love sunny weather, it makes me feel happy all day.' => 'Comma splice / run-on',
];

$questionId = 13; // Assuming question ID 13 exists

echo "Testing each sentence with LanguageTool API and full evaluation:\n\n";

foreach ($testCases as $sentence => $errorType) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 Sentence: \"$sentence\"\n";
    echo "🎯 Expected Error Type: $errorType\n\n";
    
    // Test LanguageTool API directly
    echo "🔍 LanguageTool API Result:\n";
    $apiErrors = $grammarChecker->checkGrammar($sentence);
    if (empty($apiErrors)) {
        echo "   ❌ No errors detected by API\n";
    } else {
        echo "   ✅ Errors detected:\n";
        foreach ($apiErrors as $error) {
            echo "      - $error\n";
        }
    }
    echo "\n";
    
    // Test full evaluation
    echo "📊 Full Evaluation Result:\n";
    try {
        $result = $service->evaluateWritingAttempt($questionId, 1, $sentence);
        echo "   Score: {$result->score}/100\n";
        echo "   Is Correct: " . ($result->is_correct ? 'Yes ✅' : 'No ❌') . "\n";
        echo "   Feedback: " . substr($result->feedback, 0, 100) . "...\n";
    } catch (\Exception $e) {
        echo "   ⚠️  Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Small delay to avoid rate limiting
    sleep(1);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Testing completed!\n";

