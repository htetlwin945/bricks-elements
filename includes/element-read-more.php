<?php
/**
 * Bricks Read More Element
 * 
 * Nestable container that expands/collapses content with a toggle button.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Read_More_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'read-more';
    public $icon = 'ti-angle-double-down';
    public $nestable = true;

    public function get_label()
    {
        return esc_html__('Read More (Expandable)', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['settings'] = [
            'title' => esc_html__('Settings', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['button'] = [
            'title' => esc_html__('Toggle Button', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['overlay'] = [
            'title' => esc_html__('Fade Overlay', 'bricks-elements-pack'),
            'tab' => 'style',
        ];
    }

    public function set_controls()
    {
        // === Settings ===
        $this->controls['collapsedHeight'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Collapsed Height', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '150px',
            'description' => esc_html__('Initial visible height (e.g. 150px)', 'bricks-elements-pack'),
        ];

        $this->controls['transitionDuration'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Transition Duration (ms)', 'bricks-elements-pack'),
            'type' => 'number',
            'default' => 500,
        ];

        // === Button ===
        $this->controls['readMoreText'] = [
            'tab' => 'content',
            'group' => 'button',
            'label' => esc_html__('Read More Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'Read More',
        ];

        $this->controls['readLessText'] = [
            'tab' => 'content',
            'group' => 'button',
            'label' => esc_html__('Read Less Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'Read Less',
        ];

        $this->controls['buttonAlignment'] = [
            'tab' => 'content',
            'group' => 'button',
            'label' => esc_html__('Alignment', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'flex-start' => 'Left',
                'center' => 'Center',
                'flex-end' => 'Right',
            ],
            'default' => 'center',
            'css' => [
                [
                    'property' => 'justify-content',
                    'selector' => '.read-more-button-wrapper',
                ],
            ],
        ];

        // === Styles: Overlay ===
        $this->controls['overlayColor'] = [
            'tab' => 'style',
            'group' => 'overlay',
            'label' => esc_html__('Fade Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff', // Default white fade
            'css' => [
                [
                    'property' => '--overlay-color', // CSS variable usage
                    'selector' => '.read-more-overlay',
                ],
            ],
        ];

        $this->controls['overlayHeight'] = [
            'tab' => 'style',
            'group' => 'overlay',
            'label' => esc_html__('Fade Height', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '80px',
            'css' => [
                [
                    'property' => 'height',
                    'selector' => '.read-more-overlay',
                ],
            ],
        ];

        // Button Styles (Standard Bricks Button Style)
        // Note: Bricks doesn't expose a 'button' style group easily for custom elements without complex mapping.
        // We'll add basic button styling controls here.

        $this->control_groups['style_button'] = [
            'title' => esc_html__('Button Style', 'bricks-elements-pack'),
            'tab' => 'style',
        ];

        $this->controls['btnTypography'] = [
            'tab' => 'style',
            'group' => 'style_button',
            'label' => 'Typography',
            'type' => 'typography',
            'css' => [
                ['property' => 'font', 'selector' => '.read-more-toggle'],
            ],
        ];

        $this->controls['btnBg'] = [
            'tab' => 'style',
            'group' => 'style_button',
            'label' => 'Background',
            'type' => 'background',
            'css' => [
                ['property' => 'background', 'selector' => '.read-more-toggle'],
            ],
        ];

        $this->controls['btnBorder'] = [
            'tab' => 'style',
            'group' => 'style_button',
            'label' => 'Border',
            'type' => 'border',
            'css' => [
                ['property' => 'border', 'selector' => '.read-more-toggle'],
            ],
        ];

        $this->controls['btnPadding'] = [
            'tab' => 'style',
            'group' => 'style_button',
            'label' => 'Padding',
            'type' => 'dimensions',
            'css' => [
                ['property' => 'padding', 'selector' => '.read-more-toggle'],
            ],
            'default' => [
                'top' => '10px',
                'right' => '20px',
                'bottom' => '10px',
                'left' => '20px',
            ],
        ];

        $this->controls['btnRadius'] = [
            'tab' => 'style',
            'group' => 'style_button',
            'label' => 'Border Radius',
            'type' => 'dimensions',
            'css' => [
                ['property' => 'border-radius', 'selector' => '.read-more-toggle'],
            ],
            'default' => [
                'top' => '5px',
                'right' => '5px',
                'bottom' => '5px',
                'left' => '5px',
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('bricks-read-more-js');
        wp_enqueue_style('bricks-read-more-css');
    }

    public function render()
    {
        $settings = $this->settings;

        $collapsed_height = $settings['collapsedHeight'] ?? '150px';
        $duration = $settings['transitionDuration'] ?? 500;
        $more_text = $settings['readMoreText'] ?? 'Read More';
        $less_text = $settings['readLessText'] ?? 'Read Less';

        // Root attributes
        $this->set_attribute('_root', 'class', 'read-more-container');
        $this->set_attribute('_root', 'data-duration', $duration);
        $this->set_attribute('_root', 'data-collapsed-height', $collapsed_height);

        // CSS var for collapsed height (used in JS/CSS)
        $this->set_attribute('_root', 'style', "--collapsed-height: {$collapsed_height};");

        echo '<div ' . $this->render_attributes('_root') . '>';

        // Content Wrapper
        echo '<div class="read-more-content-wrapper" style="height: ' . esc_attr($collapsed_height) . '; overflow: hidden; transition: height ' . intval($duration) . 'ms ease;">';
        echo '<div class="read-more-inner-content">';
        echo \Bricks\Frontend::render_children($this);
        echo '</div>';

        // Overlay (Fade)
        echo '<div class="read-more-overlay"></div>';
        echo '</div>'; // End content wrapper

        // Toggle Button
        echo '<div class="read-more-button-wrapper" style="display: flex; margin-top: 15px;">';
        echo '<button class="read-more-toggle" data-more="' . esc_attr($more_text) . '" data-less="' . esc_attr($less_text) . '">';
        echo esc_html($more_text);
        echo '</button>';
        echo '</div>';

        echo '</div>';
    }
}
