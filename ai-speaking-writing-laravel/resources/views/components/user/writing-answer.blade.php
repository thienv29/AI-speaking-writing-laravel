<div class="answer-wrapper">
    <div class="answer-label">✍️ Bé hãy viết câu trả lời</div>
    <div class="answer-input-group" id="wcsInputGroup" style="display: none;">
        <div class="answer-prefix" id="wcsPrefix"></div>
        <div class="answer-suffix-container">
            <textarea
                id="answerSuffix"
                class="answer-suffix-input"
                rows="2"
                placeholder="..."
                autocomplete="off"
            ></textarea>
        </div>
    </div>
    <textarea 
        id="userAnswer" 
        placeholder="Viết câu trả lời của con tại đây..."
        rows="5"
    ></textarea>
    <div class="btn-area">
        <button class="btn-primary" id="submitBtn" data-loading-text="Đang chấm bài...">
            Gửi câu trả lời
        </button>
        <button class="btn-secondary" type="button" id="resetBtn">
            Làm lại
        </button>
    </div>
    <div class="bottom-navigation">
        <button id="prevQuestionBtn" class="nav-arrow-btn" type="button" title="Câu trước">
            <span class="nav-arrow-icon">←</span>
            <span class="nav-arrow-text">Câu trước</span>
        </button>
        <span class="nav-counter" id="questionCounter"></span>
        <button id="nextQuestionBtn" class="nav-arrow-btn" type="button" title="Câu sau">
            <span class="nav-arrow-text">Câu sau</span>
            <span class="nav-arrow-icon">→</span>
        </button>
    </div>
    <div class="error" id="error">
        ❌ Lỗi: <span id="errorMessage"></span>
    </div>
</div>