# Repository guidance

- This is a WordPress plugin for Bricks Builder (WordPress 5.0+, PHP 7.4+); it has no package manifest, build step, or automated test/lint/typecheck configuration. Verify changes manually in a WordPress site with Bricks active.
- `bricks-elements-pack.php` is the runtime entrypoint. Element PHP files are loaded only when explicitly listed in its `init` hook and only when `BRICKS_VERSION` is defined; register a new element there.
- Element classes live in `includes/`; their browser-side JS/CSS lives in `assets/`. Keep element settings, asset handles, and frontend behavior aligned across these locations.
- Register shared asset handles in the `wp_enqueue_scripts` hook in `bricks-elements-pack.php`; builder preview assets are separately enqueued via `bricks/builder/enqueue_scripts`.
- Do not rely blindly on README's file tree: the active Animated Headline registration is `includes/element-letter-launcher.php`, despite the separate `element-animated-headline.php` file.
- Particle runtime assets are restricted to Bricks frontend pages by `includes/class-assets.php`; test both builder preview and rendered frontend when changing asset loading.
- GSAP (including plugins) and theme-toggle CSS are CDN dependencies; `assets/particles.min.js` is a checked-in bundled dependency. Polylang and Core Framework integrations are optional.
