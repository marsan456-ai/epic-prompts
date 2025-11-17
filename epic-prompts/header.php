<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
    <div class="container">
        <div class="header-content">
            <div class="site-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name') . ' - ' . __('Home', 'epic-prompts')); ?>">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <h1 class="site-title">Epic<span>Prompts</span></h1>
                    <?php endif; ?>
                </a>
            </div>

            <nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e('Main navigation', 'epic-prompts'); ?>">
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
                    <li><a href="<?php echo esc_url(home_url('/prompts')); ?>"><?php esc_html_e('Browse Prompts', 'epic-prompts'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/submit')); ?>"><?php esc_html_e('Submit Prompt', 'epic-prompts'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/leaderboard')); ?>"><?php esc_html_e('Leaderboard', 'epic-prompts'); ?></a></li>
                </ul>
                <?php endif; ?>
            </nav>

            <div class="user-menu" role="complementary" aria-label="<?php esc_attr_e('User menu', 'epic-prompts'); ?>">
                <?php if (is_user_logged_in()) : ?>
                    <?php
                    $current_user_id = get_current_user_id();
                    $user_stats = epic_prompts_user_stats($current_user_id);
                    $current_user = wp_get_current_user();
                    ?>
                    <div class="user-stats" aria-label="<?php esc_attr_e('User statistics', 'epic-prompts'); ?>">
                        <?php epic_prompts_user_level_badge($current_user_id); ?>
                        <span class="xp-display" id="header-xp-display" aria-label="<?php esc_attr_e('Experience points', 'epic-prompts'); ?>">
                            <?php echo esc_html(epic_prompts_format_number($user_stats['xp_total'])); ?> XP
                        </span>
                        <span class="coins-display" aria-label="<?php esc_attr_e('Coins balance', 'epic-prompts'); ?>">
                            💰 <?php echo esc_html($user_stats['coins']); ?>
                        </span>
                    </div>
                    <div class="user-profile">
                        <a href="<?php echo esc_url(get_author_posts_url($current_user_id)); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s\'s profile', 'epic-prompts'), $current_user->display_name)); ?>">
                            <?php echo get_avatar($current_user_id, 32, '', $current_user->display_name, array('class' => 'user-avatar')); ?>
                        </a>
                    </div>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-outline" aria-label="<?php esc_attr_e('Log out from your account', 'epic-prompts'); ?>"><?php esc_html_e('Logout', 'epic-prompts'); ?></a>
                <?php else : ?>
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn-outline" aria-label="<?php esc_attr_e('Log in to your account', 'epic-prompts'); ?>"><?php esc_html_e('Login', 'epic-prompts'); ?></a>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary" aria-label="<?php esc_attr_e('Create a new account', 'epic-prompts'); ?>"><?php esc_html_e('Sign Up', 'epic-prompts'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main id="main-content" class="site-main" role="main">
