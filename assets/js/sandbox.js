document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('toggle-mode');
    const frame = document.querySelector('.sandbox__frame');
    if (!toggle || !frame) return;

    toggle.addEventListener('click', () => {
        const url = new URL(frame.src, window.location.origin);
        const current = url.searchParams.get('mode') || 'vulnerable';
        url.searchParams.set('mode', current === 'vulnerable' ? 'fixed' : 'vulnerable');
        frame.src = url.toString();
    });
});
