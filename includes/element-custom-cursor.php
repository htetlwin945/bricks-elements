<?php
/**
 * Bricks Custom Cursor Element
 * 
 * Animated custom cursor using GSAP with hover effects,
 * magnetic interactions, text labels, and mix-blend-mode.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Custom_Cursor_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'custom-cursor';
    public $icon = 'ti-hand-point-up';

    public function get_label()
    {
        return esc_html__('Custom Cursor', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['cursorStyle'] = [
            'title' => esc_html__('Cursor Style', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['hoverEffects'] = [
            'title' => esc_html__('Hover Effects', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['magnetic'] = [
            'title' => esc_html__('Magnetic Effect', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['colors'] = [
            'title' => esc_html__('Colors', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // === Cursor Style ===
        $this->controls['cursorStyle'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Style', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'dot-ring' => esc_html__('Dot + Ring', 'bricks-elements-pack'),
                'dot' => esc_html__('Dot Only', 'bricks-elements-pack'),
                'ring' => esc_html__('Ring Only', 'bricks-elements-pack'),
            ],
            'default' => 'dot-ring',
        ];

        $this->controls['dotSize'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Dot Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '8px',
            'css' => [
                ['property' => '--cc-dot-size', 'selector' => ''],
            ],
        ];

        $this->controls['ringSize'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Ring Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '40px',
            'css' => [
                ['property' => '--cc-ring-size', 'selector' => ''],
            ],
        ];

        $this->controls['ringWidth'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Ring Border Width', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '2px',
            'css' => [
                ['property' => '--cc-ring-width', 'selector' => ''],
            ],
        ];

        $this->controls['blendMode'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Mix Blend Mode (Difference)', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
            'description' => esc_html__('Creates a dramatic inversion effect over content', 'bricks-elements-pack'),
        ];

        $this->controls['followSpeed'] = [
            'tab' => 'content',
            'group' => 'cursorStyle',
            'label' => esc_html__('Follow Speed', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0.2',
            'description' => esc_html__('Seconds of lag (lower = faster). Ring always lags more than dot.', 'bricks-elements-pack'),
        ];

        // === Hover Effects ===
        $this->controls['hoverTargets'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Hover Targets (CSS Selectors)', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'a, button, input[type="submit"], .bep-cursor-hover',
            'description' => esc_html__('Elements that trigger hover effect', 'bricks-elements-pack'),
        ];

        $this->controls['hoverScale'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Hover Scale', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '1.5',
            'description' => esc_html__('Scale multiplier when hovering targets', 'bricks-elements-pack'),
        ];

        $this->controls['clickEffect'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Click Shrink Effect', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
        ];

        $this->controls['hoverText'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Show Text Label on Hover', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
            'description' => esc_html__('Shows text inside cursor ring on hover. Override per-element via data-cursor="text:View More"', 'bricks-elements-pack'),
        ];

        $this->controls['hoverTextContent'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Default Hover Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'View',
            'required' => ['hoverText', '!=', ''],
        ];

        $this->controls['hoverTextSize'] = [
            'tab' => 'content',
            'group' => 'hoverEffects',
            'label' => esc_html__('Hover Text Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '10px',
            'css' => [
                ['property' => '--cc-text-size', 'selector' => ''],
            ],
            'required' => ['hoverText', '!=', ''],
        ];

        // === Magnetic ===
        $this->controls['magneticEnabled'] = [
            'tab' => 'content',
            'group' => 'magnetic',
            'label' => esc_html__('Enable Magnetic Pull', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
            'description' => esc_html__('Add data-cursor-magnetic attribute to elements you want to have magnetic pull effect', 'bricks-elements-pack'),
        ];

        $this->controls['magneticStrength'] = [
            'tab' => 'content',
            'group' => 'magnetic',
            'label' => esc_html__('Magnetic Strength', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0.3',
            'description' => esc_html__('0 to 1, how strongly elements are pulled', 'bricks-elements-pack'),
            'required' => ['magneticEnabled', '!=', ''],
        ];

        // === Colors ===
        $this->controls['dotColor'] = [
            'tab' => 'content',
            'group' => 'colors',
            'label' => esc_html__('Dot Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#000000',
            'css' => [
                ['property' => '--cc-dot-color', 'selector' => ''],
            ],
        ];

        $this->controls['ringColor'] = [
            'tab' => 'content',
            'group' => 'colors',
            'label' => esc_html__('Ring Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => 'rgba(0,0,0,0.4)',
            'css' => [
                ['property' => '--cc-ring-color', 'selector' => ''],
            ],
        ];

        $this->controls['hoverDotColor'] = [
            'tab' => 'content',
            'group' => 'colors',
            'label' => esc_html__('Dot Color (Hover)', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => '--cc-dot-hover-color', 'selector' => ''],
            ],
        ];

        $this->controls['hoverRingColor'] = [
            'tab' => 'content',
            'group' => 'colors',
            'label' => esc_html__('Ring Color (Hover)', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => '--cc-ring-hover-color', 'selector' => ''],
            ],
        ];

        $this->controls['textColor'] = [
            'tab' => 'content',
            'group' => 'colors',
            'label' => esc_html__('Text Label Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'css' => [
                ['property' => '--cc-text-color', 'selector' => ''],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-custom-cursor-css');
        wp_enqueue_script('bricks-custom-cursor-js');
    }

    public function render()
    {
        $settings = $this->settings;

        $config = [
            'style' => $settings['cursorStyle'] ?? 'dot-ring',
            'blendMode' => !empty($settings['blendMode']),
            'followSpeed' => floatval($settings['followSpeed'] ?? 0.2),
            'hoverTargets' => $settings['hoverTargets'] ?? 'a, button, input[type="submit"], .bep-cursor-hover',
            'hoverScale' => floatval($settings['hoverScale'] ?? 1.5),
            'clickEffect' => !empty($settings['clickEffect']),
            'hoverText' => !empty($settings['hoverText']),
            'hoverTextContent' => $settings['hoverTextContent'] ?? 'View',
            'magneticEnabled' => !empty($settings['magneticEnabled']),
            'magneticStrength' => floatval($settings['magneticStrength'] ?? 0.3),
        ];

        $this->set_attribute('_root', 'class', 'bep-custom-cursor-wrapper');
        $this->set_attribute('_root', 'data-cursor-config', wp_json_encode($config));

        echo '<div ' . $this->render_attributes('_root') . '>';

        // Dot
        if ($config['style'] === 'dot-ring' || $config['style'] === 'dot') {
            $dot_blend = $config['blendMode'] ? ' bep-cursor-blend' : '';
            echo '<div class="bep-cursor-dot' . $dot_blend . '"></div>';
        }

        // Ring
        if ($config['style'] === 'dot-ring' || $config['style'] === 'ring') {
            $ring_blend = $config['blendMode'] ? ' bep-cursor-blend' : '';
            echo '<div class="bep-cursor-ring' . $ring_blend . '">';
            if ($config['hoverText']) {
                echo '<span class="bep-cursor-text">' . esc_html($config['hoverTextContent']) . '</span>';
            }
            echo '</div>';
        }

        echo '</div>';
    }
}
