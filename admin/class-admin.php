<?php
/**
 * Admin Class
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CodeSnip_AI_Admin
 * 
 * Handles admin-specific functionality for the plugin.
 */
class CodeSnip_AI_Admin {

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
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('script_loader_tag', array($this, 'script_loader_tag'), 0, 3);
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_scripts($hook) {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return; // Only in dev mode
        }

        $slug = 'codesnip-ai';
        $var_prefix = 'codesnip_ai_';
        
        wp_enqueue_script(
            $slug . '-vite-client-helper-MODULE', 
            'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/src/lib/devHotModule.js', 
            array(), 
            null
        );
        
        wp_enqueue_script(
            $slug . '-vite-client-MODULE', 
            'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/@vite/client', 
            array(), 
            null
        );
        
        wp_enqueue_script(
            $slug . '-index-MODULE', 
            'http://localhost:5173/wp-content/plugins/codesnip-ai/frontend/src/main.jsx', 
            array(), 
            null
        );

        wp_localize_script($slug . '-index-MODULE', $var_prefix, array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('codesnip_ai_nonce'),
        ));
    }

    /**
     * Filter script loader tag to add module type
     *
     * @param string $html   The link tag for the enqueued script
     * @param string $handle The script's registered handle
     * @param string $href   The script's source URL
     * @return string Modified script tag
     */
    public function script_loader_tag($html, $handle, $href) {
        $slug = 'codesnip-ai';
        $new_tag = $html;
        
        if (strpos($handle, 'MODULE') !== false && strpos($handle, $slug) !== false) {
            $new_tag = preg_replace('/<script /', '<script type="module" ', $new_tag);
        }

        return $new_tag;
    }
}
