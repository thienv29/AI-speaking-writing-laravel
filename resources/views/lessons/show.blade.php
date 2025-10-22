@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
                    <p class="mt-2 text-lg text-gray-600">{{ $lesson->description }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($lesson->level === 'Easy') bg-green-100 text-green-800
                        @elseif($lesson->level === 'Medium') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $lesson->level }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Lesson Info -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $lesson->exercises->count() }}</div>
                        <div class="text-sm text-gray-600">Total Exercises</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $lesson->exercises->where('type.name', 'like', '%Writing%')->count() }}</div>
                        <div class="text-sm text-gray-600">Writing Exercises</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $lesson->exercises->where('type.name', 'like', '%Speaking%')->count() }}</div>
                        <div class="text-sm text-gray-600">Speaking Exercises</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercises -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Exercises in this Lesson</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($lesson->exercises as $exercise)
                    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ $loop->iteration }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $exercise->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $exercise->type->name }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($exercise->difficulty === 'Dễ') bg-green-100 text-green-800
                                    @elseif($exercise->difficulty === 'Trung bình') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $exercise->difficulty ?? 'Medium' }}
                                </span>
                            </div>
                            
                            @if($exercise->instruction)
                                <p class="text-gray-600 mb-4">{{ $exercise->instruction }}</p>
                            @endif
                            
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">{{ $exercise->questions->count() }} questions</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                @if(str_contains($exercise->type->name, 'Writing'))
                                    <a href="{{ route('writing.exercise', ['lesson' => $lesson->id, 'exercise' => $exercise->id]) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Start Writing
                                    </a>
                                @else
                                    <a href="#" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                        </svg>
                                        Start Speaking
                                    </a>
                                @endif
                                
                                <span class="text-xs text-gray-400">{{ $exercise->type->code }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No exercises available</h3>
                        <p class="mt-1 text-sm text-gray-500">Exercises will appear here when available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between">
            <a href="{{ route('lessons.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Lessons
            </a>
            
            <a href="{{ route('writing.demo') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                View Writing Demo
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection

