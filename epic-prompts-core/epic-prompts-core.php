<?php
/**
 * Plugin Name: Epic Prompts Core
 * Plugin URI: https://epic-prompts.com
 * Description: Core functionality for Epic Prompts - gamified AI prompts sharing platform
 * Version: 1.0.0
 * Author: Epic Prompts Team
 * Author URI: https://epic-prompts.com
 * License: GPL v2 or later
 * Text Domain: epic-prompts
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EPIC_PROMPTS_VERSION', '1.0.0');
define('EPIC_PROMPTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EPIC_PROMPTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Epic Prompts Core Class
 */
class Epic_Prompts_Core {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize plugin
     */
    private function init() {
        // Load includes
        $this->load_includes();

        // Register hooks
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // AJAX handlers
        add_action('wp_ajax_add_reaction', array($this, 'handle_add_reaction'));
        add_action('wp_ajax_nopriv_add_reaction', array($this, 'ajax_must_login'));
        add_action('wp_ajax_submit_prompt', array($this, 'handle_submit_prompt'));
        add_action('wp_ajax_vote_prompt', array($this, 'handle_vote_prompt'));

        // User registration hooks
        add_action('user_register', array($this, 'init_user_meta'));
        add_action('wp_login', array($this, 'update_user_activity'), 10, 2);
    }

    /**
     * Load include files
     */
    private function load_includes() {
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/post-types.php';
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/taxonomies.php';
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/user-functions.php';
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/xp-system.php';
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/ajax-handlers.php';
        require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/rest-api.php';

        // Load admin functions if in admin area
        if (is_admin()) {
            require_once EPIC_PROMPTS_PLUGIN_DIR . 'includes/admin.php';
        }
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        EP_Post_Types::register();
    }

    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        EP_Taxonomies::register();
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Main stylesheet
        wp_enqueue_style(
            'epic-prompts-style',
            EPIC_PROMPTS_PLUGIN_URL . 'assets/css/epic-prompts.css',
            array(),
            EPIC_PROMPTS_VERSION
        );

        // Main JavaScript
        wp_enqueue_script(
            'epic-prompts-script',
            EPIC_PROMPTS_PLUGIN_URL . 'assets/js/epic-prompts.js',
            array('jquery'),
            EPIC_PROMPTS_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('epic-prompts-script', 'epicPromptsAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epic_prompts_nonce'),
            'user_id' => get_current_user_id(),
            'is_logged_in' => is_user_logged_in()
        ));
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style(
            'epic-prompts-admin',
            EPIC_PROMPTS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            EPIC_PROMPTS_VERSION
        );
    }

    /**
     * Handle AJAX add reaction
     */
    public function handle_add_reaction() {
        EP_Ajax_Handlers::add_reaction();
    }

    /**
     * Handle AJAX submit prompt
     */
    public function handle_submit_prompt() {
        EP_Ajax_Handlers::submit_prompt();
    }

    /**
     * Handle AJAX vote prompt
     */
    public function handle_vote_prompt() {
        EP_Ajax_Handlers::vote_prompt();
    }

    /**
     * AJAX must login response
     */
    public function ajax_must_login() {
        wp_send_json_error(array('message' => __('You must be logged in to perform this action.', 'epic-prompts')));
    }

    /**
     * Initialize user meta on registration
     */
    public function init_user_meta($user_id) {
        EP_User_Functions::init_user_meta($user_id);
    }

    /**
     * Update user activity on login
     */
    public function update_user_activity($user_login, $user) {
        EP_User_Functions::update_last_active($user->ID);
        EP_XP_System::award_daily_login_xp($user->ID);
    }
}

/**
 * Initialize the plugin
 */
function epic_prompts_init() {
    return Epic_Prompts_Core::get_instance();
}

// Start the plugin
epic_prompts_init();
