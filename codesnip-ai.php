<?php
/**
 * Plugin Name: CodeSnip AI
 * Description: A simple React app embedded in WordPress using Vite.
 * Version: 1.0
 */

// add_action('admin_menu', function () {
//   add_menu_page(
//     'CodeSnip AI', 
//     'CodeSnip AI',  
//     'manage_options', 
//     'codesnip-ai', 
//     'codesnip_render_app', 
//     'dashicons-media-code', 26
//   );
//     add_submenu_page(
//       'codesnip-ai',          // Parent slug
//       'All Snippets',         // Page title
//       'All Snippets',         // Menu title (this is the submenu name you want)
//       'manage_options',
//       'codesnip-ai',          // Must match parent to override default
//       'codesnip_render_app'   // Same callback
//     );
//   add_submenu_page(
//     'codesnip-ai', 
//     'Add New', 
//     'Add New', 
//     'manage_options', 
//     'codesnip-ai#/add', 
//     'codesnip_render_app'
//   );
//   add_submenu_page(
//     'codesnip-ai', 
//     'Settings', 
//     'Settings', 
//     'manage_options', 
//     'codesnip-ai#/settings', 
//     'codesnip_render_app'
//   );
// });

// Define admin menu using class-style
class SideBarMenu {
  public function createMenu(): array {
      $icon = '<svg width="36" height="34" viewBox="0 0 36 34" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.9403 1.3999C15.1093 1.3999 12.8145 3.66555 12.8145 6.46034C12.8145 9.25515 15.1093 11.5208 17.9403 11.5208C20.7713 11.5208 23.066 9.25515 23.066 6.46034C23.066 3.66555 20.7713 1.3999 17.9403 1.3999ZM7.91819 11.2447C13.2538 11.2447 17.5792 15.5149 17.5792 20.7825V28.5999H12.7738V20.7825C12.7738 20.2214 12.6762 19.6831 12.4969 19.1827L5.43836 26.1513L2.04042 22.7965L8.84736 16.0765C8.54661 16.019 8.23595 15.9888 7.91819 15.9888H0V11.2447H7.91819ZM28.0819 11.2447C22.7463 11.2447 18.4208 15.5149 18.4208 20.7825V28.5999H23.2262V20.7825C23.2262 20.3432 23.2861 19.9175 23.3983 19.5133L30.7819 26.8028L34.18 23.4481L26.7963 16.1586C27.2058 16.0479 27.637 15.9888 28.0819 15.9888H36V11.2447H28.0819Z" fill="#808285"/>
      </svg>';

      $slug = 'codesnip-ai';

      return [
          'Home' => [
              'type'       => 'menu',
              'title'      => __('CodeSnip AI - Admin Panel', 'codesnip-ai'),
              'name'       => __('CodeSnip AI', 'codesnip-ai'),
              'capability' => 'manage_options',
              'slug'       => $slug,
              'callback'   => [$this, 'renderApp'],
              'icon'       => 'data:image/svg+xml;base64,' . base64_encode($icon),
              'position'   => 25,
          ],
          'All Snippets' => [
              'type'       => 'submenu',
              'parent'     => $slug,
              'name'       => __('All Snippets', 'codesnip-ai'),
              'capability' => 'manage_options',
              'slug'       => $slug . '#/',
          ],
          'Add New' => [
            'type'       => 'submenu',
            'parent'     => $slug,
            'name'       => __('Add New', 'codesnip-ai'),
            'capability' => 'manage_options',
            'slug'       => $slug . '#/add-new',
          ],
          'Settings' => [
              'type'       => 'submenu',
              'parent'     => $slug,
              'name'       => __('Settings', 'codesnip-ai'),
              'capability' => 'manage_options',
              'slug'       => $slug . '#/settings',
          ],
      ];
  }

  public function renderApp() {
      echo '<div id="codesnip-ai-root"></div>';
  }

  public function addMenu() {
      $menus = $this->createMenu();
      global $submenu;
      foreach ($menus as $menu) {
          if (current_user_can($menu['capability'])) {
              if ($menu['type'] === 'menu') {
                  add_menu_page(
                      $menu['title'],
                      $menu['name'],
                      $menu['capability'],
                      $menu['slug'],
                      $menu['callback'],
                      $menu['icon'],
                      $menu['position']
                  );
              } else {
                  $submenu[$menu['parent']][] = [
                      $menu['name'],
                      $menu['capability'],
                      'admin.php?page=' . $menu['slug']
                  ];
              }
          }
      }
  }
}

add_action('admin_menu', [new SideBarMenu(), 'addMenu']);

// function codesnip_render_app() {
//   echo '<div id="codesnip-ai-root"></div>';
// }

