<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <h1>Epic<span>Prompts</span></h1>
                    <?php endif; ?>
                </a>
            </div>

            <nav class="main-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'primary-menu',
                    'container' => false,
                    'fallback_cb' => false,
                ));
                ?>
                <?php if (!has_nav_menu('primary')) : ?>
                <ul class="primary-menu">
                    <li><a href="<?php echo esc_url(home_url('/prompts')); ?>">Browse Prompts</a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit')); ?>">Submit Prompt</a></li>
                    <li><a href="<?php echo esc_url(home_url('/leaderboard')); ?>">Leaderboard</a></li>
                </ul>
                <?php endif; ?>
            </nav>

            <div class="user-menu">
                <?php if (is_user_logged_in()) : ?>
                    <?php
                    $current_user_id = get_current_user_id();
                    $user_stats = epic_prompts_user_stats($current_user_id);
                    ?>
                    <div class="user-stats">
                        <?php epic_prompts_user_level_badge($current_user_id); ?>
                        <span class="xp-display" id="header-xp-display">
                            <?php echo esc_html(epic_prompts_format_number($user_stats['xp_total'])); ?> XP
                        </span>
                        <span class="coins-display">
                            💰 <?php echo esc_html($user_stats['coins']); ?>
                        </span>
                    </div>
                    <div class="user-profile">
                        <a href="<?php echo esc_url(get_author_posts_url($current_user_id)); ?>">
                            <?php echo get_avatar($current_user_id, 32, '', '', array('class' => 'user-avatar')); ?>
                        </a>
                    </div>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-outline">Logout</a>
                <?php else : ?>
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn-outline">Login</a>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main id="main-content" class="site-main">
