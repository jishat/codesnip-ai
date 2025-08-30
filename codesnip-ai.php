<?php

/**
 * Plugin Name: CodeSnip AI
 * Description: A simple React app embedded in WordPress using Vite.
 * Version: 1.0
 */

class SideBarMenu
{
  public function createMenu(): array
  {
    $icon = '<svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 194.69 161.41" fill="none" width="36" height="36">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M122.33,46.73c0-10.41-0.04-20.81,0.04-31.22c0.01-1.75-0.7-3.99,1.06-5.13c2.17-1.42,3.86,0.74,5.43,1.71
        c10.03,6.22,19.33,13.59,30.5,18.01c9.26,3.66,18.79,5.41,28.61,6.12c5.7,0.42,7.74,2.71,6.27,8.19
        c-3.11,11.56-11.73,18.32-21.74,23.49c-3.01,1.55-4.31,3.06-3.98,6.49c3.37,35.64-19.5,68.52-53.41,81.39
        c-37.85,14.36-81.06,0.52-102.48-32.8C-12.84,83.39,1.53,31.12,43.95,9.78C63.39,0.01,83.89-2.4,105.22,2.37
        c4.02,0.9,6.03,2.66,5.99,6.96c-0.13,12.45-0.11,24.9-0.02,37.35c0.04,5.08-1.52,6.21-6.53,4.25
        c-26.21-10.29-48.96,4.94-57.28,22.68c-7.28,15.51,1.21,33.82,17.64,38.83c22.1,6.74,48-5.75,56.52-27.26
        c1.48-3.73,0.91-7.66,0.95-11.51c0.07-8.98,0.02-17.96,0.02-26.94C122.45,46.73,122.39,46.73,122.33,46.73z" fill="#808285"/>
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

  public function renderApp()
  {
    echo '<div id="codesnip-ai-root"></div>';
  }

  public function addMenu()
  {
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

add_action('admin_enqueue_scripts', function ($hook) {
  if (!defined('WP_DEBUG') || !WP_DEBUG) return; // Only in dev mode

  $slug = 'codesnip-ai';
  $varPrefix = 'codesnip_ai_';
  wp_enqueue_script($slug . '-vite-client-helper-MODULE', 'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/src/lib/devHotModule.js', [], null);
  wp_enqueue_script($slug . '-vite-client-MODULE', 'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/@vite/client', [], null);
  wp_enqueue_script($slug . '-index-MODULE', 'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/src/main.jsx', [], null);

  wp_localize_script($slug . '-index-MODULE', $varPrefix, [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('codesnip_ai_nonce'),
  ]);
}, 100);


add_filter('script_loader_tag',function($html, $handle, $href)
{
    $slug = 'codesnip-ai';
    $newTag = $html;
    if (strpos($handle, 'MODULE') !== false && strpos($handle, $slug) !== false) {
        $newTag = preg_replace('/<script /', '<script type="module" ', $newTag);
    }

    return $newTag;
}, 0, 3);

function codesnip_ai_assist_callback()
{
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => ['prompt' => 'Invalid nonce']], 403);
  }

  $raw_prompt  = isset( $_POST['prompt'] ) ? trim(wp_unslash( $_POST['prompt'] )) : '';
  $raw_snippet = isset( $_POST['snippet'] ) ? trim(wp_unslash( $_POST['snippet'] )) : '';
  if (!isset($raw_prompt) || empty($raw_prompt)) {
    wp_send_json_error(['error' => ['prompt' => 'Prompt must required']], 403);
  }

  if ( !is_string($raw_prompt) || strlen($raw_prompt) > 10000 ) {
    wp_send_json_error([ 'error' => ['prompt' => 'Invalid prompt or max length 10000 characters'] ], 400 );
  }

  if (!isset($raw_snippet) || empty($raw_snippet)) {
    wp_send_json_error(['error' => ['prompt' => 'Code snippet must required']], 403);
  }

  $disallowed = [ 'html', 'body', 'script', 'link', 'footer' ];
  foreach ( $disallowed as $tag ) {
    if ( preg_match( '/<' . $tag . '\b/i', $raw_snippet ) ) {
      wp_send_json_error(['error' => ['snippet' => sprintf( __( 'The <%s> tag is not allowed in snippets.', 'codesnip-ai' ), $tag )]], 403);
    }
  }
  
  $allowed_tags = wp_kses_allowed_html( 'post' );
  foreach ( $disallowed as $tag ) {
    unset( $allowed_tags[ $tag ] );
  }

  $snippet = wp_kses( $raw_snippet, $allowed_tags );

