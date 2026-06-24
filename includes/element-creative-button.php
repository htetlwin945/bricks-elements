<?php
/**
 * Bricks Creative Button Element
 * 
 * A standalone button element with advanced directional liquid fill effects.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Creative_Button_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'creative-button';
    public $icon = 'ti-paint-bucket'; // Creative icon

    public function get_label()
    {
        return esc_html__('Creative Button', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['content'] = [
            'title' => esc_html__('Content', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['settings'] = [
            'title' => esc_html__('Effect Settings', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['cursor'] = [
            'title' => esc_html__('Cursor', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // === Content ===
        $this->controls['text'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'Click Me',
        ];

        $this->controls['link'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Link', 'bricks-elements-pack'),
            'type' => 'link',
        ];

        // === Effect Settings ===
        $this->controls['effectType'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Effect Type', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'liquid' => esc_html__('Liquid Fill (Blob)', 'bricks-elements-pack'),
            ],
            'default' => 'liquid',
        ];

        $this->controls['fillColor'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Liquid Fill Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#000000',
            'css' => [
                ['property' => '--bep-liquid-bg', 'selector' => '.bep-creative-button'],
            ],
        ];

        $this->controls['textHoverColor'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Text Hover Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'css' => [
                ['property' => '--bep-liquid-text', 'selector' => '.bep-creative-button'],
            ],
        ];

        $this->controls['duration'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Duration (s)', 'bricks-elements-pack'),
            'type' => 'number',
            'default' => 0.6,
            'step' => 0.1,
            'min' => 0.1,
            'max' => 5,
            'css' => [
                ['property' => '--bep-liquid-duration', 'selector' => '.bep-creative-button', 'unit' => 's'],
            ],
        ];

        $this->controls['easing'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Easing', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'power1.out' => 'Power1 Out',
                'power2.out' => 'Power2 Out',
                'power3.out' => 'Power3 Out',
                'expo.out' => 'Expo Out (Liquid)',
                'elastic.out' => 'Elastic Out',
                'circ.out' => 'Circ Out',
            ],
            'default' => 'expo.out',
        ];

        // === Cursor Settings ===
        $this->controls['cursorAction'] = [
            'tab' => 'content',
            'group' => 'cursor',
            'label' => esc_html__('On Hover', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'none' => esc_html__('None', 'bricks-elements-pack'),
                'hide' => esc_html__('Hide Custom Cursor', 'bricks-elements-pack'),
                'shrink' => esc_html__('Shrink Custom Cursor', 'bricks-elements-pack'),
            ],
            'default' => 'shrink',
        ];

        // === Typography ===
        $this->controls['typography'] = [
            'tab' => 'style',
            'label' => esc_html__('Typography', 'bricks-elements-pack'),
            'type' => 'typography',
            'css' => [
                ['property' => 'typography', 'selector' => '.bep-creative-button'],
            ],
        ];

        // === Background/Border ===
        $this->controls['background'] = [
            'tab' => 'style',
            'label' => esc_html__('Background', 'bricks-elements-pack'),
            'type' => 'background',
            'css' => [
                ['property' => 'background', 'selector' => '.bep-creative-button'],
            ],
        ];

        $this->controls['border'] = [
            'tab' => 'style',
            'label' => esc_html__('Border', 'bricks-elements-pack'),
            'type' => 'border',
            'css' => [
                ['property' => 'border', 'selector' => '.bep-creative-button'],
            ],
        ];

        $this->controls['boxShadow'] = [
            'tab' => 'style',
            'label' => esc_html__('Box Shadow', 'bricks-elements-pack'),
            'type' => 'box-shadow',
            'css' => [
                ['property' => 'box-shadow', 'selector' => '.bep-creative-button'],
            ],
        ];

        $this->controls['padding'] = [
            'tab' => 'style',
            'label' => esc_html__('Padding', 'bricks-elements-pack'),
            'type' => 'dimensions',
            'css' => [
                ['property' => 'padding', 'selector' => '.bep-creative-button'],
            ],
            'default' => [
                'top' => '15px',
                'right' => '30px',
                'bottom' => '15px',
                'left' => '30px',
            ],
        ];
    }

    public function render()
    {
        $settings = $this->settings;
        $text = isset($settings['text']) ? $settings['text'] : 'Click Me';
        $link = isset($settings['link']) ? $settings['link'] : [];
        $effectType = isset($settings['effectType']) ? $settings['effectType'] : 'liquid';
        $easing = isset($settings['easing']) ? $settings['easing'] : 'expo.out';
        $cursorAction = isset($settings['cursorAction']) ? $settings['cursorAction'] : 'shrink';

        // Attributes
        $this->set_attribute('_root', 'class', 'bep-creative-button-wrapper');
        $this->set_attribute('button', 'class', 'bep-creative-button');
        $this->set_attribute('button', 'data-effect', $effectType);
        $this->set_attribute('button', 'data-ease', $easing);

        if ($cursorAction === 'shrink') {
            $this->set_attribute('button', 'data-cursor', 'scale:0');
        } elseif ($cursorAction === 'hide') {
            $this->set_attribute('button', 'data-cursor-hide', '');
        }

        // Link
        if (isset($link['type']) && $link['type'] === 'external') {
            $this->set_attribute('button', 'href', $link['url']);
            if (isset($link['target']) && $link['target']) {
                $this->set_attribute('button', 'target', $link['target']);
            }
            if (isset($link['nofollow']) && $link['nofollow']) {
                $this->set_attribute('button', 'rel', 'nofollow');
            }
            $tag = 'a';
        } elseif (isset($link['type']) && $link['type'] === 'internal' && isset($link['url'])) {
            $this->set_attribute('button', 'href', $link['url']);
            $tag = 'a';
        } else {
            $tag = 'button';
        }

        echo "<div " . $this->render_attributes('_root') . ">";
        echo "<{$tag} " . $this->render_attributes('button') . ">";
        echo '<span class="bep-btn-text">' . esc_html($text) . '</span>';
        echo "</{$tag}>";
        echo "</div>";
    }

    public static function custom_enqueue_scripts()
    {
        wp_enqueue_style('bricks-creative-button-css');
        wp_enqueue_script('bricks-creative-button-js');
    }
}
