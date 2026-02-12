/**
 * Timeline Scroll Animation
 * Uses GSAP ScrollTrigger to animate timeline progress on scroll
 */

(function () {
    'use strict';

    function initTimelineAnimations() {
        // Check for GSAP and ScrollTrigger
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            console.warn('Timeline: GSAP or ScrollTrigger not loaded');
            return;
        }

        gsap.registerPlugin(ScrollTrigger);

        // Find all timelines with scroll animation enabled
        const timelines = document.querySelectorAll('.bep-timeline[data-scroll-animation="true"]');

        timelines.forEach(function (timeline) {
            const progressLine = timeline.querySelector('.bep-timeline-progress');
            const items = timeline.querySelectorAll('.bep-timeline-item');
            const dots = timeline.querySelectorAll('.bep-timeline-dot');

            if (!progressLine || items.length === 0) return;

            // How many items to show as active on page load (default: 1)
            const initialActive = parseInt(timeline.getAttribute('data-initial-active') || '1', 10);

            // Restrict line height to stop at the last item's dot
            const lastItem = items[items.length - 1];
            const lastDot = lastItem.querySelector('.bep-timeline-dot');
            const trackLine = timeline.querySelector('.bep-timeline-line');

            function updateLineLimit() {
                if (!lastItem || !lastDot) return;

                // Calculate the exact height: from timeline top to center of last dot
                const timelineRect = timeline.getBoundingClientRect();
                const dotRect = lastDot.getBoundingClientRect();
                const lineHeight = dotRect.top - timelineRect.top + (dotRect.height / 2);

                // Apply explicit height and remove bottom:0 so the line stops at the last dot
                if (trackLine) {
                    trackLine.style.bottom = 'auto';
                    trackLine.style.height = lineHeight + 'px';
                }
                progressLine.style.bottom = 'auto';
                progressLine.style.height = lineHeight + 'px';
            }

            // Update initially and on resize/content change
            updateLineLimit();
            window.addEventListener('resize', updateLineLimit);

            if (typeof ResizeObserver !== 'undefined') {
                new ResizeObserver(updateLineLimit).observe(timeline);
            }

            // Pre-activate the first N items immediately (no scroll trigger needed)
            items.forEach(function (item, index) {
                const dot = item.querySelector('.bep-timeline-dot');
                const content = item.querySelector('.bep-timeline-card');

                if (index < initialActive) {
                    // Immediately activate dot and content — no animation delay
                    if (dot) dot.classList.add('active');
                    if (content) {
                        content.style.opacity = '1';
                        content.style.transform = 'translateY(0)';
                        content.classList.add('active');
                    }
                }
            });

            // Set initial progress line to cover the initially active dots
            if (initialActive > 0 && items.length > 0) {
                const targetIndex = Math.min(initialActive - 1, items.length - 1);
                const targetItem = items[targetIndex];
                const targetDot = targetItem.querySelector('.bep-timeline-dot');

                if (targetDot) {
                    // Calculate what fraction of the total line the initial items cover
                    const lastDotOffset = lastItem.offsetTop + (lastDot.offsetHeight / 2);
                    const targetDotOffset = targetItem.offsetTop + (targetDot.offsetHeight / 2);
                    const initialScale = lastDotOffset > 0 ? targetDotOffset / lastDotOffset : 0;

                    // Set initial scaleY so the progress line covers the active dots
                    gsap.set(progressLine, { scaleY: initialScale });
                }
            }

            // Animate progress line height based on scroll
            // End point matches the last dot position so the fill stays in sync
            const lastDotOffset = lastItem.offsetTop + (lastDot.offsetHeight / 2);
            gsap.to(progressLine, {
                scaleY: 1,
                ease: 'none',
                scrollTrigger: {
                    trigger: timeline,
                    start: 'top center',
                    end: function () {
                        return '+=' + lastDotOffset;
                    },
                    scrub: true
                }
            });

            // Activate dots and items as they enter viewport (skip initially active ones)
            items.forEach(function (item, index) {
                const dot = item.querySelector('.bep-timeline-dot');
                const content = item.querySelector('.bep-timeline-card');

                // Create ScrollTrigger for each item
                ScrollTrigger.create({
                    trigger: item,
                    start: 'top center+=100',
                    end: 'bottom center',
                    onEnter: function () {
                        if (dot) dot.classList.add('active');
                        if (content) content.classList.add('active');
                    },
                    onLeaveBack: function () {
                        // Don't deactivate initially active items when scrolling back up
                        if (index < initialActive) return;
                        if (dot) dot.classList.remove('active');
                        if (content) content.classList.remove('active');
                    }
                });

                // Fade in content animation (skip for initially active items)
                if (content && index >= initialActive) {
                    gsap.set(content, { opacity: 0, y: 30 });

                    ScrollTrigger.create({
                        trigger: item,
                        start: 'top center+=150',
                        onEnter: function () {
                            gsap.to(content, {
                                opacity: 1,
                                y: 0,
                                duration: 0.6,
                                ease: 'power2.out'
                            });
                        },
                        once: true // Only animate once
                    });
                }
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTimelineAnimations);
    } else {
        // Small delay to ensure all elements are rendered
        setTimeout(initTimelineAnimations, 100);
    }

    // Re-initialize for Bricks builder preview
    if (typeof bricksIsFrontend !== 'undefined' && !bricksIsFrontend) {
        document.addEventListener('bricks-builder-preview-updated', initTimelineAnimations);
    }
})();
