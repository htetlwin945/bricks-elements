<?php
/**
 * Bricks Timeline Element (Parent)
 * 
 * Nestable vertical timeline with connected dots, content cards, and scroll animation.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Timeline_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'timeline';
    public $icon = 'ti-layout-list-post';
    public $nestable = true;

    public function get_label()
    {
        return esc_html__('Timeline', 'bricks-elements-pack');
    }

    public function get_nestable_children()
    {
        return [
            [
                'name' => 'timeline-item',
                'label' => esc_html__('Item', 'bricks-elements-pack') . ' {item_index}',
                'settings' => [],
            ],
        ];
    }

    public function set_control_groups()
    {
        $this->control_groups['items'] = [
            'title' => esc_html__('Items', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['animation'] = [
            'title' => esc_html__('Scroll Animation', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['line'] = [
            'title' => esc_html__('Line', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['dot'] = [
            'title' => esc_html__('Dot', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['layout'] = [
            'title' => esc_html__('Layout', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['arrow'] = [
            'title' => esc_html__('Arrow', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // Repeater for nestable children
        $this->controls['_children'] = [
            'tab' => 'content',
            'group' => 'items',
            'type' => 'repeater',
            'titleProperty' => 'label',
            'items' => 'children',
        ];

        // === Animation Controls ===
        $this->controls['scrollAnimation'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Enable Scroll Animation', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
            'description' => esc_html__('Animate line and dots as you scroll', 'bricks-elements-pack'),
        ];

        $this->controls['initialActive'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Initially Active Items', 'bricks-elements-pack'),
            'type' => 'number',
            'default' => 1,
            'min' => 0,
            'max' => 20,
            'description' => esc_html__('Number of items to show as active on page load (0 = none, all hidden until scroll)', 'bricks-elements-pack'),
            'required' => ['scrollAnimation', '!=', ''],
        ];

        $this->controls['activeColor'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Active Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#0ea5e9',
            'css' => [
                ['property' => '--timeline-active-color', 'selector' => ''],
            ],
            'required' => ['scrollAnimation', '!=', ''],
        ];

        // === Line Controls ===
        $this->controls['lineColor'] = [
            'tab' => 'content',
            'group' => 'line',
            'label' => esc_html__('Track Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#cbd5e1',
            'css' => [
                ['property' => '--timeline-line-color', 'selector' => ''],
            ],
        ];

        $this->controls['lineWidth'] = [
            'tab' => 'content',
            'group' => 'line',
            'label' => esc_html__('Width', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '2px',
            'css' => [
                ['property' => '--timeline-line-width', 'selector' => ''],
            ],
        ];

        $this->controls['linePosition'] = [
            'tab' => 'content',
            'group' => 'line',
            'label' => esc_html__('Position from Left', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '8px',
            'css' => [
                ['property' => '--timeline-line-left', 'selector' => ''],
            ],
        ];

        // === Dot Controls ===
        $this->controls['dotColor'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Inactive Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#64748b',
            'css' => [
                ['property' => '--timeline-dot-color', 'selector' => ''],
            ],
            'description' => esc_html__('Color before item is scrolled into view', 'bricks-elements-pack'),
        ];

        $this->controls['dotSize'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '18px',
            'css' => [
                ['property' => '--timeline-dot-size', 'selector' => ''],
            ],
        ];

        $this->controls['dotBorder'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Border Width', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '3px',
            'css' => [
                ['property' => '--timeline-dot-border', 'selector' => ''],
            ],
        ];

        $this->controls['dotBorderColor'] = [
            'tab' => 'content',
            'group' => 'dot',
            'label' => esc_html__('Border Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'css' => [
                ['property' => '--timeline-dot-border-color', 'selector' => ''],
            ],
        ];

        // === Layout Controls ===
        $this->controls['contentGap'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Item Spacing', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '40px',
            'css' => [
                ['property' => '--timeline-item-gap', 'selector' => ''],
            ],
        ];

        $this->controls['contentPadding'] = [
            'tab' => 'content',
            'group' => 'layout',
            'label' => esc_html__('Content Padding Left', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '45px',
            'css' => [
                ['property' => '--timeline-content-padding', 'selector' => ''],
            ],
        ];

        // === Arrow Controls ===
        $this->controls['arrowSize'] = [
            'tab' => 'content',
            'group' => 'arrow',
            'label' => esc_html__('Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0px',
            'description' => esc_html__('Set a value (e.g. 10px) to show a triangle pointer on the content card', 'bricks-elements-pack'),
            'css' => [
                ['property' => '--timeline-arrow-size', 'selector' => ''],
            ],
        ];

        $this->controls['arrowColor'] = [
            'tab' => 'content',
            'group' => 'arrow',
            'label' => esc_html__('Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'description' => esc_html__('Match this with your content card background color', 'bricks-elements-pack'),
            'css' => [
                ['property' => '--timeline-arrow-color', 'selector' => ''],
            ],
        ];

        $this->controls['arrowTop'] = [
            'tab' => 'content',
            'group' => 'arrow',
            'label' => esc_html__('Position (Top)', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0px',
            'description' => esc_html__('Vertical offset from the top of the card', 'bricks-elements-pack'),
            'css' => [
                ['property' => '--timeline-arrow-top', 'selector' => ''],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-timeline-css');

        // Enqueue scroll animation scripts if enabled
        $settings = $this->settings;
        $scroll_enabled = isset($settings['scrollAnimation']) ? $settings['scrollAnimation'] : true;

        if ($scroll_enabled) {
            wp_enqueue_script('gsap');
            wp_enqueue_script('gsap-scrolltrigger');
            wp_enqueue_script('bricks-timeline-js');
        }
    }

    public function render()
    {
        $settings = $this->settings;
        $scroll_enabled = isset($settings['scrollAnimation']) ? $settings['scrollAnimation'] : true;

        $this->set_attribute('_root', 'class', 'bep-timeline');

        if ($scroll_enabled) {
            $this->set_attribute('_root', 'data-scroll-animation', 'true');
            $initial_active = isset($settings['initialActive']) ? intval($settings['initialActive']) : 1;
            $this->set_attribute('_root', 'data-initial-active', $initial_active);
        }

        echo '<div ' . $this->render_attributes('_root') . '>';

        // Static track line
        echo '<div class="bep-timeline-line"></div>';

        // Progress line (animated on scroll)
        if ($scroll_enabled) {
            echo '<div class="bep-timeline-progress"></div>';
        }

        // Render children (timeline items)
        echo \Bricks\Frontend::render_children($this);

        echo '</div>';
    }
}
