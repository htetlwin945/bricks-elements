/**
 * Creative Button - Advanced Hover Effects
 */

(function () {
    'use strict';

    function initCreativeButtons() {
        var buttons = document.querySelectorAll('.bep-creative-button');

        buttons.forEach(function (btn) {
            if (btn._bepInitialized) return;
            btn._bepInitialized = true;

            var effectType = btn.getAttribute('data-effect') || 'liquid';
            var ease = btn.getAttribute('data-ease') || 'expo.out';

            // Get duration from CSS variable (default 0.6s)
            var style = getComputedStyle(btn);
            var duration = parseFloat(style.getPropertyValue('--bep-liquid-duration')) || 0.6;
            var textEl = btn.querySelector('.bep-btn-text');

            btn.addEventListener('mouseenter', function (e) {
                // === Liquid Effect ===
                if (effectType === 'liquid') {
                    // Coordinate calculation
                    var rect = btn.getBoundingClientRect();
                    var x = e.clientX - rect.left;
                    var y = e.clientY - rect.top;

                    // Create fill element
                    var fill = document.createElement('span');
                    fill.classList.add('bep-cb-liquid-fill');
                    fill.style.left = x + 'px';
                    fill.style.top = y + 'px';

                    // Fallback color if var is missing
                    if (!getComputedStyle(btn).getPropertyValue('--bep-liquid-bg')) {
                        fill.style.backgroundColor = '#000';
                    }

                    btn.appendChild(fill);

                    // Calculate radius to cover furthest corner
                    var distToTL = Math.hypot(x, y);
                    var distToTR = Math.hypot(rect.width - x, y);
                    var distToBL = Math.hypot(x, rect.height - y);
                    var distToBR = Math.hypot(rect.width - x, rect.height - y);
                    var radius = Math.max(distToTL, distToTR, distToBL, distToBR);
                    var diameter = radius * 2;

                    if (diameter === 0) diameter = 200; // Fallback

                    // Animate Expansion
                    if (typeof gsap !== 'undefined') {
                        gsap.fromTo(fill,
                            { width: diameter, height: diameter, scale: 0, opacity: 1 },
                            { scale: 1, duration: duration, ease: ease }
                        );

                        // Animate Text Color
                        if (textEl) {
                            if (!btn._originalColor) {
                                btn._originalColor = style.color;
                            }
                            var hoverColor = getComputedStyle(btn).getPropertyValue('--bep-liquid-text').trim();
                            if (hoverColor) {
                                gsap.to(btn, { color: hoverColor, duration: 0.3, overwrite: 'auto' });
                            }
                        }
                    } else {
                        console.error('GSAP not found');
                    }
                }
            });

            btn.addEventListener('mouseleave', function (e) {
                // === Liquid Effect Exit ===
                if (effectType === 'liquid') {
                    var fill = btn.querySelector('.bep-cb-liquid-fill');
                    if (fill) {
                        // Calculate exit point
                        var rect = btn.getBoundingClientRect();
                        var x = e.clientX - rect.left;
                        var y = e.clientY - rect.top;

                        // Animate shrinking to exit point
                        gsap.to(fill, {
                            left: x,
                            top: y,
                            scale: 0,
                            width: 0, // Ensure it shrinks to 0 dimensionally too
                            height: 0,
                            opacity: 0, // Fade out slightly at end
                            duration: duration,
                            ease: ease,
                            onComplete: function () {
                                fill.remove();
                            }
                        });
                    }

                    // Revert Text Color
                    if (textEl) {
                        gsap.to(btn, { color: btn._originalColor || '', duration: 0.3, overwrite: 'auto' });
                    }
                }
            });
        });
    }

    // Initialize on load and Bricks AJAX
    document.addEventListener('DOMContentLoaded', initCreativeButtons);
    // window.addEventListener('load', initCreativeButtons); // Backup

    // Bricks Builder/AJAX events
    window.addEventListener('bricks/ajax/load', initCreativeButtons);
    // If inside builder iframe
    if (window.bricksIsFrontend) {
        initCreativeButtons();
    }
})();
