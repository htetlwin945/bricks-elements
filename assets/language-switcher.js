document.addEventListener('DOMContentLoaded', () => {
    const switchers = document.querySelectorAll('.bep-lang-switcher[data-ls-config]');

    // Handle back/forward cache (bfcache) - remove animations when page is shown again
    window.addEventListener('pageshow', (event) => {
        // If the page was restored from cache, remove any lingering animations
        if (event.persisted) {
            removeAnimations();
        }
    });

    switchers.forEach(switcher => {
        const config = JSON.parse(switcher.dataset.lsConfig);
        const links = switcher.querySelectorAll('.bep-lang-item');

        links.forEach(link => {
            link.addEventListener('click', (e) => {
                // Ignore if opening in new tab or if it's the current language (and not a dropdown trigger)
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || link.target === '_blank') return;

                // If it's a dropdown trigger (clicking current lang to open menu), don't animate
                if (link.closest('.bep-lang-trigger')) return;

                // Don't prevent default! Let the browser navigate naturally.
                // The animation will run until the current page unloads.

                if (config.type === 'spinner') {
                    handleSpinnerAnimation(link, config);
                } else if (config.type === 'overlay') {
                    handleOverlayAnimation(config);
                } else if (config.type === 'flip') {
                    handleFlipAnimation(link, switcher);
                } else if (config.type === 'spin') {
                    handleSpinAnimation(link, switcher);
                }
            });
        });
    });

    function handleSpinnerAnimation(link, config) {
        const flag = link.querySelector('.bep-lang-flag');

        if (flag) {
            // Remove existing spinner if any
            const existing = flag.querySelector('.bep-ls-spinner');
            if (existing) existing.remove();

            // Create spinner
            const spinner = document.createElement('div');
            spinner.className = 'bep-ls-spinner';

            // Hide flag image
            const img = flag.querySelector('img');
            if (img) img.style.opacity = '0';

            flag.appendChild(spinner);

            // Animate using GSAP
            gsap.to(spinner, {
                rotation: 360,
                duration: 1,
                repeat: -1,
                ease: "linear"
            });
        }
    }

    function handleOverlayAnimation(config) {
        // Remove existing overlay if any
        const existing = document.querySelector('.bep-ls-overlay');
        if (existing) existing.remove();

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'bep-ls-overlay';
        overlay.style.backgroundColor = config.overlayColor;
        document.body.appendChild(overlay);

        // Animate in
        gsap.to(overlay, {
            opacity: 1,
            duration: 0.3
        });
    }

    function handleFlipAnimation(link, switcher) {
        const flagToAnimate = getFlagToAnimate(link, switcher);
        if (!flagToAnimate) return;

        const targetSrc = getTargetSrc(link);
        if (!targetSrc) return;

        storeOriginalSrc(flagToAnimate);

        // Animate - Continuous Rotation Y (Flip)
        gsap.to(flagToAnimate, {
            rotationY: 360,
            duration: 0.8,
            repeat: -1,
            ease: "linear",
            onUpdate: function () {
                swapSrcAtQuarter(this, flagToAnimate, targetSrc);
            }
        });
    }

    function handleSpinAnimation(link, switcher) {
        const flagToAnimate = getFlagToAnimate(link, switcher);
        if (!flagToAnimate) return;

        const targetSrc = getTargetSrc(link);
        if (!targetSrc) return;

        storeOriginalSrc(flagToAnimate);

        // Animate - Continuous Rotation Z (Spin)
        gsap.to(flagToAnimate, {
            rotation: 360,
            duration: 0.8,
            repeat: -1,
            ease: "linear",
            onUpdate: function () {
                swapSrcAtQuarter(this, flagToAnimate, targetSrc);
            }
        });
    }

    // Helpers
    function getFlagToAnimate(link, switcher) {
        const trigger = switcher.querySelector('.bep-lang-trigger .bep-lang-flag img');
        const targetImg = link.querySelector('img');
        return trigger || targetImg;
    }

    function getTargetSrc(link) {
        const img = link.querySelector('img');
        return img ? img.src : null;
    }

    function storeOriginalSrc(img) {
        if (!img.dataset.originalSrc) {
            img.dataset.originalSrc = img.src;
        }
    }

    function swapSrcAtQuarter(tween, img, targetSrc) {
        if (img.src !== targetSrc) {
            const p = tween.progress();
            if (p > 0.25 && p < 0.75) {
                img.src = targetSrc;
            }
        }
    }

    function removeAnimations() {
        // Remove spinners
        document.querySelectorAll('.bep-ls-spinner').forEach(el => el.remove());

        // Show flags again & Restore Sources
        document.querySelectorAll('.bep-lang-flag img').forEach(img => {
            img.style.opacity = '1';

            // Restore original src if morphed
            if (img.dataset.originalSrc) {
                img.src = img.dataset.originalSrc;
                delete img.dataset.originalSrc;
            }

            // Reset rotation/GSAP
            gsap.killTweensOf(img);
            gsap.set(img, { clearProps: "all" });
        });

        // Remove overlays
        document.querySelectorAll('.bep-ls-overlay').forEach(el => el.remove());
    }
});
