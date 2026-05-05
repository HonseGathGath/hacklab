document.addEventListener('DOMContentLoaded', () => {
    const markBtn = document.getElementById('mark-read');
    if (markBtn) {
        markBtn.addEventListener('click', async () => {
            const module = markBtn.dataset.module;
            const csrf = document.querySelector('input[name="csrf_token"]')?.value;
            const res = await fetch('../ajax/mark_lesson_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ module, csrf_token: csrf })
            });
            if (res.ok) {
                markBtn.textContent = 'Marked as read';
                markBtn.disabled = true;
            }
        });
    }
});
