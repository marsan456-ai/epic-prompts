<?php
/**
 * Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Latest Prompts Shortcode
 * Usage: [epic_prompts_latest number="6" platform="chatgpt-4" type="text-generation"]
 */
function epic_prompts_latest_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number' => 6,
        'platform' => '',
        'type' => '',
        'category' => '',
        'columns' => 3,
    ), $atts);

    $query_args = array(
        'post_type' => 'ai_prompt',
        'posts_per_page' => intval($atts['number']),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $tax_query = array();

    if (!empty($atts['platform'])) {
        $tax_query[] = array(
            'taxonomy' => 'ai_platform',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['platform']),
        );
    }

    if (!empty($atts['type'])) {
        $tax_query[] = array(
            'taxonomy' => 'prompt_type',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['type']),
        );
    }

    if (!empty($atts['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'prompt_category',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['category']),
        );
    }

    if (!empty($tax_query)) {
        $query_args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($query_args);

    ob_start();

    if ($query->have_posts()) {
        echo '<div class="prompts-grid" style="grid-template-columns: repeat(' . intval($atts['columns']) . ', 1fr);">';
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/prompt-card');
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>' . __('No prompts found.', 'epic-prompts') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('epic_prompts_latest', 'epic_prompts_latest_shortcode');

/**
 * User Stats Shortcode
 * Usage: [epic_prompts_user_stats user_id="123"]
 */
function epic_prompts_user_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'user_id' => get_current_user_id(),
    ), $atts);

    $user_id = intval($atts['user_id']);

    if (!$user_id) {
        return '<p>' . __('Please log in to view stats.', 'epic-prompts') . '</p>';
    }

    $stats = EP_User_Functions::get_user_stats($user_id);
    $xp_info = EP_User_Functions::get_xp_for_next_level($user_id);

    ob_start();
    ?>
    <div class="ep-user-stats-shortcode" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: #6366F1;">
                    <?php echo esc_html($stats['level']); ?>
                </div>
                <div style="color: #6B7280;">Level</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: #EC4899;">
                    <?php echo number_format($stats['xp_total']); ?>
                </div>
                <div style="color: #6B7280;">Total XP</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: #10B981;">
                    <?php echo number_format($stats['coins']); ?>
                </div>
                <div style="color: #6B7280;">Coins</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: #F59E0B;">
                    <?php echo $stats['streak_days']; ?>
                </div>
                <div style="color: #6B7280;">Day Streak</div>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-weight: 600;">Progress to Level <?php echo $stats['level'] + 1; ?></span>
                <span><?php echo $xp_info['percentage']; ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: <?php echo $xp_info['percentage']; ?>%;"></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <strong>Prompts Submitted:</strong> <?php echo number_format($stats['prompts_submitted']); ?>
            </div>
            <div>
                <strong>Votes Cast:</strong> <?php echo number_format($stats['votes_cast']); ?>
            </div>
            <div>
                <strong>Reviews Written:</strong> <?php echo number_format($stats['reviews_written']); ?>
            </div>
            <div>
                <strong>Reputation:</strong> <?php echo number_format($stats['reputation']); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('epic_prompts_user_stats', 'epic_prompts_user_stats_shortcode');

/**
 * Leaderboard Shortcode
 * Usage: [epic_prompts_leaderboard number="10" type="total"]
 */
function epic_prompts_leaderboard_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number' => 10,
        'type' => 'total', // total or monthly
    ), $atts);

    $leaderboard = EP_XP_System::get_leaderboard(intval($atts['number']), $atts['type']);

    ob_start();

    if (!empty($leaderboard)) {
        ?>
        <div class="leaderboard-table" style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <?php foreach ($leaderboard as $entry) : ?>
                <div class="leaderboard-row">
                    <div class="rank-number <?php echo ($entry['rank'] <= 3) ? 'top-3' : ''; ?>">
                        <?php if ($entry['rank'] == 1) : ?>
                            🥇
                        <?php elseif ($entry['rank'] == 2) : ?>
                            🥈
                        <?php elseif ($entry['rank'] == 3) : ?>
                            🥉
                        <?php else : ?>
                            #<?php echo $entry['rank']; ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-info">
                        <img src="<?php echo esc_url($entry['avatar']); ?>" alt="<?php echo esc_attr($entry['username']); ?>" class="user-avatar">
                        <div>
                            <div style="font-weight: 600;">
                                <?php echo esc_html($entry['username']); ?>
                            </div>
                            <div style="color: #6B7280; font-size: 0.875rem;">
                                <?php echo esc_html($entry['title']); ?>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <span class="level-badge">Lv <?php echo esc_html($entry['level']); ?></span>
                    </div>
                    <div style="text-align: right; font-weight: 700; color: #6366F1;">
                        <?php echo number_format($entry['xp']); ?> XP
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        echo '<p>' . __('No leaderboard data available.', 'epic-prompts') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('epic_prompts_leaderboard', 'epic_prompts_leaderboard_shortcode');

/**
 * Platform List Shortcode
 * Usage: [epic_prompts_platforms group="text-chat"]
 */
function epic_prompts_platforms_shortcode($atts) {
    $atts = shortcode_atts(array(
        'group' => '', // text-chat, image, video, code, audio
        'orderby' => 'count', // name, count
        'order' => 'DESC',
    ), $atts);

    $args = array(
        'taxonomy' => 'ai_platform',
        'hide_empty' => false,
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );

    $platforms = get_terms($args);

    // Filter by group if specified
    if (!empty($atts['group'])) {
        $platforms = array_filter($platforms, function($platform) use ($atts) {
            $name = $platform->name;
            $group = $atts['group'];

            if ($group === 'text-chat') {
                return strpos($name, 'ChatGPT') !== false || strpos($name, 'Claude') !== false ||
                       strpos($name, 'Gemini') !== false || strpos($name, 'Copilot') !== false;
            } elseif ($group === 'image') {
                return strpos($name, 'Midjourney') !== false || strpos($name, 'DALL-E') !== false ||
                       strpos($name, 'Stable Diffusion') !== false;
            } elseif ($group === 'video') {
                return strpos($name, 'Runway') !== false || strpos($name, 'Sora') !== false ||
                       strpos($name, 'Pika') !== false;
            } elseif ($group === 'code') {
                return strpos($name, 'Copilot') !== false || strpos($name, 'Cursor') !== false;
            } elseif ($group === 'audio') {
                return strpos($name, 'ElevenLabs') !== false || strpos($name, 'Suno') !== false;
            }

            return true;
        });
    }

    ob_start();

    if ($platforms && !is_wp_error($platforms)) {
        echo '<ul class="ep-platforms-list" style="list-style: none; padding: 0; margin: 0;">';
        foreach ($platforms as $platform) {
            $link = get_term_link($platform);
            ?>
            <li style="padding: 10px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
                <a href="<?php echo esc_url($link); ?>" style="font-weight: 600;">
                    <?php echo esc_html($platform->name); ?>
                </a>
                <span style="background: #F3F4F6; padding: 4px 12px; border-radius: 9999px; font-size: 0.875rem; color: #6B7280;">
                    <?php echo $platform->count; ?> prompts
                </span>
            </li>
            <?php
        }
        echo '</ul>';
    } else {
        echo '<p>' . __('No platforms found.', 'epic-prompts') . '</p>';
    }

    return ob_get_clean();
}
add_shortcode('epic_prompts_platforms', 'epic_prompts_platforms_shortcode');

/**
 * Submit Button Shortcode
 * Usage: [epic_prompts_submit_button text="Share Your Prompt"]
 */
function epic_prompts_submit_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => __('Submit Prompt', 'epic-prompts'),
        'style' => 'primary', // primary, secondary, outline
    ), $atts);

    $class = 'btn btn-' . sanitize_html_class($atts['style']);

    return '<a href="' . esc_url(home_url('/submit')) . '" class="' . esc_attr($class) . '">' . esc_html($atts['text']) . '</a>';
}
add_shortcode('epic_prompts_submit_button', 'epic_prompts_submit_button_shortcode');
