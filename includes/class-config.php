<?php
/**
 * Plugin Configuration Class
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CodeSnip_AI_Config
 * 
 * Centralized configuration for the plugin.
 */
class CodeSnip_AI_Config {

    /**
     * Plugin text domain
     */
    const TEXT_DOMAIN = 'codesnip-ai';

    /**
     * Plugin slug
     */
    const PLUGIN_SLUG = 'codesnip-ai';

    /**
     * Plugin name
     */
    const PLUGIN_NAME = 'CodeSnip AI';

    /**
     * Plugin version
     */
    const PLUGIN_VERSION = '1.0.0';

    /**
     * Plugin file path
     */
    const PLUGIN_FILE = CODESNIP_AI_PLUGIN_FILE;

    /**
     * Plugin directory path
     */
    const PLUGIN_DIR = CODESNIP_AI_PLUGIN_DIR;

    /**
     * Plugin URL
     */
    const PLUGIN_URL = CODESNIP_AI_PLUGIN_URL;

    /**
     * Database table prefix for plugin tables
     */
    const DB_TABLE_PREFIX = 'codesnip_snippets';

    /**
     * Nonce action name
     */
    const NONCE_ACTION = 'codesnip_ai_nonce';

    /**
     * Get the text domain
     *
     * @return string
     */
    public static function get_text_domain(): string {
        return self::TEXT_DOMAIN;
    }

    /**
     * Get the plugin slug
     *
     * @return string
     */
    public static function get_plugin_slug(): string {
        return self::PLUGIN_SLUG;
    }

    /**
     * Get the plugin name
     *
     * @return string
     */
    public static function get_plugin_name(): string {
        return self::PLUGIN_NAME;
    }

    /**
     * Get the plugin version
     *
     * @return string
     */
    public static function get_plugin_version(): string {
        return self::PLUGIN_VERSION;
    }

    /**
     * Get the database table name with WordPress prefix
     *
     * @return string
     */
    public static function get_db_table_name(): string {
        global $wpdb;
        return $wpdb->prefix . self::DB_TABLE_PREFIX;
    }

    /**
     * Get the nonce action name
     *
     * @return string
     */
    public static function get_nonce_action(): string {
        return self::NONCE_ACTION;
    }
}
