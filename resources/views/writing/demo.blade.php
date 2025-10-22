@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-900">Writing Exercises Demo</h1>
            <p class="mt-4 text-xl text-gray-600">Tất cả các dạng bài tập writing có sẵn trong hệ thống</p>
        </div>

        <!-- Exercise Types Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Writing - Answer the question -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-blue-200">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Q&A Writing</h3>
                    <p class="text-gray-600">Trả lời câu hỏi bằng câu hoàn chỉnh</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Ví dụ:</strong></p>
                        <p class="text-gray-600">"Where do you live?"</p>
                        <p class="text-gray-600">"How old are you?"</p>
                    </div>
                    <a href="{{ route('writing.exercise', ['lesson' => 1, 'exercise' => 4]) }}" 
                       class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                        Thử ngay
                    </a>
                </div>
            </div>

            <!-- Writing - Complete the sentence -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-green-200">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Complete Sentence</h3>
                    <p class="text-gray-600">Hoàn thành câu với từ còn thiếu</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Ví dụ:</strong></p>
                        <p class="text-gray-600">"I am _____"</p>
                        <p class="text-gray-600">"My name _____"</p>
                    </div>
                    <a href="{{ route('writing.exercise', ['lesson' => 1, 'exercise' => 3]) }}" 
                       class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition duration-150 ease-in-out">
                        Thử ngay
                    </a>
                </div>
            </div>

            <!-- Writing - Sentence Building -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-purple-200">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Sentence Building</h3>
                    <p class="text-gray-600">Sắp xếp các từ thành câu hoàn chỉnh</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Ví dụ:</strong></p>
                        <p class="text-gray-600">"morning, I, up, wake, at, 7, every, AM"</p>
                        <p class="text-gray-600">→ "I wake up at 7 AM every morning"</p>
                    </div>
                    <a href="{{ route('writing.exercise', ['lesson' => 2, 'exercise' => 5]) }}" 
                       class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded-md hover:bg-purple-700 transition duration-150 ease-in-out">
                        Thử ngay
                    </a>
                </div>
            </div>

            <!-- Writing - Paragraph Writing -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-orange-200">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Paragraph Writing</h3>
                    <p class="text-gray-600">Viết đoạn văn ngắn về chủ đề</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Ví dụ:</strong></p>
                        <p class="text-gray-600">Viết về cách bảo vệ môi trường</p>
                        <p class="text-gray-600">Sử dụng: environment, protect, trees, recycle</p>
                    </div>
                    <a href="{{ route('writing.exercise', ['lesson' => 3, 'exercise' => 6]) }}" 
                       class="block w-full bg-orange-600 text-white text-center py-2 px-4 rounded-md hover:bg-orange-700 transition duration-150 ease-in-out">
                        Thử ngay
                    </a>
                </div>
            </div>

            <!-- Writing - Write sentence using given word -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-pink-200">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Word-based Writing</h3>
                    <p class="text-gray-600">Viết câu sử dụng từ cho sẵn</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Ví dụ:</strong></p>
                        <p class="text-gray-600">Từ: "beautiful"</p>
                        <p class="text-gray-600">→ "The sunset is beautiful"</p>
                    </div>
                    <a href="#" 
                       class="block w-full bg-pink-600 text-white text-center py-2 px-4 rounded-md hover:bg-pink-700 transition duration-150 ease-in-out">
                        Sắp có
                    </a>
                </div>
            </div>

            <!-- Coming Soon -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-gray-200 opacity-75">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">More Coming Soon</h3>
                    <p class="text-gray-600">Nhiều dạng bài tập khác</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700"><strong>Dự kiến:</strong></p>
                        <p class="text-gray-600">• Story Writing</p>
                        <p class="text-gray-600">• Essay Writing</p>
                        <p class="text-gray-600">• Creative Writing</p>
                    </div>
                    <button disabled 
                            class="block w-full bg-gray-400 text-white text-center py-2 px-4 rounded-md cursor-not-allowed">
                        Sắp có
                    </button>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tính năng nổi bật</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Tương tác nhanh</h3>
                    <p class="text-sm text-gray-600">Submit answers và nhận feedback ngay lập tức</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Theo dõi tiến độ</h3>
                    <p class="text-sm text-gray-600">Progress bar và điểm số real-time</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Mobile-friendly</h3>
                    <p class="text-sm text-gray-600">Hoạt động tốt trên mọi thiết bị</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Sẵn sàng bắt đầu học viết?</h2>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('writing.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Bắt đầu học
                </a>
                <a href="{{ route('lessons.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Xem tất cả bài học
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

