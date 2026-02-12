/**
 * Custom Cursor - GSAP Animation
 * Smooth follower cursor with hover effects, magnetic pull, text labels, and click animation.
 *
 * Per-element attributes:
 *   data-cursor="text:Read More"       → show text label + scale cursor
 *   data-cursor="color:#ff0000"        → change cursor color + scale
 *   data-cursor="scale:2"             → custom scale factor
 *   data-cursor="text:Drag,color:#0f0" → combine multiple
 *   data-cursor-magnetic               → enable magnetic pull on this element
 *   data-cursor-blend="exclusion"      → override blend mode for sections
 *   data-cursor-hide                   → hide cursor over this element
 */
(function () {
    'use strict';

    function init() {
        var wrapper = document.querySelector('.bep-custom-cursor-wrapper');
        if (!wrapper) return;

        // Skip on touch-only devices
        if (window.matchMedia('(hover: none)').matches) {
            wrapper.style.display = 'none';
            return;
        }

        // === CRITICAL: Move wrapper to body to escape all stacking contexts ===
        // This fixes the cursor disappearing behind headers, navs, modals, etc.
        // Bricks layouts often create stacking contexts that trap z-index.
        if (wrapper.parentElement !== document.body) {
            document.body.appendChild(wrapper);
        }

        var config;
        try {
            config = JSON.parse(wrapper.getAttribute('data-cursor-config'));
        } catch (e) {
            return;
        }

        var dot = wrapper.querySelector('.bep-cursor-dot');
        var ring = wrapper.querySelector('.bep-cursor-ring');
        var textEl = wrapper.querySelector('.bep-cursor-text');
        var cursorStyle = config.style || 'dot-ring';
        var hasDot = !!dot;
        var hasRing = !!ring;

        // Hide native cursor globally
        var globalStyle = document.createElement('style');
        globalStyle.id = 'bep-cursor-hide-native';
        globalStyle.textContent = '*, *::before, *::after { cursor: none !important; }';
        document.head.appendChild(globalStyle);

        // Store original sizes for crisp width/height animation (no transform scale blur)
        var dotBaseSize = hasDot ? dot.offsetWidth : 0;
        var ringBaseSize = hasRing ? ring.offsetWidth : 0;
        var ringBaseBorder = hasRing ? parseFloat(getComputedStyle(ring).borderWidth) : 0;

        // Initialize GSAP centering — replaces CSS transform: translate(-50%, -50%)
        // This prevents GSAP x/y from clobbering the centering transform
        if (hasDot) gsap.set(dot, { xPercent: -50, yPercent: -50 });
        if (hasRing) gsap.set(ring, { xPercent: -50, yPercent: -50 });

        // GSAP quickTo for smooth following
        var speed = config.followSpeed || 0.2;
        var dotX, dotY, ringX, ringY;

        if (hasDot) {
            dotX = gsap.quickTo(dot, 'x', { duration: speed * 0.5, ease: 'power2.out' });
            dotY = gsap.quickTo(dot, 'y', { duration: speed * 0.5, ease: 'power2.out' });
        }

        if (hasRing) {
            ringX = gsap.quickTo(ring, 'x', { duration: speed, ease: 'power2.out' });
            ringY = gsap.quickTo(ring, 'y', { duration: speed, ease: 'power2.out' });
        }

        // Track mouse position
        document.addEventListener('mousemove', function (e) {
            if (dotX) dotX(e.clientX);
            if (dotY) dotY(e.clientY);
            if (ringX) ringX(e.clientX);
            if (ringY) ringY(e.clientY);
        });

        // Show cursor after first move
        document.addEventListener('mousemove', function show() {
            wrapper.classList.add('bep-cursor-visible');
            document.removeEventListener('mousemove', show);
        });

        // === Hover Effects ===
        var hoverScale = config.hoverScale || 1.5;
        var isHovering = false;
        var isScaled = false;
        var boundElements = new WeakSet();

        /**
         * Scale cursor using width/height instead of transform: scale()
         * This eliminates the blurry "zoom" effect on small elements.
         * The browser re-rasterizes at the new pixel size = always crisp.
         */
        function scaleCursor(scale, duration) {
            duration = duration || 0.3;

            if (cursorStyle === 'dot') {
                // Dot-only: grow the dot
                if (hasDot) {
                    gsap.to(dot, {
                        width: dotBaseSize * scale,
                        height: dotBaseSize * scale,
                        duration: duration,
                        ease: 'power2.out'
                    });
                }
            } else {
                // Ring or Dot+Ring: grow ring, shrink dot
                if (hasRing) {
                    gsap.to(ring, {
                        width: ringBaseSize * scale,
                        height: ringBaseSize * scale,
                        duration: duration,
                        ease: 'power2.out'
                    });
                }
                if (hasDot && scale > 1) {
                    // Shrink dot when ring grows
                    gsap.to(dot, {
                        width: dotBaseSize * 0.5,
                        height: dotBaseSize * 0.5,
                        duration: duration,
                        ease: 'power2.out'
                    });
                }
            }
        }

        function resetCursorSize(duration) {
            duration = duration || 0.3;

            if (hasDot) {
                gsap.to(dot, {
                    width: dotBaseSize,
                    height: dotBaseSize,
                    duration: duration,
                    ease: 'power2.out'
                });
            }
            if (hasRing) {
                gsap.to(ring, {
                    width: ringBaseSize,
                    height: ringBaseSize,
                    duration: duration,
                    ease: 'power2.out'
                });
            }
        }

        function onDataCursorEnter(e) {
            isHovering = true;
            isScaled = true;

            var el = e.currentTarget;
            var dataCursor = el.getAttribute('data-cursor');
            var customColor = null;
            var customText = null;
            var customScale = hoverScale;

            // Parse data-cursor attribute
            if (dataCursor) {
                dataCursor.split(',').forEach(function (part) {
                    var kv = part.trim().split(':');
                    var key = kv[0] ? kv[0].trim() : '';
                    var val = kv.slice(1).join(':').trim();
                    if (key === 'text' && val) customText = val;
                    if (key === 'color' && val) customColor = val;
                    if (key === 'scale' && val) customScale = parseFloat(val) || hoverScale;
                });
            }

            // Scale cursor (width/height animation — crisp, not blurry)
            scaleCursor(customScale);

            // Hover colors
            if (hasRing) ring.classList.add('bep-cursor-hover-active');
            if (hasDot) dot.classList.add('bep-cursor-hover-active');

            // Per-element color override
            if (customColor) {
                if (hasRing) {
                    ring.style.borderColor = customColor;
                    ring.style.backgroundColor = customColor;
                }
                if (hasDot) {
                    dot.style.backgroundColor = customColor;
                }
            }

            // Text label
            if (textEl && config.hoverText) {
                textEl.textContent = customText || config.hoverTextContent || 'View';
                textEl.classList.add('bep-cursor-text-visible');

                if (hasRing && !ring.classList.contains('bep-cursor-blend')) {
                    ring.style.backgroundColor = customColor || 'var(--cc-ring-color)';
                }
            }
        }

        function onHoverEnter(e) {
            // Basic hover: just flag it (no scaling for generic hover targets)
            isHovering = true;

            // Check if element also has data-cursor
            var el = e.currentTarget;
            if (el.hasAttribute('data-cursor')) {
                onDataCursorEnter(e);
                return;
            }

            // Hide cursor if data-cursor-hide is set
            if (el.hasAttribute('data-cursor-hide')) {
                wrapper.classList.add('bep-cursor-hidden');
            }
        }

        function onHoverLeave(e) {
            isHovering = false;
            var el = e.currentTarget;

            // Reset size if it was scaled
            if (isScaled) {
                resetCursorSize();
                isScaled = false;
            }

            // Remove hover state
            if (hasRing) {
                ring.classList.remove('bep-cursor-hover-active');
                ring.style.borderColor = '';
                ring.style.backgroundColor = '';
            }
            if (hasDot) {
                dot.classList.remove('bep-cursor-hover-active');
                dot.style.backgroundColor = '';
            }

            // Hide text
            if (textEl) {
                textEl.classList.remove('bep-cursor-text-visible');
            }

            // Un-hide
            wrapper.classList.remove('bep-cursor-hidden');

            // Reset magnetic on the element
            if (el.hasAttribute('data-cursor-magnetic')) {
                gsap.to(el, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, 0.5)' });
            }
        }

        function onMagneticMove(e) {
            var el = e.currentTarget;
            var rect = el.getBoundingClientRect();
            var cx = rect.left + rect.width / 2;
            var cy = rect.top + rect.height / 2;
            var strength = parseFloat(el.getAttribute('data-cursor-magnetic-strength')) || config.magneticStrength || 0.3;
            var dx = (e.clientX - cx) * strength;
            var dy = (e.clientY - cy) * strength;

            gsap.to(el, { x: dx, y: dy, duration: 0.3, ease: 'power2.out' });
        }

        // Bind hover events
        var targetSelector = config.hoverTargets || 'a, button';

        function bindHoverTargets() {
            try {
                // Bind generic hover targets (cursor: none only, no scaling)
                var targets = document.querySelectorAll(targetSelector);
                targets.forEach(function (el) {
                    if (el.closest('.bep-custom-cursor-wrapper')) return;
                    if (boundElements.has(el)) return;
                    boundElements.add(el);

                    el.addEventListener('mouseenter', onHoverEnter);
                    el.addEventListener('mouseleave', onHoverLeave);
                });
            } catch (e) { /* invalid selector */ }

            // Bind data-cursor elements that aren't already hover targets
            var dataCursorEls = document.querySelectorAll('[data-cursor]');
            dataCursorEls.forEach(function (el) {
                if (el.closest('.bep-custom-cursor-wrapper')) return;
                if (boundElements.has(el)) return;
                boundElements.add(el);

                el.addEventListener('mouseenter', onDataCursorEnter);
                el.addEventListener('mouseleave', onHoverLeave);
            });

            // Bind data-cursor-hide elements
            var hideEls = document.querySelectorAll('[data-cursor-hide]');
            hideEls.forEach(function (el) {
                if (boundElements.has(el)) return;
                boundElements.add(el);

                el.addEventListener('mouseenter', function () {
                    wrapper.classList.add('bep-cursor-hidden');
                });
                el.addEventListener('mouseleave', function () {
                    wrapper.classList.remove('bep-cursor-hidden');
                });
            });

            // Magnetic: only elements with data-cursor-magnetic
            if (config.magneticEnabled) {
                document.querySelectorAll('[data-cursor-magnetic]').forEach(function (el) {
                    if (el._bepMagneticBound) return;
                    el._bepMagneticBound = true;
                    el.addEventListener('mousemove', onMagneticMove);

                    // Also bind hover if not already bound
                    if (!boundElements.has(el)) {
                        boundElements.add(el);
                        el.addEventListener('mouseenter', onHoverEnter);
                        el.addEventListener('mouseleave', onHoverLeave);
                    }
                });
            }
        }

        bindHoverTargets();

        // Re-bind on new content (AJAX, infinite scroll)
        var debounceTimer;
        var observer = new MutationObserver(function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(bindHoverTargets, 100);
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // === Mix Blend Mode — per-section override ===
        if (config.blendMode) {
            document.addEventListener('mousemove', function (e) {
                var target = e.target;
                var blendEl = target.closest ? target.closest('[data-cursor-blend]') : null;

                if (blendEl) {
                    var mode = blendEl.getAttribute('data-cursor-blend') || 'difference';
                    if (hasRing) ring.style.mixBlendMode = mode;
                    if (hasDot) dot.style.mixBlendMode = mode;
                } else {
                    if (hasRing && ring.classList.contains('bep-cursor-blend')) {
                        ring.style.mixBlendMode = '';
                    }
                    if (hasDot && dot.classList.contains('bep-cursor-blend')) {
                        dot.style.mixBlendMode = '';
                    }
                }
            });
        }

        // === Click Effect ===
        if (config.clickEffect) {
            document.addEventListener('mousedown', function () {
                if (cursorStyle === 'dot') {
                    if (hasDot) {
                        var currentDotW = dot.offsetWidth;
                        gsap.to(dot, { width: currentDotW * 0.7, height: currentDotW * 0.7, duration: 0.15, ease: 'power3.out' });
                    }
                } else {
                    if (hasRing) {
                        var currentRingW = ring.offsetWidth;
                        gsap.to(ring, { width: currentRingW * 0.85, height: currentRingW * 0.85, duration: 0.15, ease: 'power3.out' });
                    }
                    if (hasDot) {
                        var currentDotW2 = dot.offsetWidth;
                        gsap.to(dot, { width: currentDotW2 * 0.7, height: currentDotW2 * 0.7, duration: 0.15, ease: 'power3.out' });
                    }
                }
            });

            document.addEventListener('mouseup', function () {
                if (isScaled) {
                    // Restore to hover size
                    scaleCursor(hoverScale, 0.4);
                } else {
                    // Restore to base size
                    resetCursorSize(0.4);
                }
            });
        }

        // === Hide when mouse leaves window ===
        document.addEventListener('mouseleave', function () {
            wrapper.classList.remove('bep-cursor-visible');
        });

        document.addEventListener('mouseenter', function () {
            wrapper.classList.add('bep-cursor-visible');
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
