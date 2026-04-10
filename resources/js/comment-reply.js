document.addEventListener('click', (e) => {
    const replyBtn = e.target.closest('.reply-btn');
    if (replyBtn) {
        const parentId = replyBtn.dataset.replyTo;
        const authorName = replyBtn.dataset.replyAuthor;

        document.getElementById('comment-parent-id').value = parentId;
        document.getElementById('reply-author-name').textContent = authorName;
        document.getElementById('reply-banner').classList.remove('hidden');

        const form = document.getElementById('comment-form');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            form.querySelector('textarea')?.focus({ preventScroll: true });
        }
    }

    if (e.target.id === 'cancel-reply') {
        document.getElementById('comment-parent-id').value = '0';
        document.getElementById('reply-banner').classList.add('hidden');
        document.getElementById('reply-author-name').textContent = '';
    }
});
