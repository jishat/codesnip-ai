<?php
/**
 * Database Class
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CodeSnip_AI_Database
 * 
 * Handles database operations for the plugin.
 */
class CodeSnip_AI_Database {

    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'codesnip_snippets';
    }

    /**
     * Create the snippets table
     */
    public function create_table() {
        global $wpdb;
        
        // Check if table already exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") == $this->table_name;
        
        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$this->table_name} (
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
    }

    /**
     * Set default options
     */
    public function set_default_options() {
        // Set default OpenAI settings if they don't exist
        if (!get_option('codesnip_ai_openai_model')) {
            add_option('codesnip_ai_openai_model', 'gpt-4.1-nano');
        }
        if (!get_option('codesnip_ai_openai_max_tokens')) {
            add_option('codesnip_ai_openai_max_tokens', 1500);
        }
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function get_table_name() {
        return $this->table_name;
    }
}
