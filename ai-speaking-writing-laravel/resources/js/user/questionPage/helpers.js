// Helper functions
import confetti from 'canvas-confetti';

export function getExerciseType(question) {
    const code = (question?.exercise?.type?.code || '').toString().toUpperCase();
    return {
        code,
        isWriting: code.startsWith('W'),
        isSpeaking: code.startsWith('S'),
        isWcs: code === 'WCS',
    };
}

export function clearUserAudio(userAudio) {
    if (userAudio) {
        userAudio.src = '';
        userAudio.style.display = 'none';
    }
}

export function setLoadingState(isLoading, submitBtn, loadingEl, errorEl, loadingPopup = null) {
    if (submitBtn) {
        if (isLoading) {
            if (!submitBtn.dataset.originalContent) {
                submitBtn.dataset.originalContent = submitBtn.innerHTML;
            }
            const loadingText = submitBtn.dataset.loadingText || 'Đang chấm bài...';
            submitBtn.innerHTML = `<span class="btn-loader"></span><span>${loadingText}</span>`;
            submitBtn.classList.add('is-loading');
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('is-loading');
            submitBtn.innerHTML = submitBtn.dataset.originalContent || 'Gửi câu trả lời';
        }
    }
    if (loadingEl) loadingEl.style.display = isLoading ? 'flex' : 'none';
    if (errorEl && isLoading) errorEl.style.display = 'none';
    // Show/hide loading popup
    if (loadingPopup) {
        if (isLoading) {
            loadingPopup.classList.add('show');
        } else {
            loadingPopup.classList.remove('show');
        }
    }
}

export function triggerSuccessEffect(score = null) {
    try {
        const intensity = score && Number.isFinite(score) ? Math.min(Math.max(score, 50), 100) : 90;
        confetti({
            particleCount: Math.round(intensity),
            startVelocity: 35,
            spread: 65,
            origin: { y: 0.6 },
            scalar: 0.8,
        });
    } catch (error) {
        console.warn('[questionPage] Không thể chạy hiệu ứng confetti', error);
    }
}

export function sanitizeFeedbackMessage(feedback, answer = '') {
    if (!feedback) return feedback;
    const trimmedAnswer = (answer || '').trim();
    if (!trimmedAnswer) return feedback;

    const endsWithPunctuation = /[.!?…]+$/.test(trimmedAnswer);
    if (!endsWithPunctuation) return feedback;

    return feedback.replace(/Lần sau con nhớ thêm dấu chấm cuối câu nhé\.*\s*/gi, '').trim();
}

export function extractScoreFromFeedback(feedback) {
    if (!feedback) return null;
    const scoreMatch = feedback.match(/Điểm:\s*(\d+)\s*\/\s*100/i);
    if (scoreMatch && scoreMatch[1]) {
        const parsed = Number.parseInt(scoreMatch[1], 10);
        return Number.isNaN(parsed) ? null : parsed;
    }
    return null;
}

export const toNumber = (value) => {
    if (value === null || value === undefined || value === '') return null;
    const num = Number(value);
    return Number.isNaN(num) ? null : num;
};

