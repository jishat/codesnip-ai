<?php
/**
 * AJAX Handlers Class
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// No need to define constants here - they're now in the config class

/**
 * Class CodeSnip_AI_Ajax_Handlers
 * 
 * Handles all AJAX requests for the plugin.
 */
class CodeSnip_AI_Ajax_Handlers {

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_ajax_codesnip_ai_assist', array($this, 'assist_callback'));
        add_action('wp_ajax_codesnip_ai_save', array($this, 'save_callback'));
        add_action('wp_ajax_codesnip_ai_get_all', array($this, 'get_all_callback'));
        add_action('wp_ajax_codesnip_ai_get_by_type', array($this, 'get_by_type_callback'));
        add_action('wp_ajax_codesnip_ai_toggle_status', array($this, 'toggle_status_callback'));
        add_action('wp_ajax_codesnip_ai_delete', array($this, 'delete_callback'));
        add_action('wp_ajax_codesnip_ai_get_by_id', array($this, 'get_by_id_callback'));
        add_action('wp_ajax_codesnip_ai_update', array($this, 'update_callback'));
        add_action('wp_ajax_codesnip_ai_save_settings', array($this, 'save_settings_callback'));
        add_action('wp_ajax_codesnip_ai_get_settings', array($this, 'get_settings_callback'));
    }

    /**
     * AI Assist callback
     */
    public function assist_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => array('prompt' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        $raw_prompt  = isset($_POST['prompt']) ? trim(wp_unslash($_POST['prompt'])) : '';
        $raw_snippet = isset($_POST['snippet']) ? trim(wp_unslash($_POST['snippet'])) : '';
        
        if (!isset($raw_prompt) || empty($raw_prompt)) {
            wp_send_json_error(array('error' => array('prompt' => __('Prompt must required', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        if (!is_string($raw_prompt) || strlen($raw_prompt) > 10000) {
            wp_send_json_error(array('error' => array('prompt' => __('Invalid prompt or max length 10000 characters', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        if (!isset($raw_snippet) || empty($raw_snippet)) {
            wp_send_json_error(array('error' => array('prompt' => __('Code snippet must required', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        $disallowed = array('html', 'body', 'script', 'link', 'footer');
        foreach ($disallowed as $tag) {
            if (preg_match('/<' . $tag . '\b/i', $raw_snippet)) {
                wp_send_json_error(array('error' => array('snippet' => sprintf(__('The <%s> tag is not allowed in snippets.', CodeSnip_AI_Config::get_text_domain()), $tag))), 403);
            }
        }
        
        $allowed_tags = wp_kses_allowed_html('post');
        foreach ($disallowed as $tag) {
            unset($allowed_tags[$tag]);
        }

        $snippet = wp_kses($raw_snippet, $allowed_tags);

        $content = $raw_prompt . "
            Other Requirements: 
            - Output only the code.
            - No <html>, <head>, <body>, <script>, <link>, <footer> or <style> tags and word
            - No markdown or explanation.
            - No need to add any other text or explanation.
            
            Code/snippet:\n\n" . $_POST['snippet'];

        $api_key = get_option('codesnip_ai_openai_api_key', '');
        if (empty($api_key)) {
            wp_send_json_error(array('error' => array('prompt' => __('OpenAI API key not configured. Please configure it in Settings.', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => get_option('codesnip_ai_openai_model', 'gpt-4.1-nano'),
                'messages' => array(array('role' => 'user', 'content' => $content)),
                'max_tokens' => intval(get_option('codesnip_ai_openai_max_tokens', 1500))
            )),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('error' => array('prompt' => $response->get_error_message())), 403);
        }
        
        // Check HTTP response status
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $error_message = __('API request failed', CodeSnip_AI_Config::get_text_domain());
            
            // Try to get specific error message from OpenAI response
            if (isset($body['error']['message'])) {
                $error_message = $body['error']['message'];
            } elseif ($response_code === 401) {
                $error_message = __('Invalid API key. Please check your OpenAI API key in Settings.', CodeSnip_AI_Config::get_text_domain());
            } elseif ($response_code === 429) {
                $error_message = __('Rate limit exceeded. Please try again later.', CodeSnip_AI_Config::get_text_domain());
            } elseif ($response_code === 500) {
                $error_message = __('OpenAI service error. Please try again later.', CodeSnip_AI_Config::get_text_domain());
            }
            
            wp_send_json_error(array('error' => array('prompt' => $error_message)), $response_code);
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($body['choices'][0]['message']['content'])) {
            wp_send_json_error(array('error' => array('prompt' => __('Invalid response from AI API.', CodeSnip_AI_Config::get_text_domain()))), 500);
        }

        $ai_response = $body['choices'][0]['message']['content'];

        $allowed_tags = wp_kses_allowed_html( 'post' );
        unset( $allowed_tags['html'], $allowed_tags['script'], $allowed_tags['link'], $allowed_tags['footer'], $allowed_tags['body'] );
        
        $escaped_response = wp_kses( $ai_response, $allowed_tags );

        wp_send_json_success(array(
            'message' => __('Successfully!', CodeSnip_AI_Config::get_text_domain()),
            'data'    => $escaped_response
        ));
    }

    /**
     * Save snippet callback
     */
    public function save_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => array('common' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        $raw_input = isset($_POST['snippet']) ? trim(wp_unslash($_POST['snippet'])) : '';
        $disallowed = array('html', 'body', 'script', 'link', 'footer');
        if (!isset($raw_input) || empty($raw_input)) {
            wp_send_json_error(array('error' => array('snippet' => __('Code must required', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        foreach ($disallowed as $tag) {
            if (preg_match('/<' . $tag . '\b/i', $raw_input)) {
                wp_send_json_error(array('error' => array('snippet' => sprintf(__('The <%s> tag is not allowed in snippets.', CodeSnip_AI_Config::get_text_domain()), $tag))), 403);
            }
        }
        
        $allowed_tags = wp_kses_allowed_html('post');
        foreach ($disallowed as $tag) {
            unset($allowed_tags[$tag]);
        }

        $snippet = wp_kses($raw_input, $allowed_tags);
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';

        if ($snippet === '') {
            wp_send_json_error(array('error' => array('snippet' => __('Code must required', CodeSnip_AI_Config::get_text_domain()))), 403);
        }
        if ($title === '') {
            wp_send_json_error(array('error' => array('title' => __('Title must required', CodeSnip_AI_Config::get_text_domain()))), 403);
        }
        
        $type = 'html';

        global $wpdb;
        $snippet_slug = $this->generate_unique_slug($title);
        $wpdb->insert(CodeSnip_AI_Config::get_db_table_name(), array(
            'snippet' => $snippet,
            'title' => $title,
            'slug' => $snippet_slug,
            'type' => $type,
            'status' => 1,
            'created_at' => current_time('mysql')
        ));

        wp_send_json_success(array(
            'message' => __('Snippet saved successfully!', CodeSnip_AI_Config::get_text_domain()),
            'data'    => array('id' => intval($wpdb->insert_id))
        ));
    }

    /**
     * Get all snippets callback
     */
    public function get_all_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();
        
        $snippets = $wpdb->get_results(
            "SELECT id, title, slug, status, created_at, type 
             FROM $table 
             ORDER BY created_at DESC",
            ARRAY_A
        );

        if ($snippets === null) {
            wp_send_json_error(array('error' => __('Snippets not found', CodeSnip_AI_Config::get_text_domain())), 500);
        }

        // Escape all output data for security
        $escaped_snippets = array();
        foreach ($snippets as $snippet) {
            $escaped_snippets[] = array(
                'id' => intval($snippet['id']),
                'title' => esc_html($snippet['title']),
                'slug' => esc_html($snippet['slug']),
                'status' => intval($snippet['status']),
                'created_at' => esc_html($snippet['created_at']),
                'type' => esc_html($snippet['type'])
            );
        }

        wp_send_json_success(array('snippets' => $escaped_snippets));
    }

    /**
     * Get snippets by type callback
     */
    public function get_by_type_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
        
        if (empty($type)) {
            wp_send_json_error(array('error' => __('Type parameter is required', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();

        if ($type !== 'html') {
            wp_send_json_error(array('error' => __('Type is invalid', CodeSnip_AI_Config::get_text_domain())), 400);
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
            wp_send_json_error(array('error' => __('Database error', CodeSnip_AI_Config::get_text_domain())), 500);
        }

        // Escape all output data for security
        $escaped_snippets = array();
        foreach ($snippets as $snippet) {
            $escaped_snippets[] = array(
                'id' => intval($snippet['id']),
                'title' => esc_html($snippet['title']),
                'slug' => esc_html($snippet['slug']),
                'status' => intval($snippet['status']),
                'created_at' => esc_html($snippet['created_at']),
                'type' => esc_html($snippet['type'])
            );
        }

        wp_send_json_success(array('snippets' => $escaped_snippets));
    }

    /**
     * Toggle snippet status callback
     */
    public function toggle_status_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : '';
        $status = isset($_POST['status']) ? intval($_POST['status']) : '';
        if($status !== 0 && $status !== 1) {
            wp_send_json_error(array('error' => __('Invalid status', CodeSnip_AI_Config::get_text_domain())), 400);
        }
        if($snippet_id === '' || $status === '') {
            wp_send_json_error(array('error' => __('Invalid snippet ID or status', CodeSnip_AI_Config::get_text_domain())), 400);
        }
        if ($snippet_id <= 0) {
            wp_send_json_error(array('error' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();
        
        $result = $wpdb->update(
            $table,
            array('status' => $status),
            array('id' => $snippet_id),
            array('%d'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('error' => __('Failed to update status', CodeSnip_AI_Config::get_text_domain())), 500);
        }

        wp_send_json_success(array('message' => __('Status updated successfully', CodeSnip_AI_Config::get_text_domain())));
    }

    /**
     * Delete snippet callback
     */
    public function delete_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : '';

        if($snippet_id === '') {
            wp_send_json_error(array('error' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        if ($snippet_id <= 0) {
            wp_send_json_error(array('error' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();
        
        $result = $wpdb->delete(
            $table,
            array('id' => $snippet_id),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('error' => __('Failed to delete snippet', CodeSnip_AI_Config::get_text_domain())), 500);
        }

        wp_send_json_success(array('message' => __('Snippet deleted successfully', CodeSnip_AI_Config::get_text_domain())));
    }

    /**
     * Get snippet by ID callback
     */
    public function get_by_id_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : '';

        if($snippet_id === '') {
            wp_send_json_error(array('error' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        if ($snippet_id <= 0) {
            wp_send_json_error(array('error' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();
        
        $snippet = $wpdb->get_row($wpdb->prepare(
            "SELECT id, title, snippet, slug, status, created_at, type
             FROM $table
             WHERE id = %d",
            $snippet_id
        ), ARRAY_A);

        if (!$snippet) {
            wp_send_json_error(array('error' => __('Snippet not found', CodeSnip_AI_Config::get_text_domain())), 404);
        }

        // Escape all output data for security
        $escaped_snippet = array(
            'id' => intval($snippet['id']),
            'title' => esc_html($snippet['title']),
            'snippet' => wp_kses_post($snippet['snippet']), // Allow safe HTML for snippet content
            'slug' => esc_html($snippet['slug']),
            'status' => intval($snippet['status']),
            'created_at' => esc_html($snippet['created_at']),
            'type' => esc_html($snippet['type'])
        );

        wp_send_json_success(array('snippet' => $escaped_snippet));
    }

    /**
     * Update snippet callback
     */
    public function update_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => array('common' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';

        if($snippet_id === '') {
            wp_send_json_error(array('error' => array('common' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain()))), 403);
        }

        if ($snippet_id <= 0) {
            wp_send_json_error(array('error' => array('common' => __('Invalid snippet ID', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        if (empty($title)) {
            wp_send_json_error(array('error' => array('title' => __('Title is required', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        if ($type !== 'html') {
            wp_send_json_error(array('error' => array('common' => __('Type is invalid', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        $raw_input = isset($_POST['snippet']) ? trim(wp_unslash($_POST['snippet'])) : '';
        if (empty($raw_input)) {
            wp_send_json_error(array('error' => array('snippet' => __('Snippet content is required', CodeSnip_AI_Config::get_text_domain()))), 400);
        }

        $disallowed = array('html', 'body', 'script', 'link', 'footer');
        foreach ($disallowed as $tag) {
            if (preg_match('/<' . $tag . '\b/i', $raw_input)) {
                wp_send_json_error(array('error' => array('snippet' => sprintf(__('The <%s> tag is not allowed in snippets.', CodeSnip_AI_Config::get_text_domain()), $tag))), 403);
            }
        }
        
        $allowed_tags = wp_kses_allowed_html('post');
        foreach ($disallowed as $tag) {
            unset($allowed_tags[$tag]);
        }

        $snippet = wp_kses($raw_input, $allowed_tags);

        global $wpdb;
        $table = CodeSnip_AI_Config::get_db_table_name();
        
        $slug = $this->generate_unique_slug($title);
        
        $result = $wpdb->update(
            $table,
            array(
                'title' => $title,
                'slug' => $slug,
                'snippet' => $snippet,
                'type' => $type,
            ),
            array('id' => $snippet_id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('error' => array('common' => __('Failed to update snippet', CodeSnip_AI_Config::get_text_domain()))), 500);
        }

        wp_send_json_success(array('message' => __('Snippet updated successfully', CodeSnip_AI_Config::get_text_domain())));
    }

    /**
     * Save OpenAI settings callback
     */
    public function save_settings_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        // Check if user has permission to manage options
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('error' => __('Insufficient permissions', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';
        $model = isset($_POST['model']) ? sanitize_text_field(wp_unslash($_POST['model'])) : '';
        $max_tokens = isset($_POST['max_tokens']) ? intval($_POST['max_tokens']) : 1500;

        // Validate API key
        if (empty($api_key)) {
            wp_send_json_error(array('error' => __('API key is required', CodeSnip_AI_Config::get_text_domain())), 400);
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]{32,200}$/', $api_key)) {
            wp_send_json_error(array('error' => __('Invalid API key format', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        if (empty($model)) {
            wp_send_json_error(array('error' => __('Model is required', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        // Validate model
        $allowed_models = array(
            'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano',
            'gpt-4o', 'gpt-4o-mini',
            'o1', 'o1-mini', 'o3', 'o3-mini',
            'gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo'
        );
        if (!in_array($model, $allowed_models)) {
            wp_send_json_error(array('error' => __('Invalid model selection', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        // Validate max tokens
        if ($max_tokens < 1 || $max_tokens > 4000) {
            wp_send_json_error(array('error' => __('Max tokens must be between 1 and 4000', CodeSnip_AI_Config::get_text_domain())), 400);
        }

        // Save settings using WordPress options API
        update_option('codesnip_ai_openai_api_key', $api_key);
        update_option('codesnip_ai_openai_model', $model);
        update_option('codesnip_ai_openai_max_tokens', $max_tokens);

        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', CodeSnip_AI_Config::get_text_domain()),
            'data' => array(
                'api_key' => $api_key,
                'model' => $model,
                'max_tokens' => $max_tokens
            )
        ));
    }

    /**
     * Get OpenAI settings callback
     */
    public function get_settings_callback() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], CodeSnip_AI_Config::get_nonce_action())) {
            wp_send_json_error(array('error' => __('Invalid nonce', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        // Check if user has permission to manage options
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('error' => __('Insufficient permissions', CodeSnip_AI_Config::get_text_domain())), 403);
        }

        $settings = array(
            'api_key' => get_option('codesnip_ai_openai_api_key', ''),
            'model' => get_option('codesnip_ai_openai_model', 'gpt-4.1-nano'),
            'max_tokens' => intval(get_option('codesnip_ai_openai_max_tokens', 1500))
        );

        wp_send_json_success(array('settings' => $settings));
    }

    /**
     * Generate unique slug for snippets
     *
     * @param string $title The title to generate slug from
     * @return string Unique slug
     */
    private function generate_unique_slug($title) {
        global $wpdb;

        $table_name = CodeSnip_AI_Config::get_db_table_name();
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
}
