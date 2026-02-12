<?php
/**
 * Bricks Animated Headline Element
 * 
 * Rotating text with various animation effects
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Animated_Headline_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'animated-headline';
    public $icon = 'ti-text';

    public function get_label()
    {
        return esc_html__('Animated Headline', 'bricks-particle-js');
    }

    public function set_control_groups()
    {
        $this->control_groups['headline'] = [
            'title' => esc_html__('Headline', 'bricks-particle-js'),
            'tab' => 'content',
        ];

        $this->control_groups['animation'] = [
            'title' => esc_html__('Animation', 'bricks-particle-js'),
            'tab' => 'content',
        ];

        $this->control_groups['style_text'] = [
            'title' => esc_html__('Text Style', 'bricks-particle-js'),
            'tab' => 'style',
        ];

        $this->control_groups['style_animated'] = [
            'title' => esc_html__('Animated Text Style', 'bricks-particle-js'),
            'tab' => 'style',
        ];
    }

    public function set_controls()
    {
        // Before Text
        $this->controls['beforeText'] = [
            'tab' => 'content',
            'group' => 'headline',
            'label' => esc_html__('Before Text', 'bricks-particle-js'),
            'type' => 'text',
            'default' => 'We are ',
        ];

        // Rotating Text (one per line)
        $this->controls['rotatingText'] = [
            'tab' => 'content',
            'group' => 'headline',
            'label' => esc_html__('Rotating Text', 'bricks-particle-js'),
            'type' => 'textarea',
            'default' => "Creative\nAwesome\nProfessional",
            'description' => esc_html__('One word/phrase per line', 'bricks-particle-js'),
        ];

        // After Text
        $this->controls['afterText'] = [
            'tab' => 'content',
            'group' => 'headline',
            'label' => esc_html__('After Text', 'bricks-particle-js'),
            'type' => 'text',
            'default' => '',
        ];

        // HTML Tag
        $this->controls['htmlTag'] = [
            'tab' => 'content',
            'group' => 'headline',
            'label' => esc_html__('HTML Tag', 'bricks-particle-js'),
            'type' => 'select',
            'options' => [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'div' => 'div',
                'span' => 'span',
                'p' => 'p',
            ],
            'default' => 'h2',
        ];

        // Animation Type
        $this->controls['animationType'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Animation Type', 'bricks-particle-js'),
            'type' => 'select',
            'options' => [
                'typing' => esc_html__('Typing', 'bricks-particle-js'),
                'clip' => esc_html__('Clip', 'bricks-particle-js'),
                'flip' => esc_html__('Flip', 'bricks-particle-js'),
                'swirl' => esc_html__('Swirl', 'bricks-particle-js'),
                'blinds' => esc_html__('Blinds', 'bricks-particle-js'),
                'drop-in' => esc_html__('Drop-in', 'bricks-particle-js'),
                'wave' => esc_html__('Wave', 'bricks-particle-js'),
                'slide' => esc_html__('Slide', 'bricks-particle-js'),
                'slide-down' => esc_html__('Slide Down', 'bricks-particle-js'),
                'fade' => esc_html__('Fade', 'bricks-particle-js'),
            ],
            'default' => 'typing',
        ];

        // Duration
        $this->controls['duration'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Duration (ms)', 'bricks-particle-js'),
            'type' => 'number',
            'default' => 2500,
            'min' => 500,
            'max' => 10000,
        ];

        // Infinite Loop
        $this->controls['infiniteLoop'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Infinite Loop', 'bricks-particle-js'),
            'type' => 'checkbox',
            'default' => true,
        ];

        // Link
        $this->controls['link'] = [
            'tab' => 'content',
            'group' => 'headline',
            'label' => esc_html__('Link', 'bricks-particle-js'),
            'type' => 'link',
        ];

        // Style: Text Color
        $this->controls['textColor'] = [
            'tab' => 'style',
            'group' => 'style_text',
            'label' => esc_html__('Text Color', 'bricks-particle-js'),
            'type' => 'color',
            'css' => [
                [
                    'property' => 'color',
                    'selector' => '.bricks-animated-headline',
                ],
            ],
        ];

        // Style: Animated Text Color
        $this->controls['animatedColor'] = [
            'tab' => 'style',
            'group' => 'style_animated',
            'label' => esc_html__('Animated Text Color', 'bricks-particle-js'),
            'type' => 'color',
            'css' => [
                [
                    'property' => 'color',
                    'selector' => '.bricks-animated-text',
                ],
            ],
        ];

        // Style: Typography
        $this->controls['typography'] = [
            'tab' => 'style',
            'group' => 'style_text',
            'label' => esc_html__('Typography', 'bricks-particle-js'),
            'type' => 'typography',
            'css' => [
                [
                    'property' => 'font',
                    'selector' => '.bricks-animated-headline',
                ],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-animated-headline-css');
        wp_enqueue_script('bricks-animated-headline-js');
    }

    public function render()
    {
        $settings = $this->settings;

        $before_text = isset($settings['beforeText']) ? $settings['beforeText'] : '';
        $after_text = isset($settings['afterText']) ? $settings['afterText'] : '';
        $rotating_text = isset($settings['rotatingText']) ? $settings['rotatingText'] : '';
        $html_tag = isset($settings['htmlTag']) ? $settings['htmlTag'] : 'h2';
        $animation_type = isset($settings['animationType']) ? $settings['animationType'] : 'typing';
        $duration = isset($settings['duration']) ? intval($settings['duration']) : 2500;
        $loop = isset($settings['infiniteLoop']) && $settings['infiniteLoop'] ? 'true' : 'false';
        $link = isset($settings['link']) ? $settings['link'] : null;

        // Parse rotating words
        $words = array_filter(array_map('trim', explode("\n", $rotating_text)));
        if (empty($words)) {
            $words = ['Creative'];
        }

        // Unique ID
        $element_id = 'animated-headline-' . $this->id;

        // Set root attributes
        $this->set_attribute('_root', 'class', 'bricks-animated-headline-wrapper');
        $this->set_attribute('_root', 'id', $element_id);
        $this->set_attribute('_root', 'data-animation', $animation_type);
        $this->set_attribute('_root', 'data-duration', $duration);
        $this->set_attribute('_root', 'data-loop', $loop);

        // Build HTML
        $output = '<div ' . $this->render_attributes('_root') . '>';

        // Optional link wrapper
        if ($link && !empty($link['url'])) {
            $link_attrs = 'href="' . esc_url($link['url']) . '"';
            if (isset($link['newTab']) && $link['newTab']) {
                $link_attrs .= ' target="_blank" rel="noopener noreferrer"';
            }
            $output .= '<a ' . $link_attrs . '>';
        }

        // Headline tag
        $output .= '<' . esc_html($html_tag) . ' class="bricks-animated-headline">';

        // Before text
        if ($before_text) {
            $output .= '<span class="bricks-headline-before">' . esc_html($before_text) . '</span>';
        }

        // Animated text wrapper
        $output .= '<span class="bricks-animated-text-wrapper">';

        foreach ($words as $index => $word) {
            $active_class = $index === 0 ? ' is-visible' : '';
            $output .= '<span class="bricks-animated-text' . $active_class . '" data-index="' . $index . '">';

            // For letter-based animations, wrap each letter
            if (in_array($animation_type, ['typing', 'wave', 'blinds'])) {
                $letters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($letters as $letter) {
                    $output .= '<span class="bricks-letter">' . esc_html($letter) . '</span>';
                }
            } else {
                $output .= esc_html($word);
            }

            $output .= '</span>';
        }

        // Cursor for typing animation
        if ($animation_type === 'typing') {
            $output .= '<span class="bricks-typing-cursor">|</span>';
        }

        $output .= '</span>'; // Close animated text wrapper

        // After text
        if ($after_text) {
            $output .= '<span class="bricks-headline-after">' . esc_html($after_text) . '</span>';
        }

        $output .= '</' . esc_html($html_tag) . '>';

        // Close link if exists
        if ($link && !empty($link['url'])) {
            $output .= '</a>';
        }

        $output .= '</div>';

        echo $output;
    }
}