  $content = $raw_prompt. "
        Other Requirements: 
        - Output only the code.
        - No <html>, <head>, <body>, <script>, <link>, <footer> or <style> tags and word
        - No markdown or explanation.
        - No need to add any other text or explanation.
        
        Code/snippet:\n\n" . $_POST['snippet'];

  // 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
  $api_key = get_option('codesnip_ai_openai_api_key', '');
  if (empty($api_key)) {
    wp_send_json_error(['error' => ['prompt' => 'OpenAI API key not configured. Please configure it in Settings.']], 400);
  }

  $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type'  => 'application/json'
    ],
    'body' => json_encode([
      'model' => get_option('codesnip_ai_openai_model', 'gpt-4.1-nano'),
      'messages' => [['role' => 'user', 'content' => $content]],
      'max_tokens' => intval(get_option('codesnip_ai_openai_max_tokens', 1500))
    ]),
    'timeout' => 60
  ]);

  if (is_wp_error($response)) {
    wp_send_json_error(['error' => ['prompt' => $response->get_error_message()]], 403);
  }
  $body = json_decode(wp_remote_retrieve_body($response), true);

  wp_send_json_success([
    'message' => 'Successfully!',
    'data'    => $body['choices'][0]['message']['content']
  ]);
}
add_action('wp_ajax_codesnip_ai_assist', 'codesnip_ai_assist_callback');

function codesnip_ai_save_callback(){
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => ['common' => 'Invalid nonce']], 403);
  }

  $raw_input = isset( $_POST['snippet'] ) ? trim( wp_unslash( $_POST['snippet'] ) ) : '';
  $disallowed = [ 'html', 'body', 'script', 'link', 'footer' ];
  foreach ( $disallowed as $tag ) {
    if ( preg_match( '/<' . $tag . '\b/i', $raw_input ) ) {
      wp_send_json_error(['error' => ['snippet' => sprintf( __( 'The <%s> tag is not allowed in snippets.', 'codesnip-ai' ), $tag )]], 403);
    }
  }
  
  $allowed_tags = wp_kses_allowed_html( 'post' );
  foreach ( $disallowed as $tag ) {
    unset( $allowed_tags[ $tag ] );
  }

  $snippet = wp_kses( $raw_input, $allowed_tags );
  $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';

  if ($snippet === '') {
    wp_send_json_error(['error' => ['snippet' => 'Code must required']], 403);
  }
  if ($title === '') {
    wp_send_json_error(['error' => ['title' => 'Title must required']], 403);
  }
  
  $type = 'html';

  global $wpdb;
  $snippetSlug = codesnip_generate_unique_slug($title);
  $wpdb->insert("{$wpdb->prefix}codesnip_snippets", [
    'snippet' => $snippet,
    'title' => $title,
    'slug' => $snippetSlug,
    'type' => $type,
    'status' => 1,
    'created_at' => current_time('mysql')
  ]);

  wp_send_json_success([
    'message' => 'Snippet saved successfully!',
    'data'    => ['id' => $wpdb->insert_id]
  ]);

};
add_action('wp_ajax_codesnip_ai_save', 'codesnip_ai_save_callback');

// AJAX handler to get all snippets
function codesnip_ai_get_all_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  $snippets = $wpdb->get_results(
    "SELECT id, title, slug, status, created_at, type 
     FROM $table 
     ORDER BY created_at DESC",
    ARRAY_A
  );

  if ($snippets === null) {
    wp_send_json_error(['error' => 'Database error'], 500);
  }

  wp_send_json_success(['snippets' => $snippets]);
}
add_action('wp_ajax_codesnip_ai_get_all', 'codesnip_ai_get_all_callback');

function codesnip_ai_get_by_type_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
  
  if (empty($type)) {
    wp_send_json_error(['error' => 'Type parameter is required'], 400);
  }

  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";

  $types = ['html', 'css', 'js'];

  if (!in_array($type, $types)) {
    wp_send_json_error(['error' => 'Type is invalid'], 400);
  }
  
  $snippets = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT id, title, snippet, slug, status, created_at, type 
       FROM $table 
       WHERE type = %s
       ORDER BY created_at DESC",
      $type
    ),
    ARRAY_A
  );

  if ($snippets === null) {
    wp_send_json_error(['error' => 'Database error'], 500);
  }

  wp_send_json_success(['snippets' => $snippets]);
}
add_action('wp_ajax_codesnip_ai_get_by_type', 'codesnip_ai_get_by_type_callback');

