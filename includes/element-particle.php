<?php
/**
 * Bricks Particle.js Nestable Element
 * 
 * A container element with particle.js background that can hold child elements
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Particle_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'particle-background';
    public $icon = 'ti-vector';
    public $nestable = true;

    public function get_label()
    {
        return esc_html__('Particle Background', 'bricks-elements-pack');
    }

    public function get_nestable_children()
    {
        return [
            [
                'name' => 'container',
                'label' => esc_html__('Container', 'bricks-elements-pack'),
                'settings' => [
                    '_padding' => [
                        'top' => 50,
                        'right' => 50,
                        'bottom' => 50,
                        'left' => 50,
                    ],
                ],
            ],
        ];
    }

    public function set_control_groups()
    {
        $this->control_groups['layout'] = [
            'title' => esc_html__('Layout', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['particles'] = [
            'title' => esc_html__('Particles', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // === Layout Controls ===

        // HTML Tag
        $this->controls['htmlTag'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('HTML Tag', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'section' => 'section',
                'div' => 'div',
                'article' => 'article',
                'header' => 'header',
                'footer' => 'footer',
                'main' => 'main',
                'aside' => 'aside',
            ],
            'default' => 'section',
        ];

        // Min Height
        $this->controls['containerHeight'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Min Height', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '400px',
            'placeholder' => '400px',
            'css' => [
                ['property' => 'min-height', 'selector' => ''],
            ],
        ];

        // Display
        $this->controls['contentDisplay'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Display', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'block' => 'block',
                'flex' => 'flex',
                'grid' => 'grid',
            ],
            'default' => 'flex',
            'css' => [
                ['property' => 'display', 'selector' => '.bricks-particle-content'],
            ],
        ];

        // Flex Direction
        $this->controls['flexDirection'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Direction', 'bricks-elements-pack'),
            'type' => 'direction',
            'default' => 'column',
            'css' => [
                ['property' => 'flex-direction', 'selector' => '.bricks-particle-content'],
            ],
            'required' => ['contentDisplay', '=', 'flex'],
        ];

        // Flex Wrap
        $this->controls['flexWrap'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Wrap', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'nowrap' => 'nowrap',
                'wrap' => 'wrap',
                'wrap-reverse' => 'wrap-reverse',
            ],
            'default' => 'nowrap',
            'css' => [
                ['property' => 'flex-wrap', 'selector' => '.bricks-particle-content'],
            ],
            'required' => ['contentDisplay', '=', 'flex'],
        ];

        // Justify Content (Main Axis)
        $this->controls['justifyContent'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Align Main Axis', 'bricks-elements-pack'),
            'type' => 'justify-content',
            'default' => 'flex-start',
            'css' => [
                ['property' => 'justify-content', 'selector' => '.bricks-particle-content'],
            ],
            'required' => ['contentDisplay', '=', 'flex'],
        ];

        // Align Items (Cross Axis)
        $this->controls['alignItems'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Align Cross Axis', 'bricks-elements-pack'),
            'type' => 'align-items',
            'default' => 'stretch',
            'css' => [
                ['property' => 'align-items', 'selector' => '.bricks-particle-content'],
            ],
            'required' => ['contentDisplay', '=', 'flex'],
        ];

        // Gap
        $this->controls['contentGap'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Gap', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '',
            'css' => [
                ['property' => 'gap', 'selector' => '.bricks-particle-content'],
            ],
            'required' => ['contentDisplay', '!=', 'block'],
        ];

        // === Particles Controls ===

        $this->controls['particlesConfig'] = [
            'tab' => 'content',
            'group' => 'particles',
            'label' => esc_html__('Particles Config (JSON)', 'bricks-elements-pack'),
            'type' => 'code',
            'mode' => 'javascript',
            'default' => '{
  "particles": {
    "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
    "color": { "value": "#ffffff" },
    "shape": { "type": "circle" },
    "opacity": { "value": 0.5, "random": false },
    "size": { "value": 3, "random": true },
    "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
    "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
  },
  "interactivity": {
    "detect_on": "window",
    "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
    "modes": { "repulse": { "distance": 200 }, "push": { "particles_nb": 4 } }
  },
  "retina_detect": true
}',
            'description' => esc_html__('Get presets: vincentgarreau.com/particles.js', 'bricks-elements-pack'),
        ];

        $this->controls['particlesZIndex'] = [
            'tab' => 'content',
            'group' => 'particles',
            'label' => esc_html__('Particles Z-Index', 'bricks-elements-pack'),
            'type' => 'number',
            'default' => 0,
            'description' => esc_html__('Set to -1 to put particles behind content', 'bricks-elements-pack'),
        ];

        $this->controls['particlesInteractive'] = [
            'tab' => 'content',
            'group' => 'particles',
            'label' => esc_html__('Enable Mouse Interactivity', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
            'description' => esc_html__('Enable hover/click effects. Note: May block clicks on content below.', 'bricks-elements-pack'),
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('particles-js');
        wp_enqueue_style('bricks-particle-css');
    }

    public function render()
    {
        $settings = $this->settings;

        // Unique ID for particles container
        $particle_id = 'particles-' . $this->id;

        // Get settings
        $config = isset($settings['particlesConfig']) ? $settings['particlesConfig'] : '{}';
        $min_height = isset($settings['containerHeight']) ? $settings['containerHeight'] : '400px';
        $z_index = isset($settings['particlesZIndex']) ? intval($settings['particlesZIndex']) : 0;
        $interactive = isset($settings['particlesInteractive']) && $settings['particlesInteractive'] ? true : false;
        $html_tag = isset($settings['htmlTag']) ? $settings['htmlTag'] : 'section';

        // Always use pointer-events: none so content is clickable
        // Interactivity works via window-level mouse detection
        $pointer_events = 'none';

        // Set root element attributes
        $this->set_attribute('_root', 'class', 'bricks-particle-container');
        $this->set_attribute('_root', 'style', 'position: relative; min-height: ' . esc_attr($min_height) . '; overflow: hidden;');

        // Valid HTML5 tags
        $allowed_tags = ['section', 'div', 'article', 'header', 'footer', 'main', 'aside'];
        if (!in_array($html_tag, $allowed_tags)) {
            $html_tag = 'section';
        }

        // Start output
        echo '<' . esc_html($html_tag) . ' ' . $this->render_attributes('_root') . '>';

        // Particles canvas container (absolute positioned)
        echo '<div id="' . esc_attr($particle_id) . '" class="bricks-particles-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: ' . esc_attr($z_index) . '; pointer-events: ' . esc_attr($pointer_events) . ';"></div>';

        // Content wrapper (above particles)
        echo '<div class="bricks-particle-content" style="position: relative; z-index: 1; height: 100%;">';

        // Render nested children
        echo \Bricks\Frontend::render_children($this);

        echo '</div>'; // Close content wrapper
        echo '</' . esc_html($html_tag) . '>'; // Close root

        // Inline initialization script
        $config_encoded = base64_encode($config);
        ?>
        <script>
            (function () {
                var particleId = '<?php echo esc_js($particle_id); ?>';
                var configData = '<?php echo esc_js($config_encoded); ?>';

                function initParticles() {
                    if (typeof window.particlesJS === 'undefined') {
                        setTimeout(initParticles, 100);
                        return;
                    }

                    var el = document.getElementById(particleId);
                    if (!el) {
                        setTimeout(initParticles, 100);
                        return;
                    }

                    // Check if already initialized
                    if (el.querySelector('.particles-js-canvas-el')) {
                        return;
                    }

                    try {
                        var config = JSON.parse(atob(configData));
                        // Use canvas detection - we'll handle mouse position manually on parent
                        if (config.interactivity) {
                            config.interactivity.detect_on = 'canvas';
                        }
                        window.particlesJS(particleId, config);

                        // Fix mouse position: track on parent container and inject into pJS
                        var container = el.closest('.bricks-particle-container');
                        if (container && window.pJSDom && window.pJSDom.length > 0) {
                            var pJS = window.pJSDom[window.pJSDom.length - 1].pJS;
                            var canvas = el.querySelector('canvas');

                            container.addEventListener('mousemove', function (e) {
                                var rect = el.getBoundingClientRect();
                                var x = e.clientX - rect.left;
                                var y = e.clientY - rect.top;
                                
                                // Scale coordinates if canvas internal size differs from display size
                                if (canvas) {
                                    var scaleX = canvas.width / rect.width;
                                    var scaleY = canvas.height / rect.height;
                                    x *= scaleX;
                                    y *= scaleY;
                                }
                                
                                pJS.interactivity.mouse.pos_x = x;
                                pJS.interactivity.mouse.pos_y = y;
                                pJS.interactivity.status = 'mousemove';
                            });

                            container.addEventListener('mouseleave', function () {
                                pJS.interactivity.mouse.pos_x = null;
                                pJS.interactivity.mouse.pos_y = null;
                                pJS.interactivity.status = 'mouseleave';
                            });

                            container.addEventListener('click', function (e) {
                                var rect = el.getBoundingClientRect();
                                var x = e.clientX - rect.left;
                                var y = e.clientY - rect.top;
                                
                                // Scale coordinates if canvas internal size differs from display size
                                if (canvas) {
                                    var scaleX = canvas.width / rect.width;
                                    var scaleY = canvas.height / rect.height;
                                    x *= scaleX;
                                    y *= scaleY;
                                }
                                
                                pJS.interactivity.mouse.pos_x = x;
                                pJS.interactivity.mouse.pos_y = y;
                                pJS.interactivity.mouse.click_pos_x = x;
                                pJS.interactivity.mouse.click_pos_y = y;
                                pJS.interactivity.mouse.click_time = new Date().getTime();

                                // Trigger click mode (push particles, etc.)
                                if (pJS.fn && pJS.fn.modes && pJS.fn.modes.pushParticles) {
                                    pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Particles.js config error:', e);
                    }
                }

                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    setTimeout(initParticles, 10);
                } else {
                    document.addEventListener('DOMContentLoaded', initParticles);
                }
            })();
        </script>
        <?php
    }
}
