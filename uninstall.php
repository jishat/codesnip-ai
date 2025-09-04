<?php
/**
 * Uninstall file for CodeSnip AI
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It removes all plugin data including database tables and options.
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has permission to uninstall
if (!current_user_can('activate_plugins')) {
    return;
}

// Delete plugin options
delete_option('codesnip_ai_openai_api_key');
delete_option('codesnip_ai_openai_model');
delete_option('codesnip_ai_openai_max_tokens');

// Drop custom tables
global $wpdb;
$table_name = $wpdb->prefix . 'codesnip_snippets';

$wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %s", $table_name));

// Clear any cached data that has been removed
wp_cache_flush();
