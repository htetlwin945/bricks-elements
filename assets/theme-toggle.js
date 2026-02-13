/**
 * Theme Toggle - Core Framework Integration
 * Toggles .cf-theme-dark on <html> and syncs with localStorage.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'bep-theme-dark';

    function isDark() {
        return document.documentElement.classList.contains('cf-theme-dark');
    }

    function syncToggles() {
        var dark = isDark();
        var toggles = document.querySelectorAll('.bep-theme-toggle');
        toggles.forEach(function (btn) {
            if (dark) {
                btn.classList.add('theme-toggle--toggled');
            } else {
                btn.classList.remove('theme-toggle--toggled');
            }
        });
    }

    function handleClick() {
        var html = document.documentElement;
        html.classList.toggle('cf-theme-dark');

        var dark = isDark();
        localStorage.setItem(STORAGE_KEY, dark ? 'true' : 'false');

        syncToggles();
    }

    function init() {
        // Check localStorage for saved preference
        var savedTheme = localStorage.getItem(STORAGE_KEY);
        var html = document.documentElement;

        if (savedTheme === 'true') {
            html.classList.add('cf-theme-dark');
        } else if (savedTheme === 'false') {
            html.classList.remove('cf-theme-dark');
        }
        // If no saved preference, we leave it as is (default)

        // Sync toggle state with current theme on load
        syncToggles();

        // Bind click handlers
        var toggles = document.querySelectorAll('.bep-theme-toggle');
        toggles.forEach(function (btn) {
            btn.addEventListener('click', handleClick);
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Also observe class changes on <html> for external dark mode toggles (e.g. Core Framework's own toggle)
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.attributeName === 'class') {
                syncToggles();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
})();