// AJAX handler to toggle snippet status
function codesnip_ai_toggle_status_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : 0;
  $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
  
  if ($snippet_id <= 0) {
    wp_send_json_error(['error' => 'Invalid snippet ID'], 400);
  }

  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  $result = $wpdb->update(
    $table,
    ['status' => $status],
    ['id' => $snippet_id],
    ['%d'],
    ['%d']
  );

  if ($result === false) {
    wp_send_json_error(['error' => 'Failed to update status'], 500);
  }

  wp_send_json_success(['message' => 'Status updated successfully']);
}
add_action('wp_ajax_codesnip_ai_toggle_status', 'codesnip_ai_toggle_status_callback');

// AJAX handler to delete snippet
function codesnip_ai_delete_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : 0;
  
  if ($snippet_id <= 0) {
    wp_send_json_error(['error' => 'Invalid snippet ID'], 400);
  }

  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  $result = $wpdb->delete(
    $table,
    ['id' => $snippet_id],
    ['%d']
  );

  if ($result === false) {
    wp_send_json_error(['error' => 'Failed to delete snippet'], 500);
  }

  wp_send_json_success(['message' => 'Snippet deleted successfully']);
}
add_action('wp_ajax_codesnip_ai_delete', 'codesnip_ai_delete_callback');

// AJAX handler to get snippet by ID
function codesnip_ai_get_by_id_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : 0;
  
  if ($snippet_id <= 0) {
    wp_send_json_error(['error' => 'Invalid snippet ID'], 400);
  }

  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  $snippet = $wpdb->get_row($wpdb->prepare(
    "SELECT id, title, snippet, slug, status, created_at, type
     FROM $table
     WHERE id = %d",
    $snippet_id
  ), ARRAY_A);

  if (!$snippet) {
    wp_send_json_error(['error' => 'Snippet not found'], 404);
  }

  wp_send_json_success(['snippet' => $snippet]);
}
add_action('wp_ajax_codesnip_ai_get_by_id', 'codesnip_ai_get_by_id_callback');

// AJAX handler to update snippet
function codesnip_ai_update_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : 0;
  $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
  // $snippet = isset($_POST['snippet']) ? wp_kses_post(wp_unslash($_POST['snippet'])) : '';
  $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';

  if ($snippet_id <= 0) {
    wp_send_json_error(['error' => ['snippet_id' => 'Invalid snippet ID']], 400);
  }

  if (empty($title)) {
    wp_send_json_error(['error' => ['title' => 'Title is required']], 400);
  }

  if ($type !== 'html' ) {
    wp_send_json_error(['error' => ['type' => 'Type is invalid']], 400);
  }

  $raw_input = isset( $_POST['snippet'] ) ? trim( wp_unslash( $_POST['snippet'] ) ) : '';
  if (empty($raw_input)) {
    wp_send_json_error(['error' => ['snippet' => 'Snippet content is required']], 400);
  }

  $disallowed = [ 'html', 'body', 'script', 'link', 'footer' ];
  foreach ( $disallowed as $tag ) {
    if ( preg_match( '/<' . $tag . '\b/i', $raw_input ) ) {
      wp_send_json_error(['error' => ['snippet' => sprintf( __( 'The <%s> tag is not allowed in snippets.', 'codesnip-ai' ), $tag )]], 403);
    }
  }
  
  $allowed_tags = wp_kses_allowed_html( 'post' );
  foreach ( $disallowed as $tag ) {
    unset( $allowed_tags[ $tag ] );
  }

  $snippet = wp_kses( $raw_input, $allowed_tags );


  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  $slug = codesnip_generate_unique_slug($title);
  
  $result = $wpdb->update(
    $table,
    [
      'title' => $title,
      'slug' => $slug,
      'snippet' => $snippet,
      'type' => $type,
    ],
    ['id' => $snippet_id],
    ['%s', '%s', '%s', '%s'],
    ['%d']
  );

  if ($result === false) {
    wp_send_json_error(['error' => ['common' => 'Failed to update snippet']], 500);
  }

  wp_send_json_success(['message' => 'Snippet updated successfully']);
}
add_action('wp_ajax_codesnip_ai_update', 'codesnip_ai_update_callback');

