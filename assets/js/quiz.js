document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('quiz-form');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const module = form.dataset.module;
        const formData = new FormData(form);
        const answers = {};
        for (const [key, value] of formData.entries()) {
            if (key.startsWith('q')) {
                answers[parseInt(key.slice(1), 10)] = parseInt(value, 10);
            }
        }

        const csrf = formData.get('csrf_token');

        const res = await fetch('../ajax/submit_quiz.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ module, answers, csrf_token: csrf })
        });
        const data = await res.json();
        const result = document.getElementById('quiz-result');
        if (data.error) {
            result.textContent = data.error;
            return;
        }
        result.textContent = `Score: ${data.score}/${data.max} — ${data.passed ? 'Pass' : 'Try again'}`;
    });
});
