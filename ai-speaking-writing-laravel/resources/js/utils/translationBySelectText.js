// TRANSLATION FEATURE
let translationPopup = null;
let translationTimeout = null;

function initTranslationFeature() {
    translationPopup = document.createElement('div');
    translationPopup.id = 'translation-popup';
    translationPopup.className = 'translation-popup';
    document.body.appendChild(translationPopup);
    
    document.addEventListener('mouseup', handleTextSelection);
    document.addEventListener('touchend', handleTextSelection);
    
    document.addEventListener('click', function(e) {
        if (!translationPopup.contains(e.target)) {
            hideTranslationPopup();
        }
    });
}

function handleTextSelection(e) {
    const selection = window.getSelection();
    const selectedText = selection.toString().trim();
    
    if (selectedText.length === 0) {
        hideTranslationPopup();
        return;
    }
    
    const activeElement = document.activeElement;
    if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
        hideTranslationPopup();
        return;
    }
    
    const cleanedText = selectedText.replace(/[.,!?;:]/g, '');
    if (!/^[a-zA-Z\s'-]+$/.test(cleanedText)) {
        hideTranslationPopup();
        return;
    }
    
    clearTimeout(translationTimeout);
    translationTimeout = setTimeout(() => {
        translateText(selectedText, e);
    }, 300);
}

function translateText(text, event) {
    if (!text || text.length === 0) return;
    
    showTranslationPopup('Đang dịch...', event);
    
    fetch('/api/translate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ text: text })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'API Error');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.translation) {
            showTranslationPopup(data.translation, event, text);
        } else {
            showTranslationPopup('Không tìm thấy bản dịch', event);
        }
    })
    .catch(error => {
        showTranslationPopup('Lỗi khi dịch: ' + error.message, event);
    });
}

function showTranslationPopup(translation, event, originalText = '') {
    if (!translationPopup) return;
    
    translationPopup.innerHTML = `
        ${originalText ? `<div class="translation-original">${escapeHtml(originalText)}</div>` : ''}
        <div class="translation-text">${escapeHtml(translation)}</div>
    `;
    translationPopup.style.display = 'block';
    
    const x = event?.clientX || window.innerWidth / 2;
    const y = event?.clientY || window.innerHeight / 2;
    
    translationPopup.style.left = Math.min(x + 20, window.innerWidth - 280) + 'px';
    translationPopup.style.top = Math.min(y + 20, window.innerHeight - 120) + 'px';
    
    setTimeout(() => hideTranslationPopup(), 5000);
}

function hideTranslationPopup() {
    if (translationPopup) {
        translationPopup.style.display = 'none';
    }
}