add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook !== 'toplevel_page_codesnip-ai') return;

  $manifest_path = plugin_dir_path(__FILE__) . 'build/manifest.json';

  if (!file_exists($manifest_path)) {
    echo '<p>Build files not found. Run npm run build.</p>';
    return;
  }

  $manifest = json_decode(file_get_contents($manifest_path), true);
  $entry = $manifest['main.jsx'];

  // Enqueue CSS
  if (isset($entry['css'])) {
    foreach ($entry['css'] as $css) {
      wp_enqueue_style(
        'codesnip-style',
        plugin_dir_url(__FILE__) . 'build/' . $css,
        [],
        null
      );
    }
  }

  // Enqueue JS
  wp_enqueue_script(
    'codesnip-script',
    plugin_dir_url(__FILE__) . 'build/' . $entry['file'],
    [],
    null,
    true
  );
}, 100); 


add_action('rest_api_init', function () {
  register_rest_route('codesnip/v1', '/ai', [
    'methods' => 'POST',
    'permission_callback' => '__return_true',
    'callback' => function ($request) {
      $params = $request->get_json_params();
      $type = sanitize_text_field($params['type'] ?? '');
      $code = sanitize_textarea_field($params['code'] ?? '');

      if (!$type || !$code) {
        return new WP_REST_Response(['error' => 'Missing parameters'], 400);
      }

      $prompt = match ($type) {
        'tailwind_full' => "Convert the following HTML and CSS into Tailwind CSS. Use only Tailwind utility classes — no custom CSS, no inline styles, no <html>, <head>, or <body> tags and no 'html' word.

        Requirements:
        - Output only the converted design section with tailwind class (no explanation or markdown)
        - Use modern and visually appealing Tailwind styles.
        - Keep the full structure and content intact.
        - if code has custom css then must use relavant tailwing color class.
        - Maintain layout, color, spacing, typography, and responsiveness.
        
        Return only the clean, final Tailwind HTML and ready to embed in a Tailwind-based project.
        
        Input:\n\n" . $code,
        default => $code,
      };

      $api_key = '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'; // TODO: Replace or load from DB
      // $api_key = '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'; // TODO: Replace or load from DB
      // $api_key = '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'; // TODO: Replace or load from DB

      $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
          'Authorization' => 'Bearer ' . $api_key,
          'Content-Type'  => 'application/json'
        ],
        'body' => json_encode([
          // 'model' => 'gpt-4o-mini',
          'model' => 'gpt-4-turbo',
          'messages' => [['role' => 'user', 'content' => $prompt]],
          'max_tokens' => 1500
        ]),
        'timeout' => 60
      ]);

      // Debug: log full body
      error_log(print_r($response, true)); // ← Add this

      if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => $response->get_error_message()], 500);
      }

      $body = json_decode(wp_remote_retrieve_body($response), true);
      return ['result' => trim($body['choices'][0]['message']['content'] ?? 'No response')];
    }
  ]);

  // Save Snippet
  register_rest_route('codesnip/v1', '/snippets', [
    'methods' => 'POST',
    'permission_callback' => '__return_true',
    'callback' => function ($req) {
      global $wpdb;
      $params = $req->get_json_params();
      $code = sanitize_textarea_field($params['code'] ?? '');
      $output = sanitize_textarea_field($params['output'] ?? '');

      $wpdb->insert("{$wpdb->prefix}codesnip_snippets", [
        'code' => $code,
        'output' => $output,
        'created_at' => current_time('mysql')
      ]);

      return [
        'id' => $wpdb->insert_id,
        'output' => $output
      ];
    }
  ]);
  
  // Get Snippets
  register_rest_route('codesnip/v1', '/snippets', [
    'methods' => 'GET',
    'permission_callback' => '__return_true',
    'callback' => function () {
      global $wpdb;
      return $wpdb->get_results("SELECT id, output FROM {$wpdb->prefix}codesnip_snippets ORDER BY id DESC", ARRAY_A);
    }
  ]);

  // settings api
  register_rest_route('codesnip/v1', '/settings', [
    'methods' => 'POST',
    'callback' => function ($request) {
      $params = $request->get_json_params();
      update_option('codesnip_openai_key', sanitize_text_field($params['apiKey']));
      update_option('codesnip_model', sanitize_text_field($params['model']));
      update_option('codesnip_max_tokens', intval($params['maxTokens']));
      return ['success' => true];
    },
    'permission_callback' => '__return_true',
  ]);

  register_rest_route('codesnip/v1', '/settings', [
    'methods' => 'GET',
    'callback' => function () {
      return [
        'apiKey'    => get_option('codesnip_openai_key', ''),
        'model'     => get_option('codesnip_model', 'gpt-3.5-turbo'),
        'maxTokens' => get_option('codesnip_max_tokens', 1000),
      ];
    },
    'permission_callback' => '__return_true',
  ]);
});

// Create DB Table
register_activation_hook(__FILE__, function () {
  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code LONGTEXT,
    output LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  ) $charset_collate;";

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($sql);
});

// Shortcode: [codesnip id="1"]
add_shortcode('codesnip', function ($atts) {
  global $wpdb;
  $atts = shortcode_atts(['id' => 0], $atts);
  $output = $wpdb->get_var($wpdb->prepare("SELECT output FROM {$wpdb->prefix}codesnip_snippets WHERE id = %d", $atts['id']));
  return $output ? "<pre class='codesnip-output'>{$output}</pre>" : 'Snippet not found.';
});
