/**
 * Custom Cursor - GSAP Animation
 *
 * ARCHITECTURE (based on https://codepen.io/akapowl/pen/LYOdYXK):
 * Dot and ring are moved OUT of wrapper, directly into <body>.
 * This is required for mix-blend-mode to work — wrappers with position:fixed
 * create stacking contexts that isolate blend mode.
 *
 * CSS variables are copied from wrapper to each element before moving,
 * preserving Bricks control values.
 *
 * Per-element attributes:
 *   data-cursor="text:Read More"       → show text label
 *   data-cursor="color:#ff0000"        → change cursor color
 *   data-cursor="scale:2"             → explicit scale factor
 *   data-cursor="text:Drag,color:#0f0" → combine multiple
 *   data-cursor-magnetic               → enable magnetic pull
 *   data-cursor-blend="exclusion"      → blend mode on specific sections
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

        // === Copy CSS variables from wrapper to each element ===
        // Bricks sets CSS vars on the wrapper via inline styles.
        // After moving dot/ring to <body>, they lose inheritance.
        // We copy the vars so styles keep working.
        var cssVars = [
            '--cc-dot-size', '--cc-ring-size', '--cc-ring-width',
            '--cc-dot-color', '--cc-ring-color',
            '--cc-dot-hover-color', '--cc-ring-hover-color',
            '--cc-text-color', '--cc-text-size'
        ];
        var wrapperCS = getComputedStyle(wrapper);
        cssVars.forEach(function (v) {
            var val = wrapperCS.getPropertyValue(v).trim();
            if (val) {
                if (hasDot) dot.style.setProperty(v, val);
                if (hasRing) ring.style.setProperty(v, val);
            }
        });

        // === Move dot and ring directly into <body> ===
        // This breaks them out of any stacking context so
        // mix-blend-mode composites against actual page content.
        if (hasDot) document.body.appendChild(dot);
        if (hasRing) document.body.appendChild(ring);

        // Hide the now-empty wrapper (still in DOM for editor preview)
        wrapper.style.display = 'none';

        // Hide native cursor (unless showNativeCursor is enabled)
        if (!config.showNativeCursor) {
            var globalStyle = document.createElement('style');
            globalStyle.id = 'bep-cursor-hide-native';
            globalStyle.textContent = '*, *::before, *::after { cursor: none !important; }';
            document.head.appendChild(globalStyle);
        }

        // Read computed sizes AFTER moving to body (CSS vars applied)
        var dotBaseSize = hasDot ? parseFloat(wrapperCS.getPropertyValue('--cc-dot-size')) || dot.offsetWidth : 0;
        var ringBaseSize = hasRing ? parseFloat(wrapperCS.getPropertyValue('--cc-ring-size')) || ring.offsetWidth : 0;

        // Initialize GSAP centering (same as reference CodePen)
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

        // === Skew/stretch animation on move ===
        // Rotates cursor to face movement direction and stretches along that axis
        // like a droplet: head at mouse tip, tail trails behind.
        var skewEnabled = config.skewEnabled || false;
        var skewStrength = config.skewStrength || 3;
        var prevMouse = { x: 0, y: 0 };
        var velocity = { x: 0, y: 0 };
        var lastAngle = 0;

        if (skewEnabled) {
            gsap.ticker.add(function () {
                var vx = mouse.x - prevMouse.x;
                var vy = mouse.y - prevMouse.y;
                prevMouse.x = mouse.x;
                prevMouse.y = mouse.y;

                // Smooth velocity for fluid motion
                velocity.x += (vx - velocity.x) * 0.15;
                velocity.y += (vy - velocity.y) * 0.15;

                // Speed magnitude
                var spd = Math.sqrt(velocity.x * velocity.x + velocity.y * velocity.y);

                // Stretch proportional to speed (clamped)
                var stretch = 1 + Math.min(spd * skewStrength * 0.04, skewStrength * 0.15);
                var squeeze = 1 / Math.sqrt(stretch); // Preserve area (width shrinks as length grows)

                // Angle of movement direction
                if (spd > 0.5) {
                    lastAngle = Math.atan2(velocity.y, velocity.x) * (180 / Math.PI);
                }

                if (hasDot) {
                    gsap.to(dot, {
                        rotation: lastAngle,
                        scaleX: stretch,
                        scaleY: squeeze,
                        duration: 0.15,
                        ease: 'power2.out',
                        overwrite: 'auto'
                    });
                }
                if (hasRing) {
                    // Ring stretches less for a layered trailing feel
                    var ringStretch = 1 + (stretch - 1) * 0.4;
                    var ringSqueeze = 1 / Math.sqrt(ringStretch);
                    gsap.to(ring, {
                        rotation: lastAngle,
                        scaleX: ringStretch,
                        scaleY: ringSqueeze,
                        duration: 0.2,
                        ease: 'power2.out',
                        overwrite: 'auto'
                    });
                }
            });
        }

        var mouse = { x: -100, y: -100 };

        // Show cursor on first move
        var shown = false;
        document.addEventListener('mousemove', function (e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
            if (!shown) {
                shown = true;
                if (hasDot) dot.classList.add('bep-cursor-visible');
                if (hasRing) ring.classList.add('bep-cursor-visible');
            }
            // Don't override position when sticky is active
            if (!isSticky) {
                if (dotX) dotX(e.clientX);
                if (dotY) dotY(e.clientY);
                if (ringX) ringX(e.clientX);
                if (ringY) ringY(e.clientY);
            }
        });

        // === Hover Effects ===
        var hoverScale = config.hoverScale || 1.5;
        var isHovering = false;
        var isScaled = false;
        var isSticky = false;
        var boundElements = new WeakSet();

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
                // Temporarily disable blend so color shows
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

            if (!customColor) {
                if (hasRing) ring.classList.add('bep-cursor-hover-active');
                if (hasDot) dot.classList.add('bep-cursor-hover-active');
            }

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

            // If element has data-cursor, use the full data-cursor logic
            if (el.hasAttribute('data-cursor')) {
                onDataCursorEnter(e);
                return;
            }

            // Hide cursor if data-cursor-hide is set
            if (el.hasAttribute('data-cursor-hide')) {
                if (hasDot) dot.classList.remove('bep-cursor-visible');
                if (hasRing) ring.classList.remove('bep-cursor-visible');
                return;
            }

            // Scale cursor for hover targets (this is the purpose of the hover targets field)
            isScaled = true;
            scaleCursor(hoverScale);

            // Apply hover color classes
            if (hasRing) ring.classList.add('bep-cursor-hover-active');
            if (hasDot) dot.classList.add('bep-cursor-hover-active');
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

            // Restore blend class
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

            // Show cursor if hidden
            if (shown) {
                if (hasDot) dot.classList.add('bep-cursor-visible');
                if (hasRing) ring.classList.add('bep-cursor-visible');
            }

            if (el.hasAttribute('data-cursor-magnetic')) {
                gsap.to(el, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, 0.5)' });
            }

            // Unstick
            if (isSticky) {
                isSticky = false;
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
            } catch (e) { }

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
                el.addEventListener('mouseenter', function () {
                    if (hasDot) dot.classList.remove('bep-cursor-visible');
                    if (hasRing) ring.classList.remove('bep-cursor-visible');
                    // Restore native cursor on this element
                    if (config.restoreNativeCursorOnHide && !config.showNativeCursor) {
                        el.style.setProperty('cursor', 'auto', 'important');
                    }
                });
                el.addEventListener('mouseleave', function () {
                    if (shown) {
                        if (hasDot) dot.classList.add('bep-cursor-visible');
                        if (hasRing) ring.classList.add('bep-cursor-visible');
                    }
                    // Remove cursor override
                    if (config.restoreNativeCursorOnHide && !config.showNativeCursor) {
                        el.style.removeProperty('cursor');
                    }
                });
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

            // === Sticky cursor ===
            document.querySelectorAll('[data-cursor-stick]').forEach(function (el) {
                if (el._bepStickyBound) return;
                el._bepStickyBound = true;

                el.addEventListener('mouseenter', function (e) {
                    isHovering = true;
                    isSticky = true;

                    var rect = el.getBoundingClientRect();
                    var cx = rect.left + rect.width / 2;
                    var cy = rect.top + rect.height / 2;

                    // Parse custom size from attribute (e.g. "80px" or "80")
                    var stickyVal = el.getAttribute('data-cursor-stick');
                    var stickySize = parseFloat(stickyVal) || 0;

                    // Animate cursor to element center using quickTo (avoids conflict)
                    if (hasDot && dotX && dotY) {
                        dotX(cx);
                        dotY(cy);
                    }
                    if (hasRing && ringX && ringY) {
                        ringX(cx);
                        ringY(cy);
                    }

                    // Resize cursor to custom size if specified
                    if (stickySize > 0) {
                        isScaled = true;
                        if (cursorStyle === 'dot') {
                            // Dot-only mode: grow the dot itself
                            if (hasDot) {
                                gsap.to(dot, { width: stickySize, height: stickySize, duration: 0.3, ease: 'power2.out' });
                            }
                        } else {
                            // Ring modes: grow ring, shrink dot
                            if (hasRing) {
                                gsap.to(ring, { width: stickySize, height: stickySize, duration: 0.3, ease: 'power2.out' });
                            }
                            if (hasDot) {
                                gsap.to(dot, { width: dotBaseSize * 0.5, height: dotBaseSize * 0.5, duration: 0.3, ease: 'power2.out' });
                            }
                        }
                    }

                    // Apply hover styling
                    if (hasRing) ring.classList.add('bep-cursor-hover-active');
                    if (hasDot) dot.classList.add('bep-cursor-hover-active');
                });

                el.addEventListener('mousemove', function (e) {
                    if (!isSticky) return;
                    var rect = el.getBoundingClientRect();
                    var cx = rect.left + rect.width / 2;
                    var cy = rect.top + rect.height / 2;

                    // Soft offset: cursor drifts slightly toward mouse but stays anchored
                    var offsetX = (e.clientX - cx) * 0.15;
                    var offsetY = (e.clientY - cy) * 0.15;

                    if (hasDot && dotX && dotY) {
                        dotX(cx + offsetX);
                        dotY(cy + offsetY);
                    }
                    if (hasRing && ringX && ringY) {
                        ringX(cx + offsetX * 0.6);
                        ringY(cy + offsetY * 0.6);
                    }
                });

                el.addEventListener('mouseleave', function (e) {
                    isSticky = false;
                    isHovering = false;

                    // Reset size
                    if (isScaled) {
                        resetCursorSize();
                        isScaled = false;
                    }

                    // Remove hover styling
                    if (hasRing) ring.classList.remove('bep-cursor-hover-active');
                    if (hasDot) dot.classList.remove('bep-cursor-hover-active');

                    // Cursor will naturally return to mouse via the normal
                    // mousemove quickTo handler (isSticky is now false)
                });
            });
        }

        bindHoverTargets();

        var debounceTimer;
        var observer = new MutationObserver(function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(bindHoverTargets, 100);
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // === Mix Blend Mode — per-element/section ===
        // Works independently of global blend checkbox.
        var blendActive = false;

        document.addEventListener('mousemove', function (e) {
            var target = e.target;
            var blendEl = target.closest ? target.closest('[data-cursor-blend]') : null;

            if (blendEl) {
                var mode = blendEl.getAttribute('data-cursor-blend') || 'difference';

                if (mode === 'normal' || mode === 'none') {
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

        // Hide/show on window enter/leave
        document.addEventListener('mouseleave', function () {
            if (hasDot) dot.classList.remove('bep-cursor-visible');
            if (hasRing) ring.classList.remove('bep-cursor-visible');
        });

        document.addEventListener('mouseenter', function () {
            if (shown) {
                if (hasDot) dot.classList.add('bep-cursor-visible');
                if (hasRing) ring.classList.add('bep-cursor-visible');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
