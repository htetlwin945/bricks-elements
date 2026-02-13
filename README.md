# Bricks Elements Pack

A collection of custom elements for [Bricks Builder](https://bricksbuilder.io/) — animated, interactive, and production-ready.

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple) ![Bricks](https://img.shields.io/badge/Bricks%20Builder-Required-orange)

## Elements

| Element | Description |
|---------|-------------|
| **Particle Background** | Interactive particle.js backgrounds with configurable density, colors, links, and interactivity |
| **Animated Headline** | GSAP-powered text animations with multiple effects (typing, rotating, sliding, clip, etc.) |
| **Letter Launcher** | Scatter and reassemble letter animations using GSAP and SplitText |
| **Read More** | Expandable content sections with smooth height animation |
| **Dark Mode Image** | Swap images automatically based on light/dark theme |
| **Timeline** | Vertical timeline with GSAP ScrollTrigger progress animation and animated dots |
| **Language Switcher** | Polylang integration with SVG flags (circle-flags CDN), inline & dropdown modes |
| **Theme Toggle** | 12 animated toggle styles via [theme-toggles](https://toggles.dev/) library with Core Framework integration |
| **Custom Cursor** | GSAP-powered custom cursor with hover effects, magnetic pull, mix-blend-mode, and text labels |

## Installation

1. Download or clone this repository
2. Upload the folder to `/wp-content/plugins/`
3. Activate **Bricks Elements Pack** in WordPress
4. Open Bricks Builder — elements appear in the element panel

## Dependencies

- **[Bricks Builder](https://bricksbuilder.io/)** — Required
- **[GSAP](https://gsap.com/)** — Loaded from jsDelivr CDN (v3.14.1) for Animated Headline, Letter Launcher, Timeline, and Custom Cursor
- **[particles.js](https://vincentgarreau.com/particles.js/)** — Bundled for Particle Background
- **[theme-toggles](https://toggles.dev/)** — CSS loaded from jsDelivr CDN for Theme Toggle
- **[Polylang](https://polylang.pro/)** — Optional, required only for Language Switcher
- **[Core Framework](https://coreframework.com/)** — Optional, Theme Toggle integrates with `.cf-theme-dark`

## Element Documentation

### Particle Background
Full-screen or contained particle animation. Configure particle count, color, size, line links, interactivity (hover repulse, grab, bubble), and movement direction.

### Animated Headline
Multiple animation types for headlines:
- **Typing** — Character-by-character with cursor
- **Rotating** — Words rotate in/out vertically
- **Sliding** — Words slide in from bottom
- **Clip** — Text revealed with clip mask
- **Push** — Text pushes in with 3D perspective
- **Zoom** — Scale and fade transition

### Letter Launcher
Scatter letters in random directions on scroll, then reassemble. Uses GSAP SplitText for character splitting and ScrollTrigger for scroll-based animation.

### Read More
Expandable text with configurable max height, "Read More" / "Read Less" labels, smooth GSAP height animation, and gradient fade overlay.

### Dark Mode Image
Displays different images for light and dark mode. Integrates with Core Framework's `.cf-theme-dark` class. Cross-fade transition between modes.

### Timeline
Vertical timeline with:
- Scroll-triggered progress line that fills as you scroll
- Animated dots that activate when items enter viewport
- Customizable colors, line width, dot size
- Left, right, or alternating layout

### Language Switcher
Polylang-powered language switcher with:
- SVG flags from [circle-flags](https://hatscripts.github.io/circle-flags/) CDN
- Custom SVG upload per language
- Inline horizontal/vertical or dropdown modes
- Active language indicator
- Flag + name, flag only, or name only display

### Theme Toggle
12 animated dark/light mode toggles from the [theme-toggles](https://toggles.dev/) library:

| Classic | Inner Moon | Expand | Within | Around | Dark Side |
|---------|-----------|--------|--------|--------|-----------|
| Horizon | Eclipse | Lightbulb | Dark Inner | Half Sun | Simple |

**Features:**
- Animation CSS loaded per style from jsDelivr CDN
- Integrates with Core Framework `.cf-theme-dark` on `<html>`
- Persists theme to `localStorage`
- Configurable icon size, colors, animation duration, reverse option

### Custom Cursor
GSAP-powered custom cursor replacing the default browser cursor:

**Cursor Styles:** Dot Only, Ring Only, Dot + Ring

**Effects:**
- **Smooth Follow** — `gsap.quickTo()` with configurable speed/lag
- **Sticky Magnetic** — Cursor stamps onto elements (buttons/links) and sticks (configurable size)
- **Skew Effect** — Cursor stretches based on movement velocity (droplet effect)
- **Liquid Fill** — Expanding circle fill from cursor entry point (changes bg/text color)
- **Hover Scale** — Cursor grows on links/buttons (configurable targets)
- **Click Shrink** — Shrinks on click, elastic spring-back
- **Mix Blend Mode** — `mix-blend-mode: difference` for color inversion
- **Text Label** — Shows text inside cursor on hover
- **Magnetic Pull** — Elements with `data-cursor-magnetic` move toward cursor
- **Color Change** — Per-element color override
- **Native Cursor Control** — Option to show/hide native cursor or restore it on specific elements

**Per-Element Attributes:**
```html
<!-- Liquid Fill Effect -->
<button data-cursor-liquid data-cursor-liquid-bg="#ff0000" data-cursor-liquid-text="#ffffff">
    Liquid Button
</button>

<!-- Custom text on hover -->
<div data-cursor="text:Read More">...</div>

<!-- Sticky Cursor (centers on element and resize to 80px) -->
<button data-cursor-stick="80">Stick Me</button>

<!-- Sticky Cursor (default size, usually fits content) -->
<button data-cursor-stick>...</div>

<!-- Custom color on hover -->
<div data-cursor="color:#ff0000">...</div>

<!-- Combine multiple -->
<div data-cursor="text:Drag,color:#00ff00">...</div>

<!-- Magnetic pull (requires magnetic enabled in element settings) -->
<button data-cursor-magnetic>I'm magnetic!</button>

<!-- Custom magnetic strength -->
<button data-cursor-magnetic data-cursor-magnetic-strength="0.5">Strong pull</button>

<!-- Override blend mode for a section -->
<section data-cursor-blend="exclusion">...</section>

<!-- Hide custom cursor & restore native cursor -->
<div data-cursor-hide>No cursor here</div>

<!-- Show native cursor alongside custom cursor -->
<div data-cursor-native="show">...</div>
```

**Auto-hidden on touch devices** via `@media (hover: none)`.

### Dark Mode Image
Displays different images for light and dark mode. Integrates with Core Framework's `.cf-theme-dark` class. Cross-fade transition between modes.
**New:** Supports dynamic data tags (e.g. `{site_url}`) in the Link field.

### Theme Toggle
12 animated dark/light mode toggles from the [theme-toggles](https://toggles.dev/) library.
**Features:**
- Persists theme state (`bep-theme-dark`) to `localStorage` across reloads
- Integrates with Core Framework `.cf-theme-dark`
- Synced button state on load

## File Structure

```
bricks-elements-pack/
├── bricks-elements-pack.php     # Main plugin file
├── includes/
│   ├── element-particle.php     # Particle Background
│   ├── element-animated-headline.php
│   ├── element-letter-launcher.php
│   ├── element-read-more.php
│   ├── element-dark-mode-image.php
│   ├── element-timeline.php     # Timeline (parent)
│   ├── element-timeline-item.php # Timeline Item (child)
│   ├── element-language-switcher.php
│   ├── element-theme-toggle.php
│   ├── element-custom-cursor.php
│   ├── class-controls.php
│   └── class-assets.php
├── assets/
│   ├── particles.min.js
│   ├── script.js                # Particle init
│   ├── main.css                 # Particle styles
│   ├── animated-headline.js
│   ├── animated-headline.css
│   ├── letter-launcher.js
│   ├── letter-launcher.css
│   ├── read-more.js
│   ├── read-more.css
│   ├── dark-mode-image.css
│   ├── timeline.js
│   ├── timeline.css
│   ├── language-switcher.css
│   ├── theme-toggle.js
│   ├── theme-toggle.css
│   ├── custom-cursor.js
│   └── custom-cursor.css
└── .gitignore
```

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Bricks Builder (active theme)

## Author

**Zeagwat, Inc.** — [zeagwat.com](https://zeagwat.com)

## License

Proprietary — All rights reserved.
