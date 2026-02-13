<?php
/**
 * Plugin Name: Bricks Elements Pack
 * Plugin URI: https://zeagwat.com
 * Description: Adds Particle Background, Animated Headline, Read More, Dark Mode Image, Timeline, Language Switcher, Theme Toggle, and Custom Cursor elements to Bricks Builder.
 * Version: 2.0.0
 * Author: Zeagwat, Inc.
 * Author URI: https://zeagwat.com
 * Text Domain: bricks-elements-pack
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BRICKS_ELEMENTS_PACK_PATH', plugin_dir_path(__FILE__));
define('BRICKS_ELEMENTS_PACK_URL', plugin_dir_url(__FILE__));

// Backward compatibility
define('BRICKS_PARTICLE_JS_PATH', BRICKS_ELEMENTS_PACK_PATH);
define('BRICKS_PARTICLE_JS_URL', BRICKS_ELEMENTS_PACK_URL);

/**
 * Register custom Bricks elements
 */
add_action('init', function () {
    if (!defined('BRICKS_VERSION')) {
        return;
    }

    $elements = [
        'element-particle.php',
        'element-letter-launcher.php', // Animated Headline
        'element-read-more.php',       // Read More
        'element-dark-mode-image.php', // Dark Mode Image
        'element-timeline.php',        // Timeline (Parent)
        'element-timeline-item.php',   // Timeline Item (Child)
        'element-language-switcher.php', // Language Switcher
        'element-theme-toggle.php',      // Theme Toggle
        'element-custom-cursor.php',     // Custom Cursor
    ];

    foreach ($elements as $element) {
        $element_file = BRICKS_ELEMENTS_PACK_PATH . 'includes/' . $element;
        if (file_exists($element_file)) {
            \Bricks\Elements::register_element($element_file);
        }
    }
}, 11);

/**
 * Register scripts and styles
 */
add_action('wp_enqueue_scripts', function () {
    // Particle Background
    wp_register_script('particles-js', BRICKS_ELEMENTS_PACK_URL . 'assets/particles.min.js', [], '2.0.0', true);
    wp_register_script('bricks-particle-init', BRICKS_ELEMENTS_PACK_URL . 'assets/script.js', ['particles-js'], '1.0.0', true);
    wp_register_style('bricks-particle-css', BRICKS_ELEMENTS_PACK_URL . 'assets/main.css', [], '1.0.0');

    // GSAP Core + Plugins (jsDelivr CDN - v3.14.1)
    wp_register_script('gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js', [], '3.14.1', true);
    wp_register_script('gsap-scrolltrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/ScrollTrigger.min.js', ['gsap'], '3.14.1', true);
    wp_register_script('gsap-splittext', 'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/SplitText.min.js', ['gsap'], '3.14.1', true);
    wp_register_script('gsap-textplugin', 'https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/TextPlugin.min.js', ['gsap'], '3.14.1', true);

    // Animated Headline
    wp_register_style('bricks-animated-headline-css', BRICKS_ELEMENTS_PACK_URL . 'assets/animated-headline.css', [], '1.6.0');
    wp_register_script('bricks-animated-headline-js', BRICKS_ELEMENTS_PACK_URL . 'assets/animated-headline.js', ['gsap', 'gsap-splittext'], '1.6.0', true);

    // Read More
    wp_register_script('bricks-read-more-js', BRICKS_ELEMENTS_PACK_URL . 'assets/read-more.js', [], '1.0.0', true);
    wp_register_style('bricks-read-more-css', BRICKS_ELEMENTS_PACK_URL . 'assets/read-more.css', [], '1.0.0');

    // Dark Mode Image
    wp_register_style('bricks-dark-mode-image-css', BRICKS_ELEMENTS_PACK_URL . 'assets/dark-mode-image.css', [], '1.0.0');

    // Timeline
    wp_register_style('bricks-timeline-css', BRICKS_ELEMENTS_PACK_URL . 'assets/timeline.css', [], '1.6.0');
    wp_register_script('bricks-timeline-js', BRICKS_ELEMENTS_PACK_URL . 'assets/timeline.js', ['gsap', 'gsap-scrolltrigger'], '1.5.0', true);

    // Language Switcher
    wp_register_style('bricks-language-switcher-css', BRICKS_ELEMENTS_PACK_URL . 'assets/language-switcher.css', [], '1.0.0');

    // Theme Toggle
    wp_register_style('bricks-theme-toggle-css', BRICKS_ELEMENTS_PACK_URL . 'assets/theme-toggle.css', [], '1.0.0');
    wp_register_script('bricks-theme-toggle-js', BRICKS_ELEMENTS_PACK_URL . 'assets/theme-toggle.js', [], '1.0.0', true);

    // Custom Cursor
    wp_register_style('bricks-custom-cursor-css', BRICKS_ELEMENTS_PACK_URL . 'assets/custom-cursor.css', [], '2.3.0');
    wp_register_script('bricks-custom-cursor-js', BRICKS_ELEMENTS_PACK_URL . 'assets/custom-cursor.js', ['gsap'], '2.4.3', true);
});

/**
 * Enqueue in Bricks builder
 */
add_action('bricks/builder/enqueue_scripts', function () {
    // Particle
    wp_enqueue_script('particles-js');
    wp_enqueue_script('bricks-particle-init');
    wp_enqueue_style('bricks-particle-css');

    // GSAP + Plugins
    wp_enqueue_script('gsap');
    wp_enqueue_script('gsap-scrolltrigger');
    wp_enqueue_script('gsap-splittext');

    // Animated Headline
    wp_enqueue_style('bricks-animated-headline-css');
    wp_enqueue_script('bricks-animated-headline-js');

    // Read More
    wp_enqueue_script('bricks-read-more-js');
    wp_enqueue_style('bricks-read-more-css');

    // Dark Mode Image
    wp_enqueue_style('bricks-dark-mode-image-css');

    // Timeline
    wp_enqueue_style('bricks-timeline-css');
    wp_enqueue_script('bricks-timeline-js');
});
