<?php
/**
 * Epic Prompts Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
function epic_prompts_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'epic-prompts'),
        'footer' => __('Footer Menu', 'epic-prompts'),
    ));

    // Image sizes
    add_image_size('prompt-thumbnail', 400, 400, true);
    add_image_size('prompt-large', 1200, 800, false);
}
add_action('after_setup_theme', 'epic_prompts_theme_setup');

/**
 * Enqueue scripts and styles
 */
function epic_prompts_enqueue_assets() {
    // Theme main stylesheet (style.css) - Changed handle to avoid conflict with plugin
    wp_enqueue_style('epic-prompts-theme', get_stylesheet_uri(), array(), '1.0.1');

    // Custom CSS (depends on main theme styles)
    wp_enqueue_style('epic-prompts-custom', get_template_directory_uri() . '/css/custom.css', array('epic-prompts-theme'), '1.0.1');

    // Professional CSS (depends on theme and custom CSS)
    wp_enqueue_style('epic-prompts-professional', get_template_directory_uri() . '/css/professional.css', array('epic-prompts-theme', 'epic-prompts-custom'), '1.0.1');

    // jQuery (included in WordPress)
    wp_enqueue_script('jquery');

    // Main JavaScript
    wp_enqueue_script('epic-prompts-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.1', true);

    // Clipboard.js for copy functionality
    wp_enqueue_script('clipboard-js', 'https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js', array(), '2.0.11', true);

    // Canvas Confetti for level up animation
    wp_enqueue_script('canvas-confetti', 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js', array(), '1.6.0', true);

    // Localize script
    wp_localize_script('epic-prompts-main', 'epicPromptsTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('epic_prompts_nonce'),
        'user_id' => get_current_user_id(),
        'is_logged_in' => is_user_logged_in(),
    ));
}
add_action('wp_enqueue_scripts', 'epic_prompts_enqueue_assets');

/**
 * Register widget areas
 */
function epic_prompts_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'epic-prompts'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'epic-prompts'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer', 'epic-prompts'),
        'id' => 'footer-1',
        'description' => __('Add widgets here for footer.', 'epic-prompts'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'epic_prompts_widgets_init');

/**
 * Custom excerpt length
 */
function epic_prompts_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'epic_prompts_excerpt_length');

/**
 * Custom excerpt more
 */
function epic_prompts_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'epic_prompts_excerpt_more');

/**
 * Get formatted number (1.2k, 5.3M, etc)
 */
function epic_prompts_format_number($number) {
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    }
    if ($number >= 1000) {
        return number_format($number / 1000, 1) . 'k';
    }
    return number_format($number);
}

/**
 * Get prompt reactions
 */
function epic_prompts_get_reactions($prompt_id) {
    $reactions = get_post_meta($prompt_id, 'reactions', true);

    if (!is_array($reactions) || empty($reactions)) {
        return array(
            'fire' => 0,
            'gem' => 0,
            'creative' => 0,
            'rocket' => 0,
            'mindblown' => 0,
        );
    }

    return $reactions;
}

/**
 * Get total reactions count
 */
function epic_prompts_get_total_reactions($prompt_id) {
    $reactions = epic_prompts_get_reactions($prompt_id);
    return array_sum($reactions);
}

/**
 * Display user level badge
 */
function epic_prompts_user_level_badge($user_id) {
    if (!class_exists('EP_User_Functions')) {
        echo '<span class="level-badge">Lv 1</span>';
        return;
    }

    $level = EP_User_Functions::get_user_level($user_id);
    $title = EP_User_Functions::get_user_title($user_id);

    echo '<span class="level-badge" title="' . esc_attr($title) . '">';
    echo 'Lv ' . esc_html($level);
    echo '</span>';
}

/**
 * Display user stats
 */
function epic_prompts_user_stats($user_id) {
    if (!class_exists('EP_User_Functions')) {
        return array(
            'xp_total' => 0,
            'level' => 1,
            'coins' => 0,
            'prompts_count' => 0,
        );
    }

    $stats = EP_User_Functions::get_user_stats($user_id);
    return $stats;
}

/**
 * Check if user has reacted to prompt
 */
function epic_prompts_user_reaction($prompt_id, $user_id) {
    $user_reaction_key = 'user_reaction_' . $user_id . '_' . $prompt_id;
    return get_transient($user_reaction_key);
}

/**
 * Get time ago string
 */
