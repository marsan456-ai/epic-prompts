<?php
/**
 * Theme Customizer Settings
 * Appearance > Customize settings for Epic Prompts
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Customizer settings
 */
function epic_prompts_customize_register($wp_customize) {

    // ========================================
    // SECTION: Brand Colors
    // ========================================
    $wp_customize->add_section('epic_prompts_colors', array(
        'title' => __('Brand Colors', 'epic-prompts'),
        'priority' => 30,
        'description' => __('Customize the main colors of your Epic Prompts platform', 'epic-prompts'),
    ));

    // Primary Color
    $wp_customize->add_setting('epic_prompts_primary_color', array(
        'default' => '#6366F1',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'epic_prompts_primary_color', array(
        'label' => __('Primary Color', 'epic-prompts'),
        'description' => __('Main brand color (buttons, links, accents)', 'epic-prompts'),
        'section' => 'epic_prompts_colors',
        'settings' => 'epic_prompts_primary_color',
    )));

    // Secondary Color
    $wp_customize->add_setting('epic_prompts_secondary_color', array(
        'default' => '#EC4899',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'epic_prompts_secondary_color', array(
        'label' => __('Secondary Color', 'epic-prompts'),
        'description' => __('Secondary brand color (highlights, badges)', 'epic-prompts'),
        'section' => 'epic_prompts_colors',
        'settings' => 'epic_prompts_secondary_color',
    )));

    // Success Color
    $wp_customize->add_setting('epic_prompts_success_color', array(
        'default' => '#10B981',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'epic_prompts_success_color', array(
        'label' => __('Success Color', 'epic-prompts'),
        'description' => __('Success messages and positive actions', 'epic-prompts'),
        'section' => 'epic_prompts_colors',
        'settings' => 'epic_prompts_success_color',
    )));

    // Warning Color
    $wp_customize->add_setting('epic_prompts_warning_color', array(
        'default' => '#F59E0B',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'epic_prompts_warning_color', array(
        'label' => __('Warning Color', 'epic-prompts'),
        'description' => __('Warnings and attention messages', 'epic-prompts'),
        'section' => 'epic_prompts_colors',
        'settings' => 'epic_prompts_warning_color',
    )));

    // ========================================
    // SECTION: Typography
    // ========================================
    $wp_customize->add_section('epic_prompts_typography', array(
        'title' => __('Typography', 'epic-prompts'),
        'priority' => 35,
        'description' => __('Customize fonts and text styles', 'epic-prompts'),
    ));

    // Heading Font
    $wp_customize->add_setting('epic_prompts_heading_font', array(
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_heading_font', array(
        'label' => __('Heading Font', 'epic-prompts'),
        'description' => __('Font family for headings', 'epic-prompts'),
        'section' => 'epic_prompts_typography',
        'type' => 'select',
        'choices' => array(
            'Inter' => 'Inter (Default)',
            'Poppins' => 'Poppins',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Lato' => 'Lato',
            'Nunito' => 'Nunito',
            'PT Sans' => 'PT Sans',
            'Source Sans Pro' => 'Source Sans Pro',
        ),
    ));

    // Body Font
    $wp_customize->add_setting('epic_prompts_body_font', array(
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_body_font', array(
        'label' => __('Body Font', 'epic-prompts'),
        'description' => __('Font family for body text', 'epic-prompts'),
        'section' => 'epic_prompts_typography',
        'type' => 'select',
        'choices' => array(
            'Inter' => 'Inter (Default)',
            'Poppins' => 'Poppins',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Lato' => 'Lato',
            'Nunito' => 'Nunito',
            'PT Sans' => 'PT Sans',
            'Source Sans Pro' => 'Source Sans Pro',
        ),
    ));

    // Font Size
    $wp_customize->add_setting('epic_prompts_base_font_size', array(
        'default' => '16',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_base_font_size', array(
        'label' => __('Base Font Size', 'epic-prompts'),
        'description' => __('Base font size in pixels (14-20)', 'epic-prompts'),
        'section' => 'epic_prompts_typography',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 14,
            'max' => 20,
            'step' => 1,
        ),
    ));

    // ========================================
    // SECTION: Layout Settings
    // ========================================
    $wp_customize->add_section('epic_prompts_layout', array(
        'title' => __('Layout Settings', 'epic-prompts'),
        'priority' => 40,
        'description' => __('Customize the layout and structure', 'epic-prompts'),
    ));

    // Container Width
    $wp_customize->add_setting('epic_prompts_container_width', array(
        'default' => '1200',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_container_width', array(
        'label' => __('Container Max Width', 'epic-prompts'),
        'description' => __('Maximum width of content container in pixels', 'epic-prompts'),
        'section' => 'epic_prompts_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 960,
            'max' => 1600,
            'step' => 20,
        ),
    ));

    // Border Radius
    $wp_customize->add_setting('epic_prompts_border_radius', array(
        'default' => '12',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_border_radius', array(
        'label' => __('Border Radius', 'epic-prompts'),
        'description' => __('Roundness of cards and buttons in pixels', 'epic-prompts'),
        'section' => 'epic_prompts_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 24,
            'step' => 2,
        ),
    ));

    // Cards per Row
    $wp_customize->add_setting('epic_prompts_cards_per_row', array(
        'default' => '3',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_cards_per_row', array(
        'label' => __('Prompt Cards per Row', 'epic-prompts'),
        'description' => __('Number of prompt cards to display per row', 'epic-prompts'),
        'section' => 'epic_prompts_layout',
        'type' => 'select',
        'choices' => array(
            '2' => '2 Cards',
            '3' => '3 Cards (Default)',
            '4' => '4 Cards',
        ),
    ));

    // ========================================
    // SECTION: Homepage Settings
    // ========================================
    $wp_customize->add_section('epic_prompts_homepage', array(
        'title' => __('Homepage Settings', 'epic-prompts'),
        'priority' => 45,
        'description' => __('Customize the homepage appearance', 'epic-prompts'),
    ));

    // Hero Title
    $wp_customize->add_setting('epic_prompts_hero_title', array(
        'default' => 'Epic Prompts for Every AI',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_hero_title', array(
        'label' => __('Hero Title', 'epic-prompts'),
        'description' => __('Main title on homepage hero section', 'epic-prompts'),
        'section' => 'epic_prompts_homepage',
        'type' => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('epic_prompts_hero_subtitle', array(
        'default' => 'Discover, share, and master AI prompts for ChatGPT, Claude, Midjourney, Suno, GitHub Copilot, and 50+ AI platforms. Earn XP, level up, and compete!',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_hero_subtitle', array(
        'label' => __('Hero Subtitle', 'epic-prompts'),
        'description' => __('Subtitle text on homepage hero section', 'epic-prompts'),
        'section' => 'epic_prompts_homepage',
        'type' => 'textarea',
    ));

    // Show Stats Section
    $wp_customize->add_setting('epic_prompts_show_stats', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_show_stats', array(
        'label' => __('Show Statistics Section', 'epic-prompts'),
        'description' => __('Display platform statistics on homepage', 'epic-prompts'),
        'section' => 'epic_prompts_homepage',
        'type' => 'checkbox',
    ));

    // Show Leaderboard
    $wp_customize->add_setting('epic_prompts_show_leaderboard', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_show_leaderboard', array(
        'label' => __('Show Leaderboard Preview', 'epic-prompts'),
        'description' => __('Display top contributors on homepage', 'epic-prompts'),
        'section' => 'epic_prompts_homepage',
        'type' => 'checkbox',
    ));

    // ========================================
    // SECTION: Social Links
    // ========================================
    $wp_customize->add_section('epic_prompts_social', array(
        'title' => __('Social Links', 'epic-prompts'),
        'priority' => 50,
        'description' => __('Add your social media links', 'epic-prompts'),
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter/X',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'github' => 'GitHub',
        'discord' => 'Discord',
        'telegram' => 'Telegram',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("epic_prompts_social_{$network}", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control("epic_prompts_social_{$network}", array(
            'label' => $label . ' ' . __('URL', 'epic-prompts'),
            'section' => 'epic_prompts_social',
            'type' => 'url',
        ));
    }

    // ========================================
    // SECTION: Advanced Settings
    // ========================================
    $wp_customize->add_section('epic_prompts_advanced', array(
        'title' => __('Advanced Settings', 'epic-prompts'),
        'priority' => 55,
        'description' => __('Advanced customization options', 'epic-prompts'),
    ));

    // Enable Animations
    $wp_customize->add_setting('epic_prompts_enable_animations', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_enable_animations', array(
        'label' => __('Enable Animations', 'epic-prompts'),
        'description' => __('Enable hover and scroll animations', 'epic-prompts'),
        'section' => 'epic_prompts_advanced',
        'type' => 'checkbox',
    ));

    // Dark Mode Toggle
    $wp_customize->add_setting('epic_prompts_enable_dark_mode', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_enable_dark_mode', array(
        'label' => __('Enable Dark Mode Toggle', 'epic-prompts'),
        'description' => __('Allow users to switch to dark mode', 'epic-prompts'),
        'section' => 'epic_prompts_advanced',
        'type' => 'checkbox',
    ));

    // Custom CSS
    $wp_customize->add_setting('epic_prompts_custom_css', array(
        'default' => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('epic_prompts_custom_css', array(
        'label' => __('Custom CSS', 'epic-prompts'),
        'description' => __('Add custom CSS code', 'epic-prompts'),
        'section' => 'epic_prompts_advanced',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'epic_prompts_customize_register');

/**
 * Output customizer CSS
 */
function epic_prompts_customizer_css() {
    // Get settings
    $primary_color = get_theme_mod('epic_prompts_primary_color', '#6366F1');
    $secondary_color = get_theme_mod('epic_prompts_secondary_color', '#EC4899');
    $success_color = get_theme_mod('epic_prompts_success_color', '#10B981');
    $warning_color = get_theme_mod('epic_prompts_warning_color', '#F59E0B');
    $heading_font = get_theme_mod('epic_prompts_heading_font', 'Inter');
    $body_font = get_theme_mod('epic_prompts_body_font', 'Inter');
    $base_font_size = get_theme_mod('epic_prompts_base_font_size', '16');
    $container_width = get_theme_mod('epic_prompts_container_width', '1200');
    $border_radius = get_theme_mod('epic_prompts_border_radius', '12');
    $custom_css = get_theme_mod('epic_prompts_custom_css', '');

    ?>
    <style type="text/css">
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=<?php echo urlencode($heading_font); ?>:wght@400;600;700&family=<?php echo urlencode($body_font); ?>:wght@400;500;600&display=swap');

        /* Root Variables */
        :root {
            --ep-primary: <?php echo esc_attr($primary_color); ?>;
            --ep-secondary: <?php echo esc_attr($secondary_color); ?>;
            --ep-success: <?php echo esc_attr($success_color); ?>;
            --ep-warning: <?php echo esc_attr($warning_color); ?>;
            --ep-heading-font: '<?php echo esc_attr($heading_font); ?>', sans-serif;
            --ep-body-font: '<?php echo esc_attr($body_font); ?>', sans-serif;
            --ep-font-size: <?php echo esc_attr($base_font_size); ?>px;
            --ep-container-width: <?php echo esc_attr($container_width); ?>px;
            --ep-border-radius: <?php echo esc_attr($border_radius); ?>px;
        }

        /* Typography */
        body {
            font-family: var(--ep-body-font);
            font-size: var(--ep-font-size);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--ep-heading-font);
        }

        /* Container */
        .container {
            max-width: var(--ep-container-width);
        }

        /* Colors */
        .btn-primary, .btn.btn-primary {
            background-color: var(--ep-primary) !important;
        }

        .btn-secondary, .btn.btn-secondary {
            background-color: var(--ep-secondary) !important;
        }

        .btn-success {
            background-color: var(--ep-success) !important;
        }

        a {
            color: var(--ep-primary);
        }

        .level-badge {
            background-color: var(--ep-primary);
        }

        /* Border Radius */
        .prompt-card,
        .stat-card,
        .btn,
        .widget,
        .search-input,
        .form-input {
            border-radius: var(--ep-border-radius);
        }

        /* Custom CSS */
        <?php echo $custom_css; ?>
    </style>
    <?php
}
add_action('wp_head', 'epic_prompts_customizer_css');
