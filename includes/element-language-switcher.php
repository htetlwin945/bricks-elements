<?php
/**
 * Bricks Language Switcher Element
 * 
 * Custom language switcher with SVG flags for Polylang.
 * Supports built-in circle-flags or custom SVG uploads per language.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bricks_Language_Switcher_Element extends \Bricks\Element
{
    public $category = 'general';
    public $name = 'language-switcher';
    public $icon = 'ti-world';

    /**
     * Common language slug → country code mapping
     * Polylang uses language codes (en, nl, my) but flags need country codes (gb, nl, mm)
     */
    private $slug_to_country = [
        'en' => 'gb',
        'my' => 'mm',
        'ja' => 'jp',
        'ko' => 'kr',
        'zh' => 'cn',
        'cs' => 'cz',
        'da' => 'dk',
        'el' => 'gr',
        'et' => 'ee',
        'he' => 'il',
        'hi' => 'in',
        'uk' => 'ua',
        'vi' => 'vn',
        'ar' => 'sa',
        'fa' => 'ir',
        'sv' => 'se',
        'sl' => 'si',
        'sq' => 'al',
        'sr' => 'rs',
        'ms' => 'my',
        'nb' => 'no',
        'nn' => 'no',
        'ca' => 'es',
        'eu' => 'es',
        'gl' => 'es',
        'ka' => 'ge',
        'hy' => 'am',
        'bn' => 'bd',
        'ta' => 'in',
        'te' => 'in',
        'ur' => 'pk',
        'km' => 'kh',
        'lo' => 'la',
        'ne' => 'np',
        'si' => 'lk',
        'sw' => 'tz',
    ];

    public function get_label()
    {
        return esc_html__('Language Switcher', 'bricks-elements-pack');
    }

    public function set_control_groups()
    {
        $this->control_groups['display'] = [
            'title' => esc_html__('Display', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['flags'] = [
            'title' => esc_html__('Flags', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['customFlags'] = [
            'title' => esc_html__('Custom Flags', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['itemStyle'] = [
            'title' => esc_html__('Item Style', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['dropdown'] = [
            'title' => esc_html__('Dropdown', 'bricks-elements-pack'),
            'tab' => 'content',
        ];

        $this->control_groups['loading'] = [
            'title' => esc_html__('Loading Animation', 'bricks-elements-pack'),
            'tab' => 'content',
        ];
    }

    public function set_controls()
    {
        // === Display Controls ===
        $this->controls['showFlags'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Show Flags', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
        ];

        $this->controls['showNames'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Show Names', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => true,
        ];

        $this->controls['nameDisplay'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Name Format', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'name' => esc_html__('Full Name', 'bricks-elements-pack'),
                'slug' => esc_html__('Slug (en, nl)', 'bricks-elements-pack'),
                'slug_upper' => esc_html__('Slug Uppercase (EN, NL)', 'bricks-elements-pack'),
            ],
            'default' => 'name',
            'required' => ['showNames', '!=', ''],
        ];

        $this->controls['hideCurrent'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Hide Current Language', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
        ];

        $this->controls['hideNoTranslation'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Hide If No Translation', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
        ];

        $this->controls['direction'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Direction', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'row' => esc_html__('Horizontal', 'bricks-elements-pack'),
                'column' => esc_html__('Vertical', 'bricks-elements-pack'),
            ],
            'default' => 'row',
            'css' => [
                ['property' => '--ls-direction', 'selector' => ''],
            ],
        ];

        $this->controls['gap'] = [
            'tab' => 'content',
            'group' => 'display',
            'label' => esc_html__('Gap', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '8px',
            'css' => [
                ['property' => '--ls-gap', 'selector' => ''],
            ],
        ];

        // === Flag Controls ===
        $this->controls['flagSize'] = [
            'tab' => 'content',
            'group' => 'flags',
            'label' => esc_html__('Flag Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '24px',
            'css' => [
                ['property' => '--ls-flag-size', 'selector' => ''],
            ],
        ];

        $this->controls['flagBorderRadius'] = [
            'tab' => 'content',
            'group' => 'flags',
            'label' => esc_html__('Border Radius', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '50%',
            'description' => esc_html__('50% = circle, 0 = square, 4px = rounded', 'bricks-elements-pack'),
            'css' => [
                ['property' => '--ls-flag-radius', 'selector' => ''],
            ],
        ];

        $this->controls['flagBorder'] = [
            'tab' => 'content',
            'group' => 'flags',
            'label' => esc_html__('Flag Border', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => 'none',
            'description' => esc_html__('e.g. 1px solid #ccc', 'bricks-elements-pack'),
            'css' => [
                ['property' => '--ls-flag-border', 'selector' => ''],
            ],
        ];

        // === Custom Flags ===
        $this->controls['customFlagsInfo'] = [
            'tab' => 'content',
            'group' => 'customFlags',
            'type' => 'info',
            'content' => esc_html__('Upload custom flag images to override the built-in SVG for specific languages. Use the Polylang language slug (e.g. en, nl, my).', 'bricks-elements-pack'),
        ];

        $this->controls['customFlags'] = [
            'tab' => 'content',
            'group' => 'customFlags',
            'label' => esc_html__('Custom Flag Overrides', 'bricks-elements-pack'),
            'type' => 'repeater',
            'titleProperty' => 'langSlug',
            'fields' => [
                'langSlug' => [
                    'label' => esc_html__('Language Slug', 'bricks-elements-pack'),
                    'type' => 'text',
                    'description' => esc_html__('Polylang slug, e.g. en, nl, my', 'bricks-elements-pack'),
                ],
                'flagImage' => [
                    'label' => esc_html__('Flag Image', 'bricks-elements-pack'),
                    'type' => 'image',
                    'description' => esc_html__('Upload SVG or PNG flag', 'bricks-elements-pack'),
                ],
            ],
            'default' => [],
        ];

        // === Item Style Controls ===
        $this->controls['itemPadding'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Item Padding', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '6px 12px',
            'css' => [
                ['property' => '--ls-item-padding', 'selector' => ''],
            ],
        ];

        $this->controls['itemBgColor'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Background', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => 'transparent',
            'css' => [
                ['property' => '--ls-item-bg', 'selector' => ''],
            ],
        ];

        $this->controls['itemHoverBgColor'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Background (Hover)', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => '--ls-item-hover-bg', 'selector' => ''],
            ],
        ];

        $this->controls['textColor'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Text Color', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => '--ls-text-color', 'selector' => ''],
            ],
        ];

        $this->controls['hoverTextColor'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Text Color (Hover)', 'bricks-elements-pack'),
            'type' => 'color',
            'css' => [
                ['property' => '--ls-hover-text-color', 'selector' => ''],
            ],
        ];

        $this->controls['fontSize'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Font Size', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '14px',
            'css' => [
                ['property' => '--ls-font-size', 'selector' => ''],
            ],
        ];

        $this->controls['itemBorderRadius'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Item Border Radius', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '6px',
            'css' => [
                ['property' => '--ls-item-radius', 'selector' => ''],
            ],
        ];

        $this->controls['activeIndicator'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Active Indicator', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'none' => esc_html__('None', 'bricks-elements-pack'),
                'underline' => esc_html__('Underline', 'bricks-elements-pack'),
                'background' => esc_html__('Background', 'bricks-elements-pack'),
                'border' => esc_html__('Border', 'bricks-elements-pack'),
                'bold' => esc_html__('Bold Text', 'bricks-elements-pack'),
            ],
            'default' => 'none',
        ];

        $this->controls['activeColor'] = [
            'tab' => 'content',
            'group' => 'itemStyle',
            'label' => esc_html__('Active Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#0ea5e9',
            'css' => [
                ['property' => '--ls-active-color', 'selector' => ''],
            ],
            'required' => ['activeIndicator', '!=', 'none'],
        ];

        // === Dropdown Controls ===
        $this->controls['enableDropdown'] = [
            'tab' => 'content',
            'group' => 'dropdown',
            'label' => esc_html__('Enable Dropdown', 'bricks-elements-pack'),
            'type' => 'checkbox',
            'default' => false,
            'description' => esc_html__('Show current language as trigger, others in dropdown on hover', 'bricks-elements-pack'),
        ];

        $this->controls['dropdownBgColor'] = [
            'tab' => 'content',
            'group' => 'dropdown',
            'label' => esc_html__('Dropdown Background', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'css' => [
                ['property' => '--ls-dropdown-bg', 'selector' => ''],
            ],
            'required' => ['enableDropdown', '!=', ''],
        ];

        $this->controls['dropdownShadow'] = [
            'tab' => 'content',
            'group' => 'dropdown',
            'label' => esc_html__('Dropdown Shadow', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0 4px 12px rgba(0,0,0,0.15)',
            'css' => [
                ['property' => '--ls-dropdown-shadow', 'selector' => ''],
            ],
            'required' => ['enableDropdown', '!=', ''],
        ];

        $this->controls['dropdownPadding'] = [
            'tab' => 'content',
            'group' => 'dropdown',
            'label' => esc_html__('Dropdown Padding', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '4px',
            'css' => [
                ['property' => '--ls-dropdown-padding', 'selector' => ''],
            ],
            'required' => ['enableDropdown', '!=', ''],
        ];

        $this->controls['dropdownRadius'] = [
            'tab' => 'content',
            'group' => 'dropdown',
            'label' => esc_html__('Dropdown Border Radius', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '8px',
            'css' => [
                ['property' => '--ls-dropdown-radius', 'selector' => ''],
            ],
            'required' => ['enableDropdown', '!=', ''],
        ];


        // === Loading Animation Controls ===
        $this->controls['loadingType'] = [
            'tab' => 'content',
            'group' => 'loading',
            'label' => esc_html__('Animation Type', 'bricks-elements-pack'),
            'type' => 'select',
            'options' => [
                'none' => esc_html__('None', 'bricks-elements-pack'),
                'spinner' => esc_html__('Spinner', 'bricks-elements-pack'),
                'flip' => esc_html__('Rotating Flag (3D)', 'bricks-elements-pack'),
                'spin' => esc_html__('Spinning Flag (2D)', 'bricks-elements-pack'),
                'overlay' => esc_html__('Full Page Overlay', 'bricks-elements-pack'),
            ],
            'default' => 'none',
        ];

        $this->controls['spinnerColor'] = [
            'tab' => 'content',
            'group' => 'loading',
            'label' => esc_html__('Spinner Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#0ea5e9',
            'required' => ['loadingType', '!=', 'none'],
            'css' => [
                ['property' => '--ls-spinner-color', 'selector' => ''],
            ],
        ];

        $this->controls['overlayColor'] = [
            'tab' => 'content',
            'group' => 'loading',
            'label' => esc_html__('Overlay Color', 'bricks-elements-pack'),
            'type' => 'color',
            'default' => '#ffffff',
            'required' => ['loadingType', '=', 'overlay'],
        ];

        $this->controls['loadingDuration'] = [
            'tab' => 'content',
            'group' => 'loading',
            'label' => esc_html__('Animation Duration (s)', 'bricks-elements-pack'),
            'type' => 'text',
            'default' => '0.5',
            'required' => ['loadingType', '!=', 'none'],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('bricks-language-switcher-css');
        wp_enqueue_script('bricks-language-switcher-js');
    }

    /**
     * Get the flag URL for a language
     */
    private function get_flag_url($slug, $settings)
    {
        // Check custom flags first
        $custom_flags = $settings['customFlags'] ?? [];
        if (!empty($custom_flags)) {
            foreach ($custom_flags as $custom) {
                if (isset($custom['langSlug']) && $custom['langSlug'] === $slug && !empty($custom['flagImage'])) {
                    $image_id = $custom['flagImage']['id'] ?? 0;
                    $image_url = $custom['flagImage']['url'] ?? '';

                    if ($image_id) {
                        $url = wp_get_attachment_url($image_id);
                        if ($url)
                            return $url;
                    }

                    if ($image_url)
                        return $image_url;
                }
            }
        }

        // Use built-in circle-flags CDN
        $country_code = $this->slug_to_country[$slug] ?? $slug;
        return 'https://hatscripts.github.io/circle-flags/flags/' . strtolower($country_code) . '.svg';
    }

    /**
     * Get display name for a language
     */
    private function get_display_name($lang, $format)
    {
        switch ($format) {
            case 'slug':
                return strtolower($lang['slug']);
            case 'slug_upper':
                return strtoupper($lang['slug']);
            case 'name':
            default:
                return $lang['name'];
        }
    }

    public function render()
    {
        $settings = $this->settings;

        // Check if Polylang is active
        if (!function_exists('pll_the_languages')) {
            echo '<div class="bep-lang-switcher-notice">';
            echo esc_html__('Language Switcher: Polylang plugin is not active.', 'bricks-elements-pack');
            echo '</div>';
            return;
        }

        // Get languages
        $languages = pll_the_languages(['raw' => 1]);

        if (empty($languages)) {
            echo '<div class="bep-lang-switcher-notice">';
            echo esc_html__('No languages configured in Polylang.', 'bricks-elements-pack');
            echo '</div>';
            return;
        }

        // Settings
        $show_flags = isset($settings['showFlags']) ? $settings['showFlags'] : true;
        $show_names = isset($settings['showNames']) ? $settings['showNames'] : true;
        $name_format = $settings['nameDisplay'] ?? 'name';
        $hide_current = !empty($settings['hideCurrent']);
        $hide_no_translation = !empty($settings['hideNoTranslation']);
        $enable_dropdown = !empty($settings['enableDropdown']);
        $active_indicator = $settings['activeIndicator'] ?? 'none';

        // Root attributes
        $this->set_attribute('_root', 'class', 'bep-lang-switcher');

        if ($enable_dropdown) {
            $this->set_attribute('_root', 'class', 'bep-lang-dropdown');
        }

        if ($active_indicator !== 'none') {
            $this->set_attribute('_root', 'data-active-style', $active_indicator);
        }

        // Pass loading config
        $loading_type = $settings['loadingType'] ?? 'none';
        if ($loading_type !== 'none') {
            $config = [
                'type' => $loading_type,
                'spinnerColor' => !empty($settings['spinnerColor']) ? $settings['spinnerColor'] : '#0ea5e9',
                'overlayColor' => !empty($settings['overlayColor']) ? $settings['overlayColor'] : '#ffffff',
                'duration' => 0.5, // Duration no longer used for timing, but passed for overlay fade speed
            ];
            $this->set_attribute('_root', 'data-ls-config', wp_json_encode($config));
        }

        echo '<div ' . $this->render_attributes('_root') . '>';

        // Find current language for dropdown trigger
        $current_lang = null;
        $other_langs = [];

        foreach ($languages as $lang) {
            if ($hide_no_translation && !empty($lang['no_translation'])) {
                continue;
            }

            if (!empty($lang['current_lang'])) {
                $current_lang = $lang;
                if (!$hide_current && !$enable_dropdown) {
                    $other_langs[] = $lang;
                }
            }
            else {
                $other_langs[] = $lang;
            }
        }

        // Dropdown mode: current language as trigger
        if ($enable_dropdown && $current_lang) {
            echo '<div class="bep-lang-trigger">';
            $this->render_lang_item($current_lang, $settings, $show_flags, $show_names, $name_format, true);
            echo '<svg class="bep-lang-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            echo '</div>';

            echo '<div class="bep-lang-dropdown-menu">';
            foreach ($other_langs as $lang) {
                $this->render_lang_item($lang, $settings, $show_flags, $show_names, $name_format, false);
            }
            echo '</div>';
        }
        else {
            // Inline mode
            if (!$hide_current && $current_lang) {
                $this->render_lang_item($current_lang, $settings, $show_flags, $show_names, $name_format, true);
            }
            foreach ($other_langs as $lang) {
                if ($lang === $current_lang)
                    continue;
                $this->render_lang_item($lang, $settings, $show_flags, $show_names, $name_format, false);
            }
        }

        echo '</div>';
    }

    /**
     * Render a single language item
     */
    private function render_lang_item($lang, $settings, $show_flags, $show_names, $name_format, $is_current)
    {
        $url = esc_url($lang['url']);
        $classes = 'bep-lang-item';
        if ($is_current)
            $classes .= ' bep-lang-active';

        $lang_attr = ' hreflang="' . esc_attr($lang['slug']) . '"';
        $aria = $is_current ? ' aria-current="true"' : '';

        echo '<a href="' . $url . '" class="' . $classes . '"' . $lang_attr . $aria . '>';

        if ($show_flags) {
            $flag_url = $this->get_flag_url($lang['slug'], $settings);
            echo '<span class="bep-lang-flag">';
            echo '<img src="' . esc_url($flag_url) . '" alt="' . esc_attr($lang['name']) . '" loading="lazy" />';
            echo '</span>';
        }

        if ($show_names) {
            echo '<span class="bep-lang-name">' . esc_html($this->get_display_name($lang, $name_format)) . '</span>';
        }

        echo '</a>';
    }
}