function epic_prompts_time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = current_time('timestamp');
    $time_difference = $current_time - $time_ago;

    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) {
        return __('just now', 'epic-prompts');
    } elseif ($minutes <= 60) {
        return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'epic-prompts'), $minutes);
    } elseif ($hours <= 24) {
        return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'epic-prompts'), $hours);
    } elseif ($days <= 7) {
        return sprintf(_n('%d day ago', '%d days ago', $days, 'epic-prompts'), $days);
    } elseif ($weeks <= 4.3) {
        return sprintf(_n('%d week ago', '%d weeks ago', $weeks, 'epic-prompts'), $weeks);
    } elseif ($months <= 12) {
        return sprintf(_n('%d month ago', '%d months ago', $months, 'epic-prompts'), $months);
    } else {
        return sprintf(_n('%d year ago', '%d years ago', $years, 'epic-prompts'), $years);
    }
}

/**
 * Increment prompt views
 */
function epic_prompts_increment_views($prompt_id) {
    $views = (int) get_post_meta($prompt_id, 'views_count', true);
    update_post_meta($prompt_id, 'views_count', $views + 1);
}

/**
 * Get prompt platform
 */
function epic_prompts_get_platform($prompt_id) {
    $platforms = get_the_terms($prompt_id, 'ai_platform');
    if ($platforms && !is_wp_error($platforms)) {
        return $platforms[0]->name;
    }
    return '';
}

/**
 * Get prompt category
 */
function epic_prompts_get_category($prompt_id) {
    $categories = get_the_terms($prompt_id, 'prompt_category');
    if ($categories && !is_wp_error($categories)) {
        return $categories[0]->name;
    }
    return '';
}

/**
 * Add favicon and app icons
 */
function epic_prompts_add_favicon() {
    $favicon_url = get_theme_mod('epic_prompts_favicon');
    if (!$favicon_url) {
        // Use default favicon if custom not set
        $favicon_url = get_template_directory_uri() . '/assets/images/favicon.ico';
    }

    echo '<link rel="icon" type="image/x-icon" href="' . esc_url($favicon_url) . '">' . "\n";
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url(str_replace('.ico', '-180x180.png', $favicon_url)) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url(str_replace('.ico', '-32x32.png', $favicon_url)) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url(str_replace('.ico', '-16x16.png', $favicon_url)) . '">' . "\n";
}
add_action('wp_head', 'epic_prompts_add_favicon', 0);

/**
 * Add async/defer attributes to scripts for performance
 */
function epic_prompts_defer_scripts($tag, $handle, $src) {
    // Scripts to defer (non-critical)
    $defer_scripts = array(
        'epic-prompts-main',
        'clipboard-js',
        'canvas-confetti'
    );

    // Scripts to load async
    $async_scripts = array();

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'epic_prompts_defer_scripts', 10, 3);

/**
 * Add preconnect for external domains (performance optimization)
 */
function epic_prompts_add_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">' . "\n";
}
add_action('wp_head', 'epic_prompts_add_preconnect', 0);

/**
 * Improve accessibility - add skip to content link
 */
function epic_prompts_skip_link() {
    echo '<a class="skip-link screen-reader-text" href="#main-content">' . esc_html__('Skip to content', 'epic-prompts') . '</a>' . "\n";
}
add_action('wp_body_open', 'epic_prompts_skip_link', 1);

/**
 * Add theme color for mobile browsers
 */
function epic_prompts_theme_color() {
    echo '<meta name="theme-color" content="#6366F1">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#6366F1">' . "\n";
}
add_action('wp_head', 'epic_prompts_theme_color', 0);

/**
 * Add proper alt text to avatars
 */
function epic_prompts_avatar_alt_text($avatar, $id_or_email, $size, $default, $alt, $args) {
    if (empty($alt)) {
        $user = false;

        if (is_numeric($id_or_email)) {
            $user = get_user_by('id', $id_or_email);
        } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
            $user = get_user_by('id', $id_or_email->user_id);
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        }

        if ($user && is_object($user)) {
            $alt = sprintf(__('%s\'s avatar', 'epic-prompts'), $user->display_name);
            $avatar = str_replace(' alt=\'\'', ' alt=\'' . esc_attr($alt) . '\'', $avatar);
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'epic_prompts_avatar_alt_text', 10, 6);

/**
 * Disable emojis for performance (optional)
 */
function epic_prompts_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
// Uncomment to enable: add_action('init', 'epic_prompts_disable_emojis');

/**
 * Load theme includes
 */
require_once get_template_directory() . '/includes/seo-functions.php';
require_once get_template_directory() . '/includes/widgets.php';
require_once get_template_directory() . '/includes/shortcodes.php';
require_once get_template_directory() . '/includes/customizer.php';
require_once get_template_directory() . '/includes/onboarding.php';
