/**
 * Letter Launcher - GSAP Powered Animation
 * Uses SplitText for text splitting, ScrollTrigger for scroll-based animation
 */

(function () {
    'use strict';

    // Register GSAP plugins
    gsap.registerPlugin(ScrollTrigger);

    function initLetterLauncher() {
        const wrappers = document.querySelectorAll('.letter-launcher-wrapper');

        wrappers.forEach(function (wrapper) {
            if (wrapper.dataset.initialized === 'true') return;
            wrapper.dataset.initialized = 'true';

            // Get settings from data attributes
            const yDistance = parseFloat(wrapper.dataset.y) || 50;
            const rotation = parseFloat(wrapper.dataset.rotation) || 15;
            const duration = parseFloat(wrapper.dataset.duration) || 0.6;
            const stagger = parseFloat(wrapper.dataset.stagger) || 0.03;
            const easing = wrapper.dataset.easing || 'back.out(1.7)';
            const displayDuration = parseFloat(wrapper.dataset.displayDuration) || 3;
            const loop = wrapper.dataset.loop !== 'false';
            const useScrollTrigger = wrapper.dataset.scrollTrigger === 'true';
            const scrollStart = wrapper.dataset.scrollStart || 'top 80%';

            const phrases = wrapper.querySelectorAll('.ll-phrase');
            if (phrases.length === 0) return;

            let currentIndex = 0;
            let isAnimating = false;
            let splitInstances = [];

            // Split all phrases using SplitText
            phrases.forEach(function (phrase, index) {
                const split = SplitText.create(phrase, { type: 'chars' });
                splitInstances[index] = split;

                // Hide all characters initially
                gsap.set(split.chars, {
                    y: yDistance,
                    rotation: rotation,
                    opacity: 0
                });
            });

            function animateIn(phraseIndex, callback) {
                const split = splitInstances[phraseIndex];

                gsap.to(split.chars, {
                    y: 0,
                    rotation: 0,
                    opacity: 1,
                    duration: duration,
                    stagger: stagger,
                    ease: easing,
                    onComplete: callback
                });
            }

            function animateOut(phraseIndex, callback) {
                const split = splitInstances[phraseIndex];

                gsap.to(split.chars, {
                    y: -yDistance,
                    rotation: -rotation,
                    opacity: 0,
                    duration: duration,
                    stagger: {
                        each: stagger,
                        from: 'end' // Animate out in reverse
                    },
                    ease: 'power2.in',
                    onComplete: callback
                });
            }

            function switchPhrase() {
                if (isAnimating) return;
                if (phrases.length <= 1) return;

                isAnimating = true;

                const currentPhrase = phrases[currentIndex];
                const nextIndex = (currentIndex + 1) % phrases.length;
                const nextPhrase = phrases[nextIndex];

                // Stop if not looping and reached end
                if (!loop && nextIndex === 0) {
                    isAnimating = false;
                    return;
                }

                // Animate out current
                animateOut(currentIndex, function () {
                    currentPhrase.classList.remove('is-active');

                    // Reset current phrase chars to starting position
                    const split = splitInstances[currentIndex];
                    gsap.set(split.chars, {
                        y: yDistance,
                        rotation: rotation,
                        opacity: 0
                    });

                    // Activate and animate in next
                    nextPhrase.classList.add('is-active');

                    animateIn(nextIndex, function () {
                        currentIndex = nextIndex;
                        isAnimating = false;
                    });
                });
            }

            function startAnimation() {
                // Animate first phrase
                animateIn(0, function () {
                    // Start rotation if multiple phrases
                    if (phrases.length > 1) {
                        setInterval(switchPhrase, displayDuration * 1000);
                    }
                });
            }

            // Trigger animation
            if (useScrollTrigger) {
                ScrollTrigger.create({
                    trigger: wrapper,
                    start: scrollStart,
                    onEnter: startAnimation,
                    once: true
                });
            } else {
                // Animate on load
                startAnimation();
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLetterLauncher);
    } else {
        initLetterLauncher();
    }

    // Expose for dynamic content
    window.initLetterLauncher = initLetterLauncher;
})();
