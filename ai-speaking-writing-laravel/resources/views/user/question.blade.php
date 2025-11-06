@extends('layouts.main')

@section('title', 'Question - AI Speaking - Writing Laravel')

@section('content')
<div class="bg-[#f9f5fc] rounded shadow-lg p-6 max-w-[800px] mx-auto">

    <div id="exercise-title"class="flex text-xl font-semibold mb-4">Bài tập</div>
    {{-- Hai mũi tên chuyển câu hỏi --}}
    <div class="flex flex-row justify-between items-center">
        <a id="prev-question-btn"
        class="cursor-pointer bg-gray-100 hover:bg-gray-200 p-3 rounded-full shadow opacity-0 pointer-events-none">
            <i class="bi bi-arrow-left"></i>
        </a>

        <span id="question-counter" class="flex-1 align-center text-xl text-center"></span>

        <a id="next-question-btn"
        class="cursor-pointer bg-gray-100 hover:bg-gray-200 p-3 rounded-full shadow opacity-0 pointer-events-none">
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    {{-- Nội dung chính của question --}}
    <div class="flex flex-col md:flex-row gap-6 mt-6">

        {{-- Hình ảnh question --}}
        <div class="w-full md:w-1/3 flex-1">
            <img id="question-img" src="https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg"
                alt="Question Image" class="max-h-[250px] rounded-lg shadow-md w-full object-cover">
        </div>

        {{-- Phần nội dung question --}}
        <div class="flex-1 flex flex-col justify-between items-stretch">

            {{-- Text và icon loa --}}
            <div class="flex justify-between">
                <p id="question-prompt-text" class="text-xl font-semibold text-gray-800 break-all"></p>
                
                <button id="sample-audio-btn" class="bg-blue-300 hover:bg-blue-400 px-6 py-4 rounded-full">
                    <i class="bi bi-volume-up"></i>
                </button>
            </div>

            {{-- Icon micro ở giữa cột nội dung --}}
            <div class="flex flex-col items-center justify-center mt-6 gap-4">
                <audio controls id="user-audio" class="w-full hidden"></audio>
                <div id="recording-indicator"
                    class="hidden p-4 inset-0 rounded-full border-4 border-red-400 border-t-transparent animate-spin">
                </div>
                <button id="mic-btn" 
                class="bg-red-500 hover:bg-red-600 px-6 py-4 rounded-full text-white shadow-lg">
                    <i class="bi bi-mic"></i>
                </button>
            </div>
            <audio id="playback" class="hidden" controls></audio>
        </div>
    </div>

    <!-- Kết quả -->
    <div id="answer-box" class="flex flex-col mt-4 hidden w-full justify-center items-center transition-all duration-500 ease-in-out">
        <div id="answer-text"></div>
    </div>
</div>

<script>
    window.appData = {
        questionId: {{ $id }}
    };
</script>

@endsection
