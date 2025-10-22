@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $exercise->title }}</h1>
                    <p class="mt-2 text-lg text-gray-600">{{ $lesson->title }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $exercise->type->name }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $exercise->difficulty ?? 'Medium' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Exercise Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-8">
                <!-- Instructions -->
                @if($exercise->instruction)
                    <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-medium text-blue-900 mb-2">Instructions</h3>
                        <p class="text-blue-800">{{ $exercise->instruction }}</p>
                    </div>
                @endif

                <!-- Questions -->
                <div class="space-y-6">
                    @foreach($exercise->questions as $index => $question)
                        <div class="border border-gray-200 rounded-lg p-6" id="question-{{ $question->id }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <h4 class="ml-3 text-lg font-medium text-gray-900">Question {{ $index + 1 }}</h4>
                                </div>
                                @if($question->img_url)
                                    <img src="{{ $question->img_url }}" alt="Question image" class="w-20 h-20 object-cover rounded-lg">
                                @endif
                            </div>

                            <!-- Question Content -->
                            <div class="mb-6">
                                @if($question->prompt_text)
                                    <p class="text-lg text-gray-700 mb-4">{{ $question->prompt_text }}</p>
                                @endif
                                
                                @if($question->starter_text)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-gray-700 font-medium">{{ $question->starter_text }}</p>
                                    </div>
                                @endif
                                
                                @if($question->target_text)
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-700">
                                            <strong>💡 Expected Answer:</strong> {{ $question->target_text }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Answer Input -->
                            <div class="space-y-4">
                                @if($exercise->type->name === 'Writing - Complete the sentence')
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-700">{{ $question->starter_text ?? '' }}</span>
                                        <input type="text" 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Type your answer here..."
                                               id="answer-{{ $question->id }}">
                                    </div>
                                @else
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                              rows="4" 
                                              placeholder="Type your answer here..."
                                              id="answer-{{ $question->id }}"></textarea>
                                @endif
                            </div>

                            <!-- Submit Buttons -->
                            <div class="mt-4 space-y-2">
                                <div class="flex space-x-3">
                <button onclick="submitAnswer({{ $question->id }}, {{ $exercise->id }}, 'auto')" 
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Auto Score
                </button>
                                    <button onclick="submitAnswer({{ $question->id }}, {{ $exercise->id }}, 'manual')" 
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Teacher Review
                                    </button>
                                </div>
            <div class="text-xs text-gray-500 text-center space-y-1">
                <p><strong>Auto Score:</strong> Smart scoring system | 
                <strong>Teacher Review:</strong> Manual grading by teacher</p>
                <p class="text-blue-600"><strong>💡 Demo Tips:</strong> Try different answer lengths and grammar for varied scores!</p>
            </div>
                            </div>

                            <!-- Result Area -->
                            <div id="result-{{ $question->id }}" class="mt-4 hidden">
                                <!-- Result will be displayed here -->
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Progress -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm text-gray-500">0 / {{ $exercise->questions->count() }} completed</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 0%" id="progress-bar"></div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex justify-between">
                    <a href="{{ route('writing.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Writing
                    </a>
                    
                    <button onclick="completeExercise()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        Complete Exercise
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let completedQuestions = 0;
const totalQuestions = {{ $exercise->questions->count() }};

function submitAnswer(questionId, exerciseId, scoringType) {
    const answer = document.getElementById('answer-' + questionId).value;
    
    if (!answer.trim()) {
        alert('Please enter an answer before submitting.');
        return;
    }

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Submitting...';
    button.disabled = true;

    // Submit answer
    fetch('/writing/submit-answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_id: questionId,
            exercise_id: exerciseId,
            answer: answer,
            user_id: 1, // TODO: Get from authentication
            scoring_type: scoringType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResult(questionId, data.data, scoringType);
            completedQuestions++;
            updateProgress();
        } else {
            alert('Error submitting answer: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting answer. Please try again.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showResult(questionId, data, scoringType) {
    const resultDiv = document.getElementById('result-' + questionId);
    
            if (scoringType === 'auto') {
                resultDiv.innerHTML = `
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 font-medium">Auto-scored successfully! 🎯</span>
                            <span class="text-xs text-green-600 ml-2">(Smart scoring system)</span>
                        </div>
                        <p class="mt-2 text-green-700"><strong>Score:</strong> ${data.score}%</p>
                        ${data.feedback ? `<div class="mt-2"><strong>Feedback:</strong><ul class="list-disc list-inside text-sm text-green-700">${data.feedback.map(f => `<li>${f}</li>`).join('')}</ul></div>` : ''}
                        ${data.suggestions && data.suggestions.length > 0 ? `<div class="mt-2"><strong>Suggestions:</strong><ul class="list-disc list-inside text-sm text-blue-700">${data.suggestions.map(s => `<li>${s}</li>`).join('')}</ul></div>` : ''}
                    </div>
                `;
    } else {
        resultDiv.innerHTML = `
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-blue-800 font-medium">Submitted for teacher review!</span>
                </div>
                <p class="mt-2 text-blue-700">Your answer has been sent to your teacher for manual grading.</p>
                <p class="mt-1 text-sm text-blue-600">You will receive feedback once your teacher reviews your work.</p>
            </div>
        `;
    }
    
    resultDiv.classList.remove('hidden');
}

function updateProgress() {
    const progress = (completedQuestions / totalQuestions) * 100;
    document.getElementById('progress-bar').style.width = progress + '%';
    
    const progressText = document.querySelector('.text-gray-500');
    progressText.textContent = `${completedQuestions} / ${totalQuestions} completed`;
}

function completeExercise() {
    if (completedQuestions < totalQuestions) {
        if (confirm('You haven\'t completed all questions. Are you sure you want to finish?')) {
            window.location.href = '/writing';
        }
    } else {
        alert('Congratulations! You have completed all questions.');
        window.location.href = '/writing';
    }
}
</script>
@endsection
