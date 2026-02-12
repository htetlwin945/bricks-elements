document.addEventListener('DOMContentLoaded', () => {

    function initReadMore() {
        const containers = document.querySelectorAll('.read-more-container');

        containers.forEach(container => {
            if (container.classList.contains('js-init')) return;
            container.classList.add('js-init');

            const btn = container.querySelector('.read-more-toggle');
            const contentWrapper = container.querySelector('.read-more-content-wrapper');
            const innerContent = container.querySelector('.read-more-inner-content');

            if (!btn || !contentWrapper || !innerContent) return;

            const collapsedHeight = container.getAttribute('data-collapsed-height') || '150px';

            btn.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent standard button submit if inside form, or jump

                const isExpanded = container.classList.contains('is-expanded');

                if (!isExpanded) {
                    // Expand
                    const fullHeight = innerContent.offsetHeight;
                    contentWrapper.style.height = fullHeight + 'px';
                    container.classList.add('is-expanded');
                    btn.textContent = btn.getAttribute('data-less');
                } else {
                    // Collapse
                    contentWrapper.style.height = collapsedHeight;
                    container.classList.remove('is-expanded');
                    btn.textContent = btn.getAttribute('data-more');
                }
            });

            // Handle content resize (optional, but good for responsiveness)
            window.addEventListener('resize', () => {
                if (container.classList.contains('is-expanded')) {
                    contentWrapper.style.height = innerContent.offsetHeight + 'px';
                }
            });
        });
    }

    // Init on load
    initReadMore();

    // Support for Bricks builder (mutation observer or event)
    // Bricks often re-renders elements.
    if (window.bricksIsFrontend) {
        // Standard frontend init is enough usually
    }
});
