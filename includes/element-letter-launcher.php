<?php
/**
 * Bricks Animated Headline Element (GSAP Powered)
 * 
 * Multiple animation styles: Typing, Clip, Flip, Swirl, Blinds, Drop-in, Wave, Slide, Slide Down
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Letter_Launcher_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'letter-launcher';
    public $icon = 'ti-text';

    public function get_label()
    {
        return esc_html__('Animated Headline', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['content'] = [
            'title' => esc_html__('Content', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['animation'] = [
            'title' => esc_html__('Animation', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['style_text'] = [
            'title' => esc_html__('Static Text', 'bricks-elements-pack'),
            'tab' => 'style',
        ];

        $this->control_groups['style_animated'] = [
            'title' => esc_html__('Rotating Text', 'bricks-elements-pack'),
            'tab' => 'style',
        ];
    }

    public function set_controls()
    {
        // Animation Type
        $this->controls['animationType'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Animation Type', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'typing' => esc_html__('Typing', 'bricks-elements-pack'),
                'clip' => esc_html__('Clip', 'bricks-elements-pack'),
                'flip' => esc_html__('Flip', 'bricks-elements-pack'),
                'swirl' => esc_html__('Swirl', 'bricks-elements-pack'),
                'blinds' => esc_html__('Blinds', 'bricks-elements-pack'),
                'drop-in' => esc_html__('Drop-in', 'bricks-elements-pack'),
                'wave' => esc_html__('Wave', 'bricks-elements-pack'),
                'slide' => esc_html__('Slide', 'bricks-elements-pack'),
                'slide-down' => esc_html__('Slide Down', 'bricks-elements-pack'),
            ],
            'default' => 'typing',
        ];

        // Before Text
        $this->controls['beforeText'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Before Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'We are ',
        ];

        // Rotating Text
        $this->controls['rotatingText'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Rotating Text', 'bricks-elements-pack'),
            'type' => 'textarea',
            'default' => "Creative\nInnovative\nExpert",
            'description' => esc_html__('One word/phrase per line.', 'bricks-elements-pack'),
        ];

        // After Text
        $this->controls['afterText'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('After Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '',
        ];

        // HTML Tag
        $this->controls['htmlTag'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('HTML Tag', 'bricks-elements-pack'),
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
            'default' => 'h3',
        ];

        // Link
        $this->controls['link'] = [
            'tab' => 'content',
            'group' => 'content',
            'label' => esc_html__('Link', 'bricks-elements-pack'),
            'type' => 'link',
        ];

        // === Animation Settings ===

        // Duration
        $this->controls['duration'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Duration (ms)', 'bricks-elements-pack'),
            'type' => 'number',
            'default' => 2500,
            'step' => 100,
            'min' => 500,
            'max' => 10000,
        ];

        // Infinite Loop
        $this->controls['loop'] = [
            'tab' => 'content',
            'group' => 'animation',
            'label' => esc_html__('Infinite Loop', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
        ];

        // === Styles: Static Text ===

        $this->controls['typography'] = [
            'tab' => 'style',
            'group' => 'style_text',
            'label' => esc_html__('Typography', 'bricks-elements-pack'),
            'type' => 'typography',
            'css' => [
                ['property' => 'font', 'selector' => '.animated-headline-text'],
            ],
            'default' => [
                'font-size' => '32px',
                'font-weight' => '700',
            ],
        ];

        $this->controls['textColor'] = [
            'tab' => 'style',
            'group' => 'style_text',
            'label' => esc_html__('Text Color', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => 'color', 'selector' => '.animated-headline-text'],
            ],
        ];

        $this->controls['textShadow'] = [
            'tab' => 'style',
            'group' => 'style_text',
            'label' => esc_html__('Text Shadow', 'bricks-elements-pack'),
            'type' => 'text-shadow',
            'css' => [
                ['property' => 'text-shadow', 'selector' => '.animated-headline-text'],
            ],
        ];

        // === Styles: Rotating Text ===

        $this->controls['animatedTypography'] = [
            'tab' => 'style',
            'group' => 'style_animated',
            'label' => esc_html__('Typography', 'bricks-elements-pack'),
            'type' => 'typography',
            'css' => [
                ['property' => 'font', 'selector' => '.ah-phrase'],
                ['property' => 'font', 'selector' => '.ah-char'], // Ensure it hits chars too
            ],
        ];

        $this->controls['animatedColor'] = [
            'tab' => 'style',
            'group' => 'style_animated',
            'label' => esc_html__('Text Color', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => 'color', 'selector' => '.ah-phrase'],
                ['property' => 'color', 'selector' => '.ah-char'],
            ],
        ];

        $this->controls['cursorColor'] = [
            'tab' => 'style',
            'group' => 'style_animated',
            'label' => esc_html__('Cursor Color', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => 'color', 'selector' => '.ah-cursor'],
            ],
            'required' => ['animationType', '=', 'typing'],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('gsap');
        wp_enqueue_style('bricks-animated-headline-css');
        wp_enqueue_script('bricks-animated-headline-js');
    }

    public function render()
    {
        $settings = $this->settings;

        $animation_type = $settings['animationType'] ?? 'typing';
        $before_text = $settings['beforeText'] ?? '';
        $after_text = $settings['afterText'] ?? '';
        $rotating_text = $settings['rotatingText'] ?? '';
        $html_tag = $settings['htmlTag'] ?? 'h3';
        $duration = intval($settings['duration'] ?? 2500);
        $loop = !empty($settings['loop']) ? 'true' : 'false';
        $link = $settings['link'] ?? [];

        // Letter-based animations
        $letter_animations = ['typing', 'blinds', 'wave'];
        $use_letters = in_array($animation_type, $letter_animations);

        // Parse phrases
        $phrases = array_filter(array_map('trim', explode("\n", $rotating_text)));
        if (empty($phrases)) {
            $phrases = ['Animated'];
        }

        // Data attributes
        // IMPORTANT: Removed explicit 'id' attribute to allow Bricks to handle the element ID
        // for proper CSS selector matching
        $this->set_attribute('_root', 'class', 'animated-headline-wrapper');
        $this->set_attribute('_root', 'data-animation', $animation_type);
        $this->set_attribute('_root', 'data-duration', $duration);
        $this->set_attribute('_root', 'data-loop', $loop);
        $this->set_attribute('_root', 'data-use-letters', $use_letters ? 'true' : 'false');

        // Build HTML
        $output = '<div ' . $this->render_attributes('_root') . '>';

        // Link wrapper if needed
        if (!empty($link['url'])) {
            $output .= '<a href="' . esc_url($link['url']) . '"' . (!empty($link['newTab']) ? ' target="_blank"' : '') . '>';
        }

        $output .= '<' . esc_html($html_tag) . ' class="animated-headline-text">';

        if ($before_text) {
            $output .= '<span class="ah-before">' . esc_html($before_text) . '</span>';
        }

        $output .= '<span class="ah-phrases-wrapper">';

        foreach ($phrases as $index => $phrase) {
            $active = $index === 0 ? ' is-active' : '';
            $output .= '<span class="ah-phrase' . $active . '" data-index="' . $index . '">';

            // For letter-based animations, wrap each character
            if ($use_letters) {
                // Split multi-byte strings correctly
                $letters = preg_split('//u', $phrase, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($letters as $li => $letter) {
                    $char = $letter === ' ' ? '&nbsp;' : esc_html($letter);
                    $output .= '<span class="ah-char">' . $char . '</span>';
                }
            } else {
                $output .= esc_html($phrase);
            }

            $output .= '</span>';
        }

        $output .= '</span>';

        // Typing cursor
        if ($animation_type === 'typing') {
            $output .= '<span class="ah-cursor">|</span>';
        }

        if ($after_text) {
            $output .= '<span class="ah-after">' . esc_html($after_text) . '</span>';
        }

        $output .= '</' . esc_html($html_tag) . '>';

        if (!empty($link['url'])) {
            $output .= '</a>';
        }

        $output .= '</div>';

        echo $output;
    }
}