// AJAX handler to save OpenAI settings
function codesnip_ai_save_settings_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  // Check if user has permission to manage options
  if (!current_user_can('manage_options')) {
    wp_send_json_error(['error' => 'Insufficient permissions'], 403);
  }

  $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';
  $model = isset($_POST['model']) ? sanitize_text_field(wp_unslash($_POST['model'])) : '';
  $max_tokens = isset($_POST['max_tokens']) ? intval($_POST['max_tokens']) : 1500;

  // Validate API key (basic validation)
  if (empty($api_key)) {
    wp_send_json_error(['error' => 'API key is required'], 400);
  }

  if (!preg_match('/^[a-zA-Z0-9_-]{32,200}$/', $api_key)) {
    wp_send_json_error(['error' => 'Invalid API key format'], 400);
  }

  // Validate model
  $allowed_models = [
    'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano',
    'gpt-4o', 'gpt-4o-mini',
    'o1', 'o1-mini', 'o3', 'o3-mini',
    'gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo'
  ];
  if (!in_array($model, $allowed_models)) {
    wp_send_json_error(['error' => 'Invalid model selection'], 400);
  }

  // Validate max tokens
  if ($max_tokens < 1 || $max_tokens > 4000) {
    wp_send_json_error(['error' => 'Max tokens must be between 1 and 4000'], 400);
  }

  // Save settings using WordPress options API
  update_option('codesnip_ai_openai_api_key', $api_key);
  update_option('codesnip_ai_openai_model', $model);
  update_option('codesnip_ai_openai_max_tokens', $max_tokens);

  wp_send_json_success([
    'message' => 'Settings saved successfully!',
    'data' => [
      'api_key' => $api_key,
      'model' => $model,
      'max_tokens' => $max_tokens
    ]
  ]);
}
add_action('wp_ajax_codesnip_ai_save_settings', 'codesnip_ai_save_settings_callback');

// AJAX handler to get OpenAI settings
function codesnip_ai_get_settings_callback() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'codesnip_ai_nonce')) {
    wp_send_json_error(['error' => 'Invalid nonce'], 403);
  }

  // Check if user has permission to manage options
  if (!current_user_can('manage_options')) {
    wp_send_json_error(['error' => 'Insufficient permissions'], 403);
  }

  $settings = [
    'api_key' => get_option('codesnip_ai_openai_api_key', ''),
    'model' => get_option('codesnip_ai_openai_model', 'gpt-4.1-nano'),
    'max_tokens' => intval(get_option('codesnip_ai_openai_max_tokens', 1500))
  ];

  wp_send_json_success(['settings' => $settings]);
}
add_action('wp_ajax_codesnip_ai_get_settings', 'codesnip_ai_get_settings_callback');

register_activation_hook(__FILE__, function () {
  global $wpdb;
  $table = "{$wpdb->prefix}codesnip_snippets";
  
  // Check if table already exists
  $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
  
  if (!$table_exists) {
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(255) NOT NULL,
      slug VARCHAR(255) NOT NULL,
      snippet LONGTEXT NOT NULL,
      type VARCHAR(20) DEFAULT 'html' NOT NULL,
      status TINYINT(1) DEFAULT 1 NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
  }

  // Set default OpenAI settings if they don't exist
  if (!get_option('codesnip_ai_openai_model')) {
    add_option('codesnip_ai_openai_model', 'gpt-4.1-nano');
  }
  if (!get_option('codesnip_ai_openai_max_tokens')) {
    add_option('codesnip_ai_openai_max_tokens', 1500);
  }
});

// Deactivation hook to clean up options (optional)
register_deactivation_hook(__FILE__, function() {
  // Optionally remove settings when plugin is deactivated
  // Uncomment the lines below if you want to remove settings on deactivation
  // delete_option('codesnip_ai_openai_api_key');
  // delete_option('codesnip_ai_openai_model');
  // delete_option('codesnip_ai_openai_max_tokens');
});

// Shortcode: [codesnip id="1"]
add_shortcode('codesnip', function ($atts) {
  global $wpdb;
  $atts = shortcode_atts(['id' => 0], $atts);
  
  // Get snippet with status check
  $snippet_data = $wpdb->get_row($wpdb->prepare(
    "SELECT snippet, type, status FROM {$wpdb->prefix}codesnip_snippets WHERE id = %d", 
    $atts['id']
  ), ARRAY_A);
  
  if (!$snippet_data) {
    return 'Snippet not found.';
  }
  
  // Check if snippet is active and is HTML type
  if ($snippet_data['status'] != 1 || $snippet_data['type'] !== 'html') {
    return ''; // Show nothing if not active or not HTML type
  }
  
  $snippet = $snippet_data['snippet'];
  
  // Return HTML snippet directly without wrapper
  return $snippet;
});

function codesnip_generate_unique_slug($title) {
  global $wpdb;

  $table_name = $wpdb->prefix . 'codesnip_snippets';
  $slug = sanitize_title($title);
  $original_slug = $slug;
  $counter = 1;

  // Check if the slug already exists
  while ($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE slug = %s", $slug)) > 0) {
      $slug = $original_slug . '-' . $counter;
      $counter++;
  }

  return $slug;
}