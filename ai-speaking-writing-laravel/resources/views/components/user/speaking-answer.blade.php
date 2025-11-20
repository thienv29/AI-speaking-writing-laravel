<div class="answer-wrapper answer-wrapper--speaking">
    <div class="answer-label">🎙️ Bé hãy nhấn nút mic và đọc câu trả lời</div>
    <div class="speaking-layout">
        <div class="speaking-illustration">
            <img id="question-img"
                 src="{{ $question->img_url ?? 'https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg' }}"
                 data-default-src="https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg"
                 alt="Question Image">
        </div>

        <div class="speaking-content">
            <div class="speaking-prompt">
                <p id="question-prompt-text">{{ $question->prompt_text }}</p>
                <button id="sample-audio-btn"
                        type="button"
                        class="speaking-btn speaking-btn--audio"
                        style="display:inline-flex;align-items:center;gap:8px;padding:12px 18px;border-radius:999px;border:none;color:#fff;background:linear-gradient(135deg,#38bdf8,#2563eb);cursor:pointer;"
                        data-default-audio="{{ asset('assets/sounds/hello.mp3') }}">
                    <span role="img" aria-label="loa">🔊</span>
                    <span>Nghe đề</span>
                </button>
            </div>

            <div class="speaking-recorder">
                <audio controls id="user-audio" class="speaking-audio hidden"></audio>
                <div id="recording-indicator" class="speaking-indicator hidden"></div>
                <button id="mic-btn" type="button" class="speaking-btn speaking-btn--mic"
                        style="display:inline-flex;align-items:center;gap:8px;padding:14px 20px;border-radius:999px;border:none;color:#fff;background:linear-gradient(135deg,#f87171,#dc2626);cursor:pointer;">
                    <span role="img" aria-label="micro">🎤</span>
                    <span>Ghi âm</span>
                </button>
            </div>
        </div>
    </div>

    <div class="error" id="error">
        ❌ Lỗi: <span id="errorMessage"></span>
    </div>
</div>
