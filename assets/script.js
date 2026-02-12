document.addEventListener('DOMContentLoaded', function () {
    initBricksParticles();
});

// Re-init on Bricks builder changes (if necessary, though full reload might happen)
// Bricks usually emits events. We'll listen for them if we can find them.

function initBricksParticles() {
    const sections = document.querySelectorAll('[data-bricks-particles="true"]');

    sections.forEach(function (section) {
        // check if already initialized
        if (section.querySelector('.bricks-particle-wrapper')) {
            return;
        }

        const configData = section.getAttribute('data-particles-config');
        const zIndex = section.getAttribute('data-particles-z-index') || 0;
        const opacity = section.getAttribute('data-particles-opacity') || 1;

        if (!configData) return;

        let config;
        try {
            config = JSON.parse(atob(configData));
        } catch (e) {
            console.error('Bricks Particles: Invalid JSON config', e);
            return;
        }

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'bricks-particle-wrapper';
        wrapper.id = 'bricks-particles-' + Math.random().toString(36).substr(2, 9);

        // Style wrapper
        wrapper.style.position = 'absolute';
        wrapper.style.top = '0';
        wrapper.style.left = '0';
        wrapper.style.width = '100%';
        wrapper.style.height = '100%';
        wrapper.style.zIndex = zIndex;
        wrapper.style.opacity = opacity;
        wrapper.style.pointerEvents = 'none'; // Allow clicks to pass through by default

        // Insert as first child of the section to be behind content? 
        // Sections in Bricks usually have a specific structure. 
        // If the user wants it as background, it should be absolute and z-index'd appropriately.
        // We prepend it so it sits behind content if content has higher z-index, but with z-index 0 it might sit on top of background image.
        section.insertBefore(wrapper, section.firstChild);

        // Ensure section has relative positioning if not static
        const computedStyle = window.getComputedStyle(section);
        if (computedStyle.position === 'static') {
            section.style.position = 'relative';
        }

        // Initialize particles
        particlesJS(wrapper.id, config);
    });
}
