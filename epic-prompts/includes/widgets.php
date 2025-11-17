<?php
/**
 * Widgets
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Latest Prompts Widget
 */
class EP_Latest_Prompts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'ep_latest_prompts',
            __('Epic Prompts - Latest', 'epic-prompts'),
            array('description' => __('Display latest prompts', 'epic-prompts'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Prompts', 'epic-prompts');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $platform = !empty($instance['platform']) ? $instance['platform'] : '';

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        $query_args = array(
            'post_type' => 'ai_prompt',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        if ($platform) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'ai_platform',
                    'field' => 'slug',
                    'terms' => $platform,
                ),
            );
        }

        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            echo '<ul class="ep-widget-list">';
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', array('style' => 'width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px; float: left;')); ?>
                        <?php endif; ?>
                        <strong><?php the_title(); ?></strong>
                    </a>
                    <small style="display: block; color: #999; margin-top: 5px;">
                        <?php echo epic_prompts_time_ago(get_the_date('c')); ?>
                    </small>
                    <div style="clear: both;"></div>
                </li>
                <?php
            }
            echo '</ul>';
        } else {
            echo '<p>' . __('No prompts found.', 'epic-prompts') . '</p>';
        }

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Prompts', 'epic-prompts');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $platform = !empty($instance['platform']) ? $instance['platform'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Title:', 'epic-prompts'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
                <?php _e('Number of prompts:', 'epic-prompts'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                   value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('platform')); ?>">
                <?php _e('Filter by platform (optional):', 'epic-prompts'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('platform')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('platform')); ?>">
                <option value=""><?php _e('All Platforms', 'epic-prompts'); ?></option>
                <?php
                $platforms = get_terms(array('taxonomy' => 'ai_platform', 'hide_empty' => false));
                foreach ($platforms as $p) {
                    $selected = ($platform === $p->slug) ? 'selected' : '';
                    echo '<option value="' . esc_attr($p->slug) . '" ' . $selected . '>' . esc_html($p->name) . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['platform'] = (!empty($new_instance['platform'])) ? sanitize_text_field($new_instance['platform']) : '';
        return $instance;
    }
}

/**
 * Top Platforms Widget
 */
class EP_Top_Platforms_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'ep_top_platforms',
            __('Epic Prompts - Top Platforms', 'epic-prompts'),
            array('description' => __('Display top AI platforms by prompt count', 'epic-prompts'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Top Platforms', 'epic-prompts');
        $number = !empty($instance['number']) ? absint($instance['number']) : 10;

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        $platforms = get_terms(array(
            'taxonomy' => 'ai_platform',
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => $number,
            'hide_empty' => true,
        ));

        if ($platforms && !is_wp_error($platforms)) {
            echo '<ul class="ep-widget-list">';
            foreach ($platforms as $platform) {
                $link = get_term_link($platform);
                echo '<li>';
                echo '<a href="' . esc_url($link) . '">';
                echo '<strong>' . esc_html($platform->name) . '</strong>';
                echo '<span style="float: right; color: #999;">' . $platform->count . '</span>';
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . __('No platforms found.', 'epic-prompts') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Top Platforms', 'epic-prompts');
        $number = !empty($instance['number']) ? absint($instance['number']) : 10;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Title:', 'epic-prompts'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
                <?php _e('Number of platforms:', 'epic-prompts'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                   value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 10;
        return $instance;
    }
}

/**
 * User Stats Widget
 */
class EP_User_Stats_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'ep_user_stats',
            __('Epic Prompts - My Stats', 'epic-prompts'),
            array('description' => __('Display current user statistics', 'epic-prompts'))
        );
    }

    public function widget($args, $instance) {
        if (!is_user_logged_in()) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : __('My Stats', 'epic-prompts');

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        $user_id = get_current_user_id();
        $stats = EP_User_Functions::get_user_stats($user_id);
        $xp_info = EP_User_Functions::get_xp_for_next_level($user_id);

        ?>
        <div class="ep-user-stats-widget" style="padding: 10px 0;">
            <div style="text-align: center; margin-bottom: 15px;">
                <div style="font-size: 2rem; font-weight: 700; color: #6366F1;">
                    Level <?php echo esc_html($stats['level']); ?>
                </div>
                <div style="color: #6B7280; font-size: 0.875rem;">
                    <?php echo esc_html($stats['title']); ?>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.875rem;">
                    <span>Progress to Level <?php echo $stats['level'] + 1; ?></span>
                    <span><?php echo $xp_info['percentage']; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: <?php echo $xp_info['percentage']; ?>%;"></div>
                </div>
            </div>

            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between;">
                    <span>💰 Coins</span>
                    <strong><?php echo number_format($stats['coins']); ?></strong>
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between;">
                    <span>💡 Prompts</span>
                    <strong><?php echo number_format($stats['prompts_submitted']); ?></strong>
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between;">
                    <span>🔥 Streak</span>
                    <strong><?php echo $stats['streak_days']; ?> days</strong>
                </li>
                <li style="padding: 8px 0; display: flex; justify-content: space-between;">
                    <span>🏆 Reputation</span>
                    <strong><?php echo number_format($stats['reputation']); ?></strong>
                </li>
            </ul>

            <div style="margin-top: 15px; text-align: center;">
                <a href="<?php echo esc_url(get_author_posts_url($user_id)); ?>" class="btn btn-primary" style="display: inline-block; padding: 8px 16px;">
                    View Profile
                </a>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Stats', 'epic-prompts');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Title:', 'epic-prompts'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p class="description">
            <?php _e('This widget is only visible to logged-in users.', 'epic-prompts'); ?>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// Register widgets
function ep_register_widgets() {
    register_widget('EP_Latest_Prompts_Widget');
    register_widget('EP_Top_Platforms_Widget');
    register_widget('EP_User_Stats_Widget');
}
add_action('widgets_init', 'ep_register_widgets');
