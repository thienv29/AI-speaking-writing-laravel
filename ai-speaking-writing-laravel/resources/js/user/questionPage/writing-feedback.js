const Feedback = {
    escapeHtml(str) {
        return (str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    renderHighlights(data) {
        const escapeHtml = this.escapeHtml; 
        const meta = data && data.evaluation_meta ? data.evaluation_meta : {};
        const segments = meta.highlight_segments || [];
        if (!segments.length) return '';

        return segments.map(segment => {
            const status = segment.status || 'correct';
            let className = 'hl-correct';
            if (status === 'wrong') className = 'hl-wrong';
            else if (status === 'neutral') className = 'hl-neutral';

            const title = segment.message ? ` title="${escapeHtml(segment.message)}"` : '';
            const text = segment.text || '';
            const safeText = escapeHtml(text).replace(/ /g, '&nbsp;');

            return `<span class="${className}"${title}>${safeText}</span>`;
        }).join('');
    },

    renderNotes(data) {
        const escapeHtml = this.escapeHtml;
        const meta = data && data.evaluation_meta ? data.evaluation_meta : {};
        const notes = meta.notes || [];
        if (!notes.length) return '';
        return `<ul class="hl-notes">${notes.map(note => `<li>${escapeHtml(note)}</li>`).join('')}</ul>`;
    },

    formatFeedbackForKids(markdownText) {
        const escapeHtml=this.escapeHtml;
        if (!markdownText) return '';

        let text = markdownText;
        text = escapeHtml(text);

        text = text.replace(/^### (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');
        text = text.replace(/^## (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');
        text = text.replace(/^# (.*$)/gim, '<p class="feedback-heading"><strong>$1</strong></p>');

        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/__(.+?)__/g, '<strong>$1</strong>');

        text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
        text = text.replace(/_(.+?)_/g, '<em>$1</em>');

        text = text.replace(/^[\s]*[-*+]\s+(.+)$/gim, '<p class="feedback-list-item">• $1</p>');
        text = text.replace(/^[\s]*\d+\.\s+(.+)$/gim, '<p class="feedback-list-item">$1</p>');

        text = text.split(/\n\s*\n/).map(para => {
            para = para.trim();
            if (!para) return '';
            if (!para.match(/^<[a-z]/i)) {
                return '<p class="feedback-paragraph">' + para + '</p>';
            }
            return para;
        }).join('');

        text = text.replace(/\n/g, '<br>');

        text = text.replace(/```[\s\S]*?```/g, '');
        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');

        text = text.replace(/<p[^>]*>\s*<\/p>/g, '');

        return text.trim();
    },
}

export default Feedback;


