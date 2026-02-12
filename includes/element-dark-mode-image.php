<?php
/**
 * Bricks Dark Mode Image Element
 * 
 * Shows different images for light and dark mode, integrating with CoreFramework.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Dark_Mode_Image_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'dark-mode-image';
    public $icon = 'ti-image';

    public function get_label()
    {
        return esc_html__('Dark Mode Image', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['images'] = [
            'title' => esc_html__('Images', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['settings'] = [
            'title' => esc_html__('Settings', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // Light Mode Image
        $this->controls['lightImage'] = [
            'tab' => 'content',
            'group' => 'images',
            'label' => esc_html__('Light Mode Image', 'bricks-elements-pack'),
            'type' => 'image',
        ];

        // Dark Mode Image
        $this->controls['darkImage'] = [
            'tab' => 'content',
            'group' => 'images',
            'label' => esc_html__('Dark Mode Image', 'bricks-elements-pack'),
            'type' => 'image',
        ];

        // Alt Text
        $this->controls['altText'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Alt Text', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '',
        ];

        // Image Size
        $this->controls['imageSize'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Image Size', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'full' => 'Full',
                'large' => 'Large',
                'medium' => 'Medium',
                'thumbnail' => 'Thumbnail',
            ],
            'default' => 'full',
        ];

        // Link
        $this->controls['link'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Link', 'bricks-elements-pack'),
            'type' => 'link',
        ];

        // Dark Mode Selector (Advanced)
        $this->controls['darkModeSelector'] = [
            'tab' => 'content',
            'group' => 'settings',
            'label' => esc_html__('Dark Mode Body Class', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'cf-theme-dark',
            'description' => esc_html__('CSS class applied to body in dark mode (CoreFramework default: cf-theme-dark)', 'bricks-elements-pack'),
        ];

        // === Style Controls ===
        $this->control_groups['style_image'] = [
            'title' => esc_html__('Image Style', 'bricks-elements-pack'),
            'tab' => 'style',
        ];

        $this->controls['imageWidth'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Width', 'bricks-elements-pack'),
            'type' => 'text',
            'css' => [
                ['property' => 'width', 'selector' => 'img'],
            ],
        ];

        $this->controls['imageMaxWidth'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Max Width', 'bricks-elements-pack'),
            'type' => 'text',
            'css' => [
                ['property' => 'max-width', 'selector' => 'img'],
            ],
            'default' => '100%',
        ];

        $this->controls['imageHeight'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Height', 'bricks-elements-pack'),
            'type' => 'text',
            'css' => [
                ['property' => 'height', 'selector' => 'img'],
            ],
            'default' => 'auto',
        ];

        $this->controls['imageBorder'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Border', 'bricks-elements-pack'),
            'type' => 'border',
            'css' => [
                ['property' => 'border', 'selector' => 'img'],
            ],
        ];

        $this->controls['imageRadius'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Border Radius', 'bricks-elements-pack'),
            'type' => 'dimensions',
            'css' => [
                ['property' => 'border-radius', 'selector' => 'img'],
            ],
        ];

        $this->controls['imageShadow'] = [
            'tab' => 'style',
            'group' => 'style_image',
            'label' => esc_html__('Box Shadow', 'bricks-elements-pack'),
            'type' => 'box-shadow',
            'css' => [
                ['property' => 'box-shadow', 'selector' => 'img'],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-dark-mode-image-css');
    }

    public function render()
    {
        $settings = $this->settings;

        $light_image = $settings['lightImage'] ?? [];
        $dark_image = $settings['darkImage'] ?? [];
        $alt_text = $settings['altText'] ?? '';
        $image_size = $settings['imageSize'] ?? 'full';
        $link = $settings['link'] ?? [];
        $dark_class = $settings['darkModeSelector'] ?? 'cf-theme-dark';

        // Get image URLs
        $light_url = '';
        $dark_url = '';

        if (!empty($light_image['id'])) {
            $light_src = wp_get_attachment_image_src($light_image['id'], $image_size);
            $light_url = $light_src ? $light_src[0] : '';
        } elseif (!empty($light_image['url'])) {
            $light_url = $light_image['url'];
        }

        if (!empty($dark_image['id'])) {
            $dark_src = wp_get_attachment_image_src($dark_image['id'], $image_size);
            $dark_url = $dark_src ? $dark_src[0] : '';
        } elseif (!empty($dark_image['url'])) {
            $dark_url = $dark_image['url'];
        }

        // Root attributes
        $this->set_attribute('_root', 'class', 'dark-mode-image-wrapper');
        $this->set_attribute('_root', 'data-dark-class', $dark_class);

        // Output
        $output = '<div ' . $this->render_attributes('_root') . '>';

        // Link wrapper
        if (!empty($link['url'])) {
            $target = !empty($link['newTab']) ? ' target="_blank"' : '';
            $output .= '<a href="' . esc_url($link['url']) . '"' . $target . '>';
        }

        // Light Mode Image
        if ($light_url) {
            $output .= '<img src="' . esc_url($light_url) . '" alt="' . esc_attr($alt_text) . '" class="dm-image dm-light" />';
        }

        // Dark Mode Image
        if ($dark_url) {
            $output .= '<img src="' . esc_url($dark_url) . '" alt="' . esc_attr($alt_text) . '" class="dm-image dm-dark" />';
        }

        // Close link
        if (!empty($link['url'])) {
            $output .= '</a>';
        }

        $output .= '</div>';

        // Inline CSS for dynamic dark class selector
        $output .= '<style>
            .' . esc_attr($dark_class) . ' .dark-mode-image-wrapper .dm-light { display: none !important; }
            .' . esc_attr($dark_class) . ' .dark-mode-image-wrapper .dm-dark { display: block !important; }
        </style>';

        echo $output;
    }
}
