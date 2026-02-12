/**
 * Custom Cursor - GSAP Animation
 * Smooth follower cursor with hover effects, magnetic pull, text labels, and click animation.
 *
 * Per-element attributes:
 *   data-cursor="text:Read More"       → show text label
 *   data-cursor="color:#ff0000"        → change cursor color
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

        // Hide native cursor globally — force on ALL elements to prevent conflicts with
        // Bricks nav menus and other components that set their own cursor styles
        var globalStyle = document.createElement('style');
        globalStyle.id = 'bep-cursor-hide-native';
        globalStyle.textContent = '*, *::before, *::after { cursor: none !important; }';
        document.head.appendChild(globalStyle);

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
        var boundElements = new WeakSet();

        function getScaleTarget() {
            // In dot-only mode, scale the dot UP. In ring/dot-ring, scale ring up and dot down.
            return cursorStyle;
        }

        function onHoverEnter(e) {
            isHovering = true;

            var el = e.currentTarget;
            var dataCursor = el.getAttribute('data-cursor');
            var customColor = null;
            var customText = null;

            // Parse data-cursor attribute
            if (dataCursor) {
                dataCursor.split(',').forEach(function (part) {
                    var kv = part.trim().split(':');
                    var key = kv[0] ? kv[0].trim() : '';
                    var val = kv.slice(1).join(':').trim(); // rejoin in case color has ':'
                    if (key === 'text' && val) customText = val;
                    if (key === 'color' && val) customColor = val;
                });
            }

            var style = getScaleTarget();

            if (style === 'dot') {
                // Dot-only: scale the dot UP
                if (hasDot) {
                    gsap.to(dot, { scale: hoverScale, duration: 0.3, ease: 'power2.out' });
                }
            } else {
                // Ring or Dot+Ring: scale ring up, dot down
                if (hasRing) {
                    gsap.to(ring, { scale: hoverScale, duration: 0.3, ease: 'power2.out' });
                }
                if (hasDot) {
                    gsap.to(dot, { scale: 0.5, duration: 0.3, ease: 'power2.out' });
                }
            }

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

                // When showing text, fill the ring
                if (hasRing && !ring.classList.contains('bep-cursor-blend')) {
                    ring.style.backgroundColor = customColor || 'var(--cc-ring-color)';
                }
            }

            // Hide cursor if data-cursor-hide is set
            if (el.hasAttribute('data-cursor-hide')) {
                wrapper.classList.add('bep-cursor-hidden');
            }
        }

        function onHoverLeave(e) {
            isHovering = false;
            var el = e.currentTarget;

            var style = getScaleTarget();

            // Reset scales
            if (style === 'dot') {
                if (hasDot) gsap.to(dot, { scale: 1, duration: 0.3, ease: 'power2.out' });
            } else {
                if (hasRing) gsap.to(ring, { scale: 1, duration: 0.3, ease: 'power2.out' });
                if (hasDot) gsap.to(dot, { scale: 1, duration: 0.3, ease: 'power2.out' });
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

            // Reset magnetic on the element itself
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
                var targets = document.querySelectorAll(targetSelector);
                targets.forEach(function (el) {
                    if (el.closest('.bep-custom-cursor-wrapper')) return;
                    if (boundElements.has(el)) return;
                    boundElements.add(el);

                    el.addEventListener('mouseenter', onHoverEnter);
                    el.addEventListener('mouseleave', onHoverLeave);
                });
            } catch (e) { /* invalid selector */ }

            // Magnetic: only elements with data-cursor-magnetic attribute
            if (config.magneticEnabled) {
                var magneticEls = document.querySelectorAll('[data-cursor-magnetic]');
                magneticEls.forEach(function (el) {
                    if (boundElements.has(el)) return; // already bound as hover
                    boundElements.add(el);

                    // Also bind hover if not already targeted
                    el.addEventListener('mouseenter', onHoverEnter);
                    el.addEventListener('mouseleave', onHoverLeave);
                });

                // Bind magnetic move separately (needs to run on all magnetic elements)
                document.querySelectorAll('[data-cursor-magnetic]').forEach(function (el) {
                    if (el._bepMagneticBound) return;
                    el._bepMagneticBound = true;
                    el.addEventListener('mousemove', onMagneticMove);
                });
            }
        }

        bindHoverTargets();

        // Re-bind on new content (e.g. Bricks infinite scroll, AJAX)
        var debounceTimer;
        var observer = new MutationObserver(function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(bindHoverTargets, 100);
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // === Mix Blend Mode — per-section control ===
        // Elements with data-cursor-blend="exclusion|difference|normal" override blend mode
        if (config.blendMode) {
            document.addEventListener('mousemove', function (e) {
                // Find the deepest ancestor with data-cursor-blend
                var target = e.target;
                var blendEl = target.closest ? target.closest('[data-cursor-blend]') : null;

                if (blendEl) {
                    var mode = blendEl.getAttribute('data-cursor-blend') || 'difference';
                    if (hasRing) ring.style.mixBlendMode = mode;
                    if (hasDot) dot.style.mixBlendMode = mode;
                } else {
                    // Default blend mode
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
                var style = getScaleTarget();
                if (style === 'dot') {
                    if (hasDot) gsap.to(dot, { scale: isHovering ? hoverScale * 0.7 : 0.6, duration: 0.15, ease: 'power3.out' });
                } else {
                    if (hasRing) gsap.to(ring, { scale: isHovering ? hoverScale * 0.8 : 0.8, duration: 0.15, ease: 'power3.out' });
                    if (hasDot) gsap.to(dot, { scale: isHovering ? 0.3 : 0.6, duration: 0.15, ease: 'power3.out' });
                }
            });

            document.addEventListener('mouseup', function () {
                var style = getScaleTarget();
                if (style === 'dot') {
                    if (hasDot) gsap.to(dot, { scale: isHovering ? hoverScale : 1, duration: 0.4, ease: 'elastic.out(1, 0.4)' });
                } else {
                    if (hasRing) gsap.to(ring, { scale: isHovering ? hoverScale : 1, duration: 0.4, ease: 'elastic.out(1, 0.4)' });
                    if (hasDot) gsap.to(dot, { scale: isHovering ? 0.5 : 1, duration: 0.4, ease: 'elastic.out(1, 0.4)' });
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
