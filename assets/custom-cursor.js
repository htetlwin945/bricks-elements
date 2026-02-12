/**
 * Custom Cursor - GSAP Animation
 * Smooth follower cursor with hover effects, magnetic pull, text labels, and click animation.
 *
 * ARCHITECTURE NOTE:
 * Dot and ring are moved OUT of the wrapper and directly into <body> as siblings.
 * This is CRITICAL for mix-blend-mode to work — position:fixed wrappers create
 * stacking contexts that isolate blend mode, making it blend with the wrapper's
 * transparent background instead of actual page content.
 *
 * Per-element attributes:
 *   data-cursor="text:Read More"       → show text label
 *   data-cursor="color:#ff0000"        → change cursor color
 *   data-cursor="scale:2"             → custom scale factor
 *   data-cursor="text:Drag,color:#0f0" → combine multiple
 *   data-cursor-magnetic               → enable magnetic pull
 *   data-cursor-blend="exclusion"      → blend mode for sections
 *   data-cursor-hide                   → hide cursor over element
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

        // === CRITICAL: Move dot and ring directly into <body> ===
        // This breaks them out of the wrapper's stacking context so mix-blend-mode
        // composites against actual page content, not the wrapper's transparent bg.
        if (hasDot) document.body.appendChild(dot);
        if (hasRing) document.body.appendChild(ring);

        // If ring has text element, it moves with the ring (it's a child of ring)

        // Hide the original wrapper (it's now empty, only used for config in editor)
        wrapper.style.display = 'none';

        // Hide native cursor globally
        var globalStyle = document.createElement('style');
        globalStyle.id = 'bep-cursor-hide-native';
        globalStyle.textContent = '*, *::before, *::after { cursor: none !important; }';
        document.head.appendChild(globalStyle);

        // Store original sizes for crisp width/height animation
        var dotBaseSize = hasDot ? dot.offsetWidth : 0;
        var ringBaseSize = hasRing ? ring.offsetWidth : 0;

        // Initialize GSAP centering
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

        // Visibility state
        var cursorVisible = false;

        function showCursor() {
            if (cursorVisible) return;
            cursorVisible = true;
            if (hasDot) dot.style.visibility = 'visible';
            if (hasRing) ring.style.visibility = 'visible';
        }

        function hideCursor() {
            if (!cursorVisible) return;
            cursorVisible = false;
            if (hasDot) dot.style.visibility = 'hidden';
            if (hasRing) ring.style.visibility = 'hidden';
        }

        // Track mouse position
        document.addEventListener('mousemove', function (e) {
            showCursor();
            if (dotX) dotX(e.clientX);
            if (dotY) dotY(e.clientY);
            if (ringX) ringX(e.clientX);
            if (ringY) ringY(e.clientY);
        });

        // === Hover Effects ===
        var hoverScale = config.hoverScale || 1.5;
        var isHovering = false;
        var isScaled = false;
        var boundElements = new WeakSet();

        /**
         * Scale via width/height (crisp, no transform blur)
         */
        function scaleCursor(scale, duration) {
            duration = duration || 0.3;
            if (cursorStyle === 'dot') {
                if (hasDot) {
                    gsap.to(dot, { width: dotBaseSize * scale, height: dotBaseSize * scale, duration: duration, ease: 'power2.out' });
                }
            } else {
                if (hasRing) {
                    gsap.to(ring, { width: ringBaseSize * scale, height: ringBaseSize * scale, duration: duration, ease: 'power2.out' });
                }
                if (hasDot && scale > 1) {
                    gsap.to(dot, { width: dotBaseSize * 0.5, height: dotBaseSize * 0.5, duration: duration, ease: 'power2.out' });
                }
            }
        }

        function resetCursorSize(duration) {
            duration = duration || 0.3;
            if (hasDot) {
                gsap.to(dot, { width: dotBaseSize, height: dotBaseSize, duration: duration, ease: 'power2.out' });
            }
            if (hasRing) {
                gsap.to(ring, { width: ringBaseSize, height: ringBaseSize, duration: duration, ease: 'power2.out' });
            }
        }

        function onDataCursorEnter(e) {
            isHovering = true;
            var el = e.currentTarget;
            var dataCursor = el.getAttribute('data-cursor');
            var customColor = null;
            var customText = null;
            var hasScaleKey = false;
            var customScale = null;

            if (dataCursor) {
                dataCursor.split(',').forEach(function (part) {
                    var kv = part.trim().split(':');
                    var key = kv[0] ? kv[0].trim() : '';
                    var val = kv.slice(1).join(':').trim();
                    if (key === 'text' && val) customText = val;
                    if (key === 'color' && val) customColor = val;
                    if (key === 'scale') {
                        hasScaleKey = true;
                        customScale = parseFloat(val) || hoverScale;
                    }
                });
            }

            // Only scale if explicitly requested
            if (hasScaleKey && customScale !== null) {
                isScaled = true;
                scaleCursor(customScale);
            }

            // Per-element color override
            if (customColor) {
                // Temporarily disable blend so color can show
                if (hasDot && dot.classList.contains('bep-cursor-blend')) {
                    dot.classList.remove('bep-cursor-blend');
                    dot._bepBlendRemoved = true;
                }
                if (hasRing && ring.classList.contains('bep-cursor-blend')) {
                    ring.classList.remove('bep-cursor-blend');
                    ring._bepBlendRemoved = true;
                }

                if (hasRing) {
                    ring.style.borderColor = customColor;
                    ring.style.backgroundColor = customColor;
                }
                if (hasDot) {
                    dot.style.setProperty('background', customColor, 'important');
                }
            }

            // Hover color classes (only if no custom color)
            if (!customColor) {
                if (hasRing) ring.classList.add('bep-cursor-hover-active');
                if (hasDot) dot.classList.add('bep-cursor-hover-active');
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
            isHovering = true;
            var el = e.currentTarget;
            if (el.hasAttribute('data-cursor')) {
                onDataCursorEnter(e);
                return;
            }
            if (el.hasAttribute('data-cursor-hide')) {
                hideCursor();
            }
        }

        function onHoverLeave(e) {
            isHovering = false;
            var el = e.currentTarget;

            if (isScaled) {
                resetCursorSize();
                isScaled = false;
            }

            if (hasRing) {
                ring.classList.remove('bep-cursor-hover-active');
                ring.style.borderColor = '';
                ring.style.backgroundColor = '';
            }
            if (hasDot) {
                dot.classList.remove('bep-cursor-hover-active');
                dot.style.removeProperty('background');
                dot.style.backgroundColor = '';
            }

            // Restore blend class if temporarily removed
            if (hasDot && dot._bepBlendRemoved) {
                dot.classList.add('bep-cursor-blend');
                dot._bepBlendRemoved = false;
            }
            if (hasRing && ring._bepBlendRemoved) {
                ring.classList.add('bep-cursor-blend');
                ring._bepBlendRemoved = false;
            }

            if (textEl) {
                textEl.classList.remove('bep-cursor-text-visible');
            }

            // Show cursor if it was hidden
            showCursor();

            // Reset magnetic
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

            var dataCursorEls = document.querySelectorAll('[data-cursor]');
            dataCursorEls.forEach(function (el) {
                if (el.closest('.bep-custom-cursor-wrapper')) return;
                if (boundElements.has(el)) return;
                boundElements.add(el);
                el.addEventListener('mouseenter', onDataCursorEnter);
                el.addEventListener('mouseleave', onHoverLeave);
            });

            var hideEls = document.querySelectorAll('[data-cursor-hide]');
            hideEls.forEach(function (el) {
                if (boundElements.has(el)) return;
                boundElements.add(el);
                el.addEventListener('mouseenter', function () { hideCursor(); });
                el.addEventListener('mouseleave', function () { showCursor(); });
            });

            if (config.magneticEnabled) {
                document.querySelectorAll('[data-cursor-magnetic]').forEach(function (el) {
                    if (el._bepMagneticBound) return;
                    el._bepMagneticBound = true;
                    el.addEventListener('mousemove', onMagneticMove);
                    if (!boundElements.has(el)) {
                        boundElements.add(el);
                        el.addEventListener('mouseenter', onHoverEnter);
                        el.addEventListener('mouseleave', onHoverLeave);
                    }
                });
            }
        }

        bindHoverTargets();

        // Re-bind on DOM changes
        var debounceTimer;
        var observer = new MutationObserver(function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(bindHoverTargets, 100);
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // === Mix Blend Mode — per-element/section control ===
        // Works independently of global blend checkbox.
        // data-cursor-blend="difference|exclusion|normal" on any element.
        var blendActive = false;

        document.addEventListener('mousemove', function (e) {
            var target = e.target;
            var blendEl = target.closest ? target.closest('[data-cursor-blend]') : null;

            if (blendEl) {
                var mode = blendEl.getAttribute('data-cursor-blend') || 'difference';

                if (mode === 'normal' || mode === 'none') {
                    // Explicitly disable blend for this area
                    if (hasDot) {
                        dot.style.mixBlendMode = 'normal';
                        if (!dot.classList.contains('bep-cursor-blend')) {
                            dot.style.removeProperty('background');
                        }
                    }
                    if (hasRing) {
                        ring.style.mixBlendMode = 'normal';
                        if (!ring.classList.contains('bep-cursor-blend')) {
                            ring.style.removeProperty('background');
                        }
                    }
                    blendActive = false;
                } else {
                    // Apply blend mode + white background
                    if (hasDot) {
                        dot.style.mixBlendMode = mode;
                        dot.style.setProperty('background', 'white', 'important');
                    }
                    if (hasRing) {
                        ring.style.mixBlendMode = mode;
                        ring.style.setProperty('background', 'white', 'important');
                        ring.style.borderColor = 'white';
                    }
                    blendActive = true;
                }
            } else if (blendActive) {
                // Left blend area — restore defaults
                blendActive = false;
                if (hasDot) {
                    dot.style.mixBlendMode = '';
                    if (!dot.classList.contains('bep-cursor-blend')) {
                        dot.style.removeProperty('background');
                    }
                }
                if (hasRing) {
                    ring.style.mixBlendMode = '';
                    ring.style.borderColor = '';
                    if (!ring.classList.contains('bep-cursor-blend')) {
                        ring.style.removeProperty('background');
                    }
                }
            }
        });

        // === Click Effect ===
        if (config.clickEffect) {
            document.addEventListener('mousedown', function () {
                if (cursorStyle === 'dot') {
                    if (hasDot) {
                        var s = dot.offsetWidth;
                        gsap.to(dot, { width: s * 0.7, height: s * 0.7, duration: 0.15, ease: 'power3.out' });
                    }
                } else {
                    if (hasRing) {
                        var rs = ring.offsetWidth;
                        gsap.to(ring, { width: rs * 0.85, height: rs * 0.85, duration: 0.15, ease: 'power3.out' });
                    }
                    if (hasDot) {
                        var ds = dot.offsetWidth;
                        gsap.to(dot, { width: ds * 0.7, height: ds * 0.7, duration: 0.15, ease: 'power3.out' });
                    }
                }
            });

            document.addEventListener('mouseup', function () {
                if (isScaled) {
                    scaleCursor(hoverScale, 0.4);
                } else {
                    resetCursorSize(0.4);
                }
            });
        }

        // Hide/show on window leave/enter
        document.addEventListener('mouseleave', function () { hideCursor(); });
        document.addEventListener('mouseenter', function () { showCursor(); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
