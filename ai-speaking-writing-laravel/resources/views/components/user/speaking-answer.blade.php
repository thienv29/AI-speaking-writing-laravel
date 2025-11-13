<div class="answer-wrapper">
    
    <div class="answer-label">🎙️ Bé hãy nhấn nút mic và đọc câu trả lời</div>
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
                <p id="question-prompt-text" class="text-xl font-semibold text-gray-800 break-all">
                    {{ $question->prompt_text }}
                </p>
                
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
    {{-- <div class="btn-area">
        <button class="btn-primary" id="submitBtn">
            Gửi câu trả lời
        </button>
        <button class="btn-secondary" type="button">
            Làm lại
        </button>
    </div> --}}
    <div class="loading" id="loading" style="display: none;">
        <div class="spinner"></div>
        <span>đang chấm bài của bạn...</span>
    </div>
    <div class="error" id="error">
        ❌ Lỗi: <span id="errorMessage"></span>
    </div>
</div>