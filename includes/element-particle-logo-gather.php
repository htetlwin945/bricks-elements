<?php
/**
 * A self-contained canvas particle field which can gather into a logo.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Particle_Logo_Gather_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'particle-logo-gather';
    public $icon = 'ti-magnet';
    public $nestable = true;

    public function get_label()
    {
        return esc_html__('Particle Logo Gather', 'bricks-elements-pack');
    }

    public function get_nestable_children()
    {
        return [[
            'name' => 'container',
            'label' => esc_html__('Container', 'bricks-elements-pack'),
            'settings' => [
                '_padding' => ['top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50],
            ],
        ]];
    }

    public function set_control_groups()
    {
        $this->control_groups['layout'] = ['title' => esc_html__('Layout', 'bricks-elements-pack'), 'tab' => 'content'];
        $this->control_groups['particles'] = ['title' => esc_html__('Particles', 'bricks-elements-pack'), 'tab' => 'content'];
        $this->control_groups['gather'] = ['title' => esc_html__('Logo Gather', 'bricks-elements-pack'), 'tab' => 'content'];
    }

    public function set_controls()
    {
        $this->controls['htmlTag'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('HTML Tag', 'bricks-elements-pack'),
            'type' => 'select', 'options' => ['section' => 'section', 'div' => 'div', 'article' => 'article', 'header' => 'header', 'footer' => 'footer', 'main' => 'main', 'aside' => 'aside'], 'default' => 'section',
        ];
        $this->controls['containerHeight'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Min Height', 'bricks-elements-pack'),
            'type' => 'text', 'default' => '400px', 'css' => [['property' => 'min-height', 'selector' => '']],
        ];
        $this->controls['contentDisplay'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Display', 'bricks-elements-pack'),
            'type' => 'select', 'options' => ['block' => 'block', 'flex' => 'flex', 'grid' => 'grid'], 'default' => 'flex',
            'css' => [['property' => 'display', 'selector' => '> .plg-content']],
        ];
        $this->controls['flexDirection'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Direction', 'bricks-elements-pack'), 'type' => 'direction', 'default' => 'column',
            'css' => [['property' => 'flex-direction', 'selector' => '> .plg-content']], 'required' => ['contentDisplay', '=', 'flex'],
        ];
        $this->controls['flexWrap'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Wrap', 'bricks-elements-pack'), 'type' => 'select',
            'options' => ['nowrap' => 'nowrap', 'wrap' => 'wrap', 'wrap-reverse' => 'wrap-reverse'], 'default' => 'nowrap',
            'css' => [['property' => 'flex-wrap', 'selector' => '> .plg-content']], 'required' => ['contentDisplay', '=', 'flex'],
        ];
        $this->controls['justifyContent'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Align Main Axis', 'bricks-elements-pack'), 'type' => 'justify-content', 'default' => 'flex-start',
            'css' => [['property' => 'justify-content', 'selector' => '> .plg-content']], 'required' => ['contentDisplay', '=', 'flex'],
        ];
        $this->controls['alignItems'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Align Cross Axis', 'bricks-elements-pack'), 'type' => 'align-items', 'default' => 'stretch',
            'css' => [['property' => 'align-items', 'selector' => '> .plg-content']], 'required' => ['contentDisplay', '=', 'flex'],
        ];
        $this->controls['contentGap'] = [
            'tab' => 'content', 'group' => 'layout', 'label' => esc_html__('Gap', 'bricks-elements-pack'), 'type' => 'text', 'default' => '',
            'css' => [['property' => 'gap', 'selector' => '> .plg-content']], 'required' => ['contentDisplay', '!=', 'block'],
        ];

        $default_config = '{
  "particles": {
    "number": { "value": 120, "density": { "enable": true, "value_area": 900 } },
    "color": { "value": "#263238" }, "shape": { "type": "circle" },
    "opacity": { "value": 0.7, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.25, "sync": false } },
    "size": { "value": 3, "random": true, "anim": { "enable": false, "speed": 2, "size_min": 0.5, "sync": false } },
    "line_linked": { "enable": true, "distance": 120, "color": "#263238", "opacity": 0.28, "width": 1 },
    "move": { "enable": true, "speed": 1.5, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
  },
    "interactivity": { "detect_on": "window", "events": { "onhover": { "enable": true, "mode": ["magnetic", "repulse"] }, "onclick": { "enable": true, "mode": "push" } }, "modes": { "magnetic": { "distance": 180, "strength": 0.18 }, "grab": { "distance": 140, "line_linked": { "opacity": 0.7 } }, "repulse": { "distance": 100, "duration": 0.4 }, "bubble": { "distance": 180, "size": 8, "opacity": 0.85, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } }
  }, "retina_detect": true
}';
        $this->controls['particlesConfig'] = [
            'tab' => 'content', 'group' => 'particles', 'label' => esc_html__('Particles Config (JSON)', 'bricks-elements-pack'), 'type' => 'code', 'mode' => 'javascript', 'default' => $default_config,
            'description' => esc_html__('Particles.js-style subset. Magnetic hover uses modes.magnetic.distance and strength; image shapes and attract safely fall back.', 'bricks-elements-pack'),
        ];
        $this->controls['particlesInteractive'] = [
            'tab' => 'content', 'group' => 'particles', 'label' => esc_html__('Enable Mouse Interactivity', 'bricks-elements-pack'), 'type' => 'checkbox', 'default' => true,
        ];
        $this->controls['logoImage'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Logo Image', 'bricks-elements-pack'), 'type' => 'image',
            'description' => esc_html__('Transparent, high-contrast PNG or SVG works best for logo detail.', 'bricks-elements-pack'),
        ];
        $this->controls['centerRadius'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Center Radius', 'bricks-elements-pack'), 'type' => 'number', 'default' => 150, 'min' => 40, 'max' => 500,
        ];
        $this->controls['logoScale'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Logo Scale (%)', 'bricks-elements-pack'), 'type' => 'number', 'default' => 70, 'min' => 20, 'max' => 100,
            'description' => esc_html__('Sets the logo size within the smaller canvas dimension while preserving its aspect ratio.', 'bricks-elements-pack'),
        ];
        $this->controls['disableLogoGatherMobile'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Disable Logo Gather on Mobile', 'bricks-elements-pack'), 'type' => 'checkbox', 'default' => false,
            'description' => esc_html__('At 767px and below, keeps normal particles and hides only logo gathering.', 'bricks-elements-pack'),
        ];
        $this->controls['disableLogoGatherTablet'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Disable Logo Gather on Tablet', 'bricks-elements-pack'), 'type' => 'checkbox', 'default' => false,
            'description' => esc_html__('At 991px and below, keeps normal particles and hides only logo gathering.', 'bricks-elements-pack'),
        ];
        $this->controls['gatherDuration'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Gather Duration (ms)', 'bricks-elements-pack'), 'type' => 'number', 'default' => 3000, 'min' => 0, 'max' => 6000,
            'description' => esc_html__('3–5 seconds is recommended; 0 is immediate and shorter values use a stable 200ms minimum.', 'bricks-elements-pack'),
        ];
        $this->controls['gatherDotCount'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Gather Dot Count', 'bricks-elements-pack'), 'type' => 'number', 'default' => 260, 'min' => 1, 'max' => 600, 'step' => 10,
            'description' => esc_html__('Normal scatter uses the JSON count; extra dots exist only while gathering.', 'bricks-elements-pack'),
        ];
        $this->controls['particleSizeMultiplier'] = [
            'tab' => 'content', 'group' => 'gather', 'label' => esc_html__('Particle Size Multiplier', 'bricks-elements-pack'), 'type' => 'number', 'default' => 1, 'min' => 0.25, 'max' => 3, 'step' => 0.05,
            'description' => esc_html__('Adjusts unified canvas dot size for logo clarity.', 'bricks-elements-pack'),
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('bricks-particle-logo-gather-js');
        wp_enqueue_style('bricks-particle-logo-gather-css');
    }

    public function render()
    {
        $settings = $this->settings;
        $tag = $settings['htmlTag'] ?? 'section';
        $allowed = ['section', 'div', 'article', 'header', 'footer', 'main', 'aside'];
        if (!in_array($tag, $allowed, true)) $tag = 'section';
        $config = isset($settings['particlesConfig']) ? (string) $settings['particlesConfig'] : '{}';
        $image = isset($settings['logoImage']) && is_array($settings['logoImage']) ? $settings['logoImage'] : [];
        $url = '';
        if (!empty($image['id'])) {
            $src = wp_get_attachment_image_src((int) $image['id'], 'full');
            $url = $src ? $src[0] : '';
        } elseif (!empty($image['url'])) {
            $url = $image['url'];
        }
        $logo = [
            'url' => $url,
            'radius' => min(500, max(40, (int) ($settings['centerRadius'] ?? 150))),
            'scale' => min(100, max(20, (int) ($settings['logoScale'] ?? 70))),
            'disableMobile' => !empty($settings['disableLogoGatherMobile']),
            'disableTablet' => !empty($settings['disableLogoGatherTablet']),
            'duration' => min(6000, max(0, (int) ($settings['gatherDuration'] ?? 3000))),
            'gatherCount' => min(600, max(1, (int) ($settings['gatherDotCount'] ?? 260))),
            'sizeMultiplier' => min(3, max(0.25, (float) ($settings['particleSizeMultiplier'] ?? 1))),
            'interactive' => !isset($settings['particlesInteractive']) || !empty($settings['particlesInteractive']),
            'alt' => !empty($image['alt']) ? $image['alt'] : esc_html__('Logo', 'bricks-elements-pack'),
        ];
        $height = $settings['containerHeight'] ?? '400px';
        $id = 'plg-particles-' . $this->id;
        $this->set_attribute('_root', 'class', 'plg-particle-logo-gather');
        $this->set_attribute('_root', 'style', 'min-height:' . esc_attr($height) . ';--plg-logo-scale:' . esc_attr($logo['scale']) . '%;');
        echo '<' . esc_html($tag) . ' ' . $this->render_attributes('_root') . '>';
        echo '<canvas id="' . esc_attr($id) . '" class="plg-canvas" aria-hidden="true"></canvas>';
        if ($url) echo '<img class="plg-fallback" src="' . esc_url($url) . '" alt="' . esc_attr($logo['alt']) . '" />';
        echo '<div class="plg-content">' . \Bricks\Frontend::render_children($this) . '</div>';
        $payload = wp_json_encode(['particles' => $config, 'logo' => $logo], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        echo '<script type="application/json" class="plg-config">' . $payload . '</script>';
        echo '</' . esc_html($tag) . '>';
    }
}
