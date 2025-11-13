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
    <div class="loading" id="loading" style="display: none;">
        <div class="spinner"></div>
        <span>đang chấm bài của bạn...</span>
    </div>
    <div class="error" id="error">
        ❌ Lỗi: <span id="errorMessage"></span>
    </div>
</div>