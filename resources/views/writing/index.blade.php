@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Writing Exercises</h1>
            <p class="mt-2 text-lg text-gray-600">Practice your English writing skills with interactive exercises</p>
        </div>

        <!-- Writing Types -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Exercise Types</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Q&A Writing</h3>
                            <p class="text-sm text-gray-500">Answer questions in complete sentences</p>
                        </div>
                    </div>
                    <a href="#" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                        Start Practice
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Sentence Building</h3>
                            <p class="text-sm text-gray-500">Build sentences from given words</p>
                        </div>
                    </div>
                    <a href="#" class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition duration-150 ease-in-out">
                        Start Practice
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Complete Sentence</h3>
                            <p class="text-sm text-gray-500">Complete sentences with missing words</p>
                        </div>
                    </div>
                    <a href="#" class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded-md hover:bg-purple-700 transition duration-150 ease-in-out">
                        Start Practice
                    </a>
                </div>
            </div>
        </div>

        <!-- Lessons with Writing Exercises -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Lessons with Writing Exercises</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($lessons as $lesson)
                    @if($lesson->exercises->count() > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">{{ $loop->iteration }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $lesson->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $lesson->level }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 mb-4">{{ $lesson->description }}</p>
                                
                                <div class="mb-4">
                                    <span class="text-sm text-gray-500">{{ $lesson->exercises->count() }} writing exercises</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($lesson->level === 'Easy') bg-green-100 text-green-800
                                        @elseif($lesson->level === 'Medium') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $lesson->level }}
                                    </span>
                                    <a href="{{ route('lessons.show', $lesson->id) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out">
                                        Start Writing
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No writing exercises available</h3>
                        <p class="mt-1 text-sm text-gray-500">Writing exercises will appear here when available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

