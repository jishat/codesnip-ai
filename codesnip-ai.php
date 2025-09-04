<?php
/**
 * Plugin Name: CodeSnip AI
 * Plugin URI: https://wordpress.org/plugins/codesnip-ai
 * Description: Save and manage HTML snippets in WordPress. Optional AI assistant for snippet optimization with OpenAI.
 * Version: 1.0.0
 * Author: Mohammad Azizur Rahman Jishat
 * Author URI: https://github.com/jishat
 * Text Domain: codesnip-ai
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package CodeSnip_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CODESNIP_AI_VERSION', '1.0.0');
define('CODESNIP_AI_PLUGIN_FILE', __FILE__);
define('CODESNIP_AI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CODESNIP_AI_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main CodeSnip AI Plugin Class
 *
 * @since 1.0.0
 */
final class CodeSnip_AI_Plugin {

    /**
     * Plugin instance
     *
     * @var CodeSnip_AI_Plugin
     */
    private static $instance = null;

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = CODESNIP_AI_VERSION;

    /**
     * Get plugin instance
     *
     * @return CodeSnip_AI_Plugin
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->includes();
        $this->init();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Include required files
     */
    private function includes() {
        // Include required files
        require_once CODESNIP_AI_PLUGIN_DIR . 'includes/class-config.php';
        require_once CODESNIP_AI_PLUGIN_DIR . 'includes/class-sidebar-menu.php';
        require_once CODESNIP_AI_PLUGIN_DIR . 'includes/class-ajax-handlers.php';
        require_once CODESNIP_AI_PLUGIN_DIR . 'includes/class-database.php';
        require_once CODESNIP_AI_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once CODESNIP_AI_PLUGIN_DIR . 'admin/class-admin.php';
    }

    /**
     * Initialize plugin
     */
    private function init() {
        // Initialize admin functionality
        if (is_admin()) {
            new CodeSnip_AI_Admin();
            
            // Initialize admin menu on admin_menu hook (proper WordPress hook)
            add_action('admin_menu', array($this, 'init_admin_menu'));
        }

        // Initialize AJAX handlers
        new CodeSnip_AI_Ajax_Handlers();

        // Initialize shortcodes
        new CodeSnip_AI_Shortcodes();
    }

    /**
     * Initialize admin menu
     */
    public function init_admin_menu() {
        $sidebar_menu = new CodeSnip_AI_SideBar_Menu();
        $sidebar_menu->add_menu();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database table
        $database = new CodeSnip_AI_Database();
        $database->create_table();
        $database->set_default_options();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Plugin deactivated
    }
}

/**
 * Initialize the plugin
 */
function codesnip_ai() {
    return CodeSnip_AI_Plugin::instance();
}

// Start the plugin
codesnip_ai();
