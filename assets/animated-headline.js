/**
 * Animated Headline - GSAP Powered
 * Animation Types: Typing, Clip, Flip, Swirl, Blinds, Drop-in, Wave, Slide, Slide Down
 */

(function () {
    'use strict';

    function initAnimatedHeadline() {
        const wrappers = document.querySelectorAll('.animated-headline-wrapper');

        wrappers.forEach(function (wrapper) {
            if (wrapper.dataset.initialized === 'true') return;
            wrapper.dataset.initialized = 'true';

            const animationType = wrapper.dataset.animation || 'typing';
            const duration = parseInt(wrapper.dataset.duration) || 2500;
            const loop = wrapper.dataset.loop !== 'false';

            const phrases = wrapper.querySelectorAll('.ah-phrase');
            if (phrases.length === 0) return;

            // Debug log to confirm new JS is loaded
            console.log('Animated Headline 1.3.0 initialized. Type:', animationType);

            let currentIndex = 0;
            let isAnimating = false;

            // Animation configurations by type
            const animations = {
                typing: {
                    animateIn: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        gsap.set(chars, { opacity: 0 });
                        gsap.to(chars, {
                            opacity: 1,
                            duration: 0.05,
                            stagger: 0.05,
                            ease: 'none',
                            onComplete: callback
                        });
                    },
                    animateOut: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        gsap.to(chars, {
                            opacity: 0,
                            duration: 0.03,
                            stagger: { each: 0.03, from: 'end' },
                            ease: 'none',
                            onComplete: callback
                        });
                    }
                },
                clip: {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { clipPath: 'inset(0 100% 0 0)' },
                            {
                                clipPath: 'inset(0 0% 0 0)',
                                duration: 0.6,
                                ease: 'power2.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            clipPath: 'inset(0 0 0 100%)',
                            duration: 0.4,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { clipPath: 'inset(0 100% 0 0)' });
                                callback();
                            }
                        });
                    }
                },
                flip: {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { rotationX: -90, opacity: 0, transformOrigin: '50% 100%' },
                            {
                                rotationX: 0,
                                opacity: 1,
                                duration: 0.5,
                                ease: 'back.out(1.5)',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            rotationX: 90,
                            opacity: 0,
                            duration: 0.4,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { rotationX: -90, opacity: 0 });
                                callback();
                            }
                        });
                    }
                },
                swirl: {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { rotation: -180, scale: 0, opacity: 0, transformOrigin: '50% 50%' },
                            {
                                rotation: 0,
                                scale: 1,
                                opacity: 1,
                                duration: 0.6,
                                ease: 'back.out(1.7)',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            rotation: 180,
                            scale: 0,
                            opacity: 0,
                            duration: 0.4,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { rotation: -180, scale: 0, opacity: 0 });
                                callback();
                            }
                        });
                    }
                },
                blinds: {
                    animateIn: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        // Elementor Blinds: RotateY 180 -> 0 using fromTo to be safe
                        gsap.fromTo(chars,
                            {
                                rotationY: 180,
                                opacity: 0,
                                transformOrigin: '50% 50%'
                            },
                            {
                                rotationY: 0,
                                opacity: 1,
                                duration: 0.6,
                                stagger: 0.05,
                                ease: 'power2.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        // Elementor Blinds: RotateY 0 -> -180
                        gsap.to(chars, {
                            rotationY: -180,
                            opacity: 0,
                            duration: 0.6,
                            stagger: { each: 0.05, from: 'end' },
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(chars, { rotationY: 180, opacity: 0 });
                                callback();
                            }
                        });
                    }
                },
                'drop-in': {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { y: -100, opacity: 0 },
                            {
                                y: 0,
                                opacity: 1,
                                duration: 0.5,
                                ease: 'bounce.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            y: 100,
                            opacity: 0,
                            duration: 0.3,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { y: -100, opacity: 0 });
                                callback();
                            }
                        });
                    }
                },
                wave: {
                    animateIn: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        gsap.fromTo(chars,
                            { y: 30, opacity: 0 },
                            {
                                y: 0,
                                opacity: 1,
                                duration: 0.4,
                                stagger: 0.05,
                                ease: 'sine.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        const chars = phrase.querySelectorAll('.ah-char');
                        gsap.to(chars, {
                            y: -30,
                            opacity: 0,
                            duration: 0.3,
                            stagger: { each: 0.03, from: 'start' },
                            ease: 'sine.in',
                            onComplete: callback
                        });
                    }
                },
                slide: {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { x: 100, opacity: 0 },
                            {
                                x: 0,
                                opacity: 1,
                                duration: 0.5,
                                ease: 'power2.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            x: -100,
                            opacity: 0,
                            duration: 0.4,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { x: 100, opacity: 0 });
                                callback();
                            }
                        });
                    }
                },
                'slide-down': {
                    animateIn: function (phrase, callback) {
                        gsap.fromTo(phrase,
                            { y: -50, opacity: 0 },
                            {
                                y: 0,
                                opacity: 1,
                                duration: 0.5,
                                ease: 'power2.out',
                                onComplete: callback
                            }
                        );
                    },
                    animateOut: function (phrase, callback) {
                        gsap.to(phrase, {
                            y: 50,
                            opacity: 0,
                            duration: 0.4,
                            ease: 'power2.in',
                            onComplete: function () {
                                gsap.set(phrase, { y: -50, opacity: 0 });
                                callback();
                            }
                        });
                    }
                }
            };

            const anim = animations[animationType] || animations.typing;

            function switchPhrase() {
                if (isAnimating) return;
                if (phrases.length <= 1) return;

                isAnimating = true;

                const currentPhrase = phrases[currentIndex];
                const nextIndex = (currentIndex + 1) % phrases.length;
                const nextPhrase = phrases[nextIndex];

                if (!loop && nextIndex === 0) {
                    isAnimating = false;
                    return;
                }

                anim.animateOut(currentPhrase, function () {
                    currentPhrase.classList.remove('is-active');
                    nextPhrase.classList.add('is-active');

                    anim.animateIn(nextPhrase, function () {
                        currentIndex = nextIndex;
                        isAnimating = false;
                    });
                });
            }

            // Start animation
            const firstPhrase = phrases[0];
            anim.animateIn(firstPhrase, function () {
                if (phrases.length > 1) {
                    setInterval(switchPhrase, duration);
                }
            });
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAnimatedHeadline);
    } else {
        initAnimatedHeadline();
    }

    window.initAnimatedHeadline = initAnimatedHeadline;
})();
