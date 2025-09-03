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
        // Check if we're on our plugin page
        if (strpos($hook, 'codesnip-ai') === false) {
            return;
        }

        $slug = CodeSnip_AI_Config::get_plugin_slug();
        $var_prefix = 'codesnip_ai_';
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            // Development mode - load from Vite dev server
            wp_enqueue_script(
                $slug . '-vite-client-helper-MODULE', 
                'http://localhost:5173/src/lib/devHotModule.js', 
                array(), 
                null
            );
            
            wp_enqueue_script(
                $slug . '-vite-client-MODULE', 
                'http://localhost:5173/@vite/client', 
                array(), 
                null
            );
            
            wp_enqueue_script(
                $slug . '-index-MODULE', 
                'http://localhost:5173/src/main.jsx', 
                array(), 
                null
            );
        } else {
            wp_enqueue_style(
                $slug . '-styles',
                CODESNIP_AI_PLUGIN_URL . 'build/index.css',
                array(),
                CODESNIP_AI_VERSION
            );
            
            wp_enqueue_script(
                $slug . '-index-MODULE', 
                CODESNIP_AI_PLUGIN_URL . 'build/index.js', 
                array(), 
                CODESNIP_AI_VERSION
            );
        }

        wp_localize_script($slug . '-index-MODULE', $var_prefix, array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce(CodeSnip_AI_Config::get_nonce_action()),
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
        $slug = CodeSnip_AI_Config::get_plugin_slug();
        $new_tag = $html;
        
        if (strpos($handle, 'MODULE') !== false && strpos($handle, $slug) !== false) {
            $new_tag = preg_replace('/<script /', '<script type="module" ', $new_tag);
        }

        return $new_tag;
    }
}
