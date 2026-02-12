<?php
if (!defined('ABSPATH'))
  exit;

class Bricks_Particle_JS_Controls
{
  public function __construct()
  {
    add_filter('bricks/elements/section/control_groups', [$this, 'add_control_group']);
    add_filter('bricks/elements/section/controls', [$this, 'add_controls']);
    add_filter('bricks/element/render_attributes', [$this, 'render_attributes'], 10, 3);
  }

  public function add_control_group($control_groups)
  {
    $control_groups['particles_group'] = [
      'title' => esc_html__('Particles.js', 'bricks-particle-js'),
      'tab' => 'style', // Use style tab
    ];
    return $control_groups;
  }

  public function add_controls($controls)
  {
    $controls['particles_enable'] = [
      'tab' => 'style',
      'group' => 'particles_group',
      'label' => esc_html__('Enable Particles', 'bricks-particle-js'),
      'type' => 'checkbox',
    ];

    $controls['particles_config'] = [
      'tab' => 'style',
      'group' => 'particles_group',
      'label' => esc_html__('Config JSON', 'bricks-particle-js'),
      'type' => 'code',
      'mode' => 'javascript', // Or 'json' if available, but javascript mode is safe for JSON
      'default' => '{
  "particles": {
    "number": {
      "value": 80,
      "density": {
        "enable": true,
        "value_area": 800
      }
    },
    "color": {
      "value": "#ffffff"
    },
    "shape": {
      "type": "circle",
      "stroke": {
        "width": 0,
        "color": "#000000"
      },
      "polygon": {
        "nb_sides": 5
      }
    },
    "opacity": {
      "value": 0.5,
      "random": false,
      "anim": {
        "enable": false,
        "speed": 1,
        "opacity_min": 0.1,
        "sync": false
      }
    },
    "size": {
      "value": 3,
      "random": true,
      "anim": {
        "enable": false,
        "speed": 40,
        "size_min": 0.1,
        "sync": false
      }
    },
    "line_linked": {
      "enable": true,
      "distance": 150,
      "color": "#ffffff",
      "opacity": 0.4,
      "width": 1
    },
    "move": {
      "enable": true,
      "speed": 6,
      "direction": "none",
      "random": false,
      "straight": false,
      "out_mode": "out",
      "bounce": false,
      "attract": {
        "enable": false,
        "rotateX": 600,
        "rotateY": 1200
      }
    }
  },
  "interactivity": {
    "detect_on": "canvas",
    "events": {
      "onhover": {
        "enable": true,
        "mode": "repulse"
      },
      "onclick": {
        "enable": true,
        "mode": "push"
      },
      "resize": true
    },
    "modes": {
      "grab": {
        "distance": 400,
        "line_linked": {
          "opacity": 1
        }
      },
      "bubble": {
        "distance": 400,
        "size": 40,
        "duration": 2,
        "opacity": 8,
        "speed": 3
      },
      "repulse": {
        "distance": 200,
        "duration": 0.4
      },
      "push": {
        "particles_nb": 4
      },
      "remove": {
        "particles_nb": 2
      }
    }
  },
  "retina_detect": true
}',
      'required' => ['particles_enable', '=', true],
      'description' => esc_html__('Paste your Particles.js JSON config here.', 'bricks-particle-js'),
    ];

    $controls['particles_z_index'] = [
      'tab' => 'style',
      'group' => 'particles_group',
      'label' => esc_html__('Z-Index', 'bricks-particle-js'),
      'type' => 'number',
      'default' => 0,
      'required' => ['particles_enable', '=', true],
    ];

    $controls['particles_opacity'] = [
      'tab' => 'style',
      'group' => 'particles_group',
      'label' => esc_html__('Canvas Opacity', 'bricks-particle-js'),
      'type' => 'number',
      'min' => 0,
      'max' => 1,
      'step' => 0.1,
      'default' => 1,
      'required' => ['particles_enable', '=', true],
    ];

    return $controls;
  }

  public function render_attributes($attributes, $key, $element)
  {
    // Only run for sections
    if ($element->name !== 'section') {
      return $attributes;
    }

    $settings = $element->settings;

    // Check if particles are enabled
    if (isset($settings['particles_enable']) && $settings['particles_enable']) {
      $attributes['data-bricks-particles'] = 'true';

      if (!empty($settings['particles_config'])) {
        // Clean up the JSON string to ensure it's valid for data attribute
        $config = $settings['particles_config'];
        // We might want to minify it or base64 encode it if it causes issues, but encoding is safer
        $attributes['data-particles-config'] = base64_encode($config);
      }

      if (isset($settings['particles_z_index'])) {
        $attributes['data-particles-z-index'] = $settings['particles_z_index'];
      }

      if (isset($settings['particles_opacity'])) {
        $attributes['data-particles-opacity'] = $settings['particles_opacity'];
      }
    }

    return $attributes;
  }
}
