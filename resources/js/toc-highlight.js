// Highlight the active TOC entry as the user scrolls through the post.
// Runs on every page; exits immediately when no .toc-link elements exist.
const links = document.querySelectorAll('.toc-link');

if (links.length) {
    // Collect unique heading elements (mobile + sidebar TOC share the same IDs).
    const seen = new Set();
    const headingEls = Array.from(links)
        .map(l => document.getElementById(l.dataset.tocId))
        .filter(el => {
            if (!el || seen.has(el.id)) return false;
            seen.add(el.id);
            return true;
        });

    if (headingEls.length) {
        const OFFSET = 82; // matches scroll-margin-top (5rem)
        let lastId = null;

        function setActive(id) {
            if (id === lastId) return;
            lastId = id;
            links.forEach(l => {
                const active = l.dataset.tocId === id;
                l.classList.toggle('text-indigo-600', active);
                l.classList.toggle('dark:text-indigo-400', active);
                l.classList.toggle('font-medium', active);
                l.classList.toggle('border-indigo-500', active);
                l.classList.toggle('text-gray-500', !active);
                l.classList.toggle('dark:text-gray-400', !active);
                l.classList.toggle('border-transparent', !active);
            });
        }

        function update() {
            const scrollY = window.scrollY + OFFSET;
            let activeId = headingEls[0].id;
            for (const el of headingEls) {
                if (el.getBoundingClientRect().top + window.scrollY <= scrollY) {
                    activeId = el.id;
                } else {
                    break;
                }
            }
            setActive(activeId);
        }

        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => { update(); ticking = false; });
                ticking = true;
            }
        }, { passive: true });

        update();
    }
}
