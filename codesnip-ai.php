<?php
/**
 * Plugin Name: CodeSnip AI
 * Description: A simple React app embedded in WordPress using Vite.
 * Version: 1.0
 */

add_action('admin_menu', function () {
  add_menu_page('CodeSnip AI', 'CodeSnip AI', 'manage_options', 'codesnip-ai', 'codesnip_ai_render', 'dashicons-editor-code', 26);
});

function codesnip_ai_render() {
  echo '<div id="codesnip-ai-root"></div>';
}

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
    'codesnip-js',
    plugin_dir_url(__FILE__) . 'build/' . $entry['file'],
    [],
    null,
    true
  );
});

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
        'explain'  => "Explain this code:\n\n" . $code,
        'clean'    => "Clean and simplify this code:\n\n" . $code,
        'tailwind' => "Convert this CSS/HTML snippet to Tailwind CSS:\n\n" . $code,
        default    => $code,
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
          'model' => 'gpt-3.5-turbo',
          'messages' => [['role' => 'user', 'content' => $prompt]],
          'max_tokens' => 500
        ]),
        'timeout' => 20
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
});
