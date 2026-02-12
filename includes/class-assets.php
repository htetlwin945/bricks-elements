<?php
if (!defined('ABSPATH'))
    exit;

class Bricks_Particle_JS_Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('bricks/builder/enqueue_scripts', [$this, 'enqueue_builder_scripts']);
    }

    public function enqueue_scripts()
    {
        // Enqueue only if we are on a Bricks frontend page
        if (function_exists('bricks_is_frontend') && !bricks_is_frontend()) {
            return;
        }

        wp_enqueue_script('particles-js', BRICKS_PARTICLE_JS_URL . 'assets/particles.min.js', [], '2.0.0', true);
        wp_enqueue_script('bricks-particle-init', BRICKS_PARTICLE_JS_URL . 'assets/script.js', ['particles-js'], '1.0.0', true);
        wp_enqueue_style('bricks-particle-css', BRICKS_PARTICLE_JS_URL . 'assets/main.css', [], '1.0.0');
    }

    public function enqueue_builder_scripts()
    {
        wp_enqueue_script('particles-js', BRICKS_PARTICLE_JS_URL . 'assets/particles.min.js', [], '2.0.0', true);
        wp_enqueue_script('bricks-particle-init', BRICKS_PARTICLE_JS_URL . 'assets/script.js', ['particles-js'], '1.0.0', true);
        wp_enqueue_style('bricks-particle-css', BRICKS_PARTICLE_JS_URL . 'assets/main.css', [], '1.0.0');
    }
}
