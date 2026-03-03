const btn = document.getElementById('back-to-top');

if (btn) {
    function toggleBtn() {
        const visible = window.scrollY > 400;
        btn.classList.toggle('opacity-0', !visible);
        btn.classList.toggle('translate-y-4', !visible);
        btn.classList.toggle('pointer-events-none', !visible);
    }

    window.addEventListener('scroll', toggleBtn, { passive: true });
    toggleBtn();
}
