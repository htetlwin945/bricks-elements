<?php
/**
 * Bricks Timeline Item Element (Child)
 * 
 * Individual timeline item with dot and nestable content area.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Timeline_Item_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'timeline-item';
    public $icon = 'ti-layout-placeholder';
    public $nestable = true;

    public function get_label()
    {
        return esc_html__('Timeline Item', 'bricks-elements-pack');
    }

    public function get_nestable_children()
    {
        return [
            [
                'name' => 'heading',
                'settings' => [
                    'text' => esc_html__('Timeline Title', 'bricks-elements-pack'),
                    'tag' => 'h4',
                ],
            ],
            [
                'name' => 'text',
                'settings' => [
                    'text' => esc_html__('Add your content here. You can include text, images, or any other elements.', 'bricks-elements-pack'),
                ],
            ],
        ];
    }

    public function set_control_groups()
    {
        $this->control_groups['dot'] = [
            'title' => esc_html__('Dot Override', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // Custom dot color (overrides parent)
        $this->controls['customDotColor'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Custom Color', 'bricks-elements-pack'),
            'type' => 'color',
            'description' => esc_html__('Override the default dot color for this item', 'bricks-elements-pack'),
        ];

        // Dot icon (optional)
        $this->controls['dotIcon'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Icon', 'bricks-elements-pack'),
            'type' => 'icon',
            'description' => esc_html__('Display an icon inside the dot', 'bricks-elements-pack'),
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-timeline-css');
    }

    public function render()
    {
        $settings = $this->settings;

        $custom_color = $settings['customDotColor'] ?? '';
        $dot_icon = $settings['dotIcon'] ?? [];

        // Root attributes
        $this->set_attribute('_root', 'class', 'bep-timeline-item');

        // Dot style override
        $dot_style = '';
        if (!empty($custom_color)) {
            $dot_style = 'background-color: ' . esc_attr($custom_color) . ';';
        }

        echo '<div ' . $this->render_attributes('_root') . '>';

        // Dot
        echo '<div class="bep-timeline-dot"' . ($dot_style ? ' style="' . $dot_style . '"' : '') . '>';

        // Icon inside dot (if set)
        if (!empty($dot_icon['icon'])) {
            echo '<i class="' . esc_attr($dot_icon['icon']) . '"></i>';
        }

        echo '</div>';

        // Content card (with arrow via ::before)
        echo '<div class="bep-timeline-card">';
        echo \Bricks\Frontend::render_children($this);
        echo '</div>';

        echo '</div>';
    }
}
