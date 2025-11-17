<?php
/**
 * Admin Functions and Settings
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_Admin {

    /**
     * Initialize admin hooks
     */
    public static function init() {
        // Add settings page
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));

        // Custom columns for prompts
        add_filter('manage_ai_prompt_posts_columns', array(__CLASS__, 'prompt_columns'));
        add_action('manage_ai_prompt_posts_custom_column', array(__CLASS__, 'prompt_column_content'), 10, 2);
        add_filter('manage_edit-ai_prompt_sortable_columns', array(__CLASS__, 'prompt_sortable_columns'));

        // Custom columns for platforms taxonomy
        add_filter('manage_edit-ai_platform_columns', array(__CLASS__, 'platform_columns'));
        add_filter('manage_ai_platform_custom_column', array(__CLASS__, 'platform_column_content'), 10, 3);

        // Custom columns for prompt types taxonomy
        add_filter('manage_edit-prompt_type_columns', array(__CLASS__, 'prompt_type_columns'));
        add_filter('manage_prompt_type_custom_column', array(__CLASS__, 'prompt_type_column_content'), 10, 3);

        // Meta boxes
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post_ai_prompt', array(__CLASS__, 'save_prompt_meta'), 10, 2);

        // Admin notices
        add_action('admin_notices', array(__CLASS__, 'admin_notices'));

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
    }

    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ai_prompt',
            __('Epic Prompts Settings', 'epic-prompts'),
            __('Settings', 'epic-prompts'),
            'manage_options',
            'epic-prompts-settings',
            array(__CLASS__, 'settings_page')
        );

        add_submenu_page(
            'edit.php?post_type=ai_prompt',
            __('Platform Manager', 'epic-prompts'),
            __('Platforms Manager', 'epic-prompts'),
            'manage_options',
            'epic-prompts-platforms',
            array(__CLASS__, 'platforms_page')
        );

        add_submenu_page(
            'edit.php?post_type=ai_prompt',
            __('Statistics', 'epic-prompts'),
            __('Statistics', 'epic-prompts'),
            'manage_options',
            'epic-prompts-stats',
            array(__CLASS__, 'stats_page')
        );
    }

    /**
     * Custom columns for prompts
     */
    public static function prompt_columns($columns) {
        $new_columns = array();

        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['thumbnail'] = __('Image', 'epic-prompts');
        $new_columns['platform'] = __('Platform', 'epic-prompts');
        $new_columns['prompt_type'] = __('Type', 'epic-prompts');
        $new_columns['reactions'] = __('Reactions', 'epic-prompts');
        $new_columns['rating'] = __('Rating', 'epic-prompts');
        $new_columns['views'] = __('Views', 'epic-prompts');
        $new_columns['author'] = $columns['author'];
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Custom column content for prompts
     */
    public static function prompt_column_content($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                $result_image = get_post_meta($post_id, 'result_image', true);
                if ($result_image) {
                    echo wp_get_attachment_image($result_image, array(50, 50));
                } elseif (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;

            case 'platform':
                $platforms = get_the_terms($post_id, 'ai_platform');
                if ($platforms && !is_wp_error($platforms)) {
                    $platform_names = wp_list_pluck($platforms, 'name');
                    echo '<span class="badge badge-platform" style="background: #DBEAFE; color: #1E40AF; padding: 2px 8px; border-radius: 3px; font-size: 11px;">' . esc_html(implode(', ', $platform_names)) . '</span>';
                } else {
                    echo '—';
                }
                break;

            case 'prompt_type':
                $types = get_the_terms($post_id, 'prompt_type');
                if ($types && !is_wp_error($types)) {
                    $type_names = wp_list_pluck($types, 'name');
                    echo '<span class="badge badge-type" style="background: #FCE7F3; color: #9F1239; padding: 2px 8px; border-radius: 3px; font-size: 11px;">' . esc_html(implode(', ', $type_names)) . '</span>';
                } else {
                    echo '—';
                }
                break;

            case 'reactions':
                $reactions = get_post_meta($post_id, 'reactions', true);
                if (is_array($reactions)) {
                    $total = array_sum($reactions);
                    echo '<strong>' . $total . '</strong> 🔥';
                } else {
                    echo '0';
                }
                break;

            case 'rating':
                $rating = get_post_meta($post_id, 'rating_avg', true);
                if ($rating > 0) {
                    echo '⭐ ' . number_format($rating, 1);
                } else {
                    echo '—';
                }
                break;

            case 'views':
                $views = get_post_meta($post_id, 'views_count', true);
                echo '<strong>' . number_format((int) $views) . '</strong>';
                break;
        }
    }

    /**
     * Sortable columns
     */
    public static function prompt_sortable_columns($columns) {
        $columns['views'] = 'views';
        $columns['rating'] = 'rating';
        return $columns;
    }

    /**
     * Custom columns for platforms
     */
    public static function platform_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['name'] = $columns['name'];
        $new_columns['prompts_count'] = __('Prompts', 'epic-prompts');
        $new_columns['slug'] = $columns['slug'];
        $new_columns['description'] = $columns['description'];
        return $new_columns;
    }

    /**
     * Platform column content
     */
    public static function platform_column_content($content, $column_name, $term_id) {
        if ($column_name === 'prompts_count') {
            $term = get_term($term_id, 'ai_platform');
            return '<strong>' . $term->count . '</strong> prompts';
        }
        return $content;
    }

    /**
     * Custom columns for prompt types
     */
    public static function prompt_type_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['name'] = $columns['name'];
        $new_columns['prompts_count'] = __('Prompts', 'epic-prompts');
        $new_columns['slug'] = $columns['slug'];
        return $new_columns;
    }

    /**
     * Prompt type column content
     */
    public static function prompt_type_column_content($content, $column_name, $term_id) {
        if ($column_name === 'prompts_count') {
            $term = get_term($term_id, 'prompt_type');
            return '<strong>' . $term->count . '</strong> prompts';
        }
        return $content;
    }

    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'epic_prompts_details',
            __('Prompt Details', 'epic-prompts'),
            array(__CLASS__, 'prompt_details_meta_box'),
            'ai_prompt',
            'normal',
            'high'
        );

        add_meta_box(
            'epic_prompts_stats',
            __('Prompt Statistics', 'epic-prompts'),
            array(__CLASS__, 'prompt_stats_meta_box'),
            'ai_prompt',
            'side',
            'default'
        );
    }

    /**
     * Prompt details meta box
     */
    public static function prompt_details_meta_box($post) {
        wp_nonce_field('epic_prompts_meta_box', 'epic_prompts_meta_box_nonce');

        $prompt_text = get_post_meta($post->ID, 'prompt_text', true);
        $result_image = get_post_meta($post->ID, 'result_image', true);

        ?>
        <div style="margin-bottom: 15px;">
            <label for="prompt_text" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php _e('Prompt Text', 'epic-prompts'); ?> <span style="color: red;">*</span>
            </label>
            <textarea
                id="prompt_text"
                name="prompt_text"
                rows="8"
                style="width: 100%; font-family: monospace;"
                required
            ><?php echo esc_textarea($prompt_text); ?></textarea>
            <p class="description"><?php _e('The actual prompt text used with the AI tool.', 'epic-prompts'); ?></p>
        </div>

        <div>
            <label for="result_image" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php _e('Result Image ID (optional)', 'epic-prompts'); ?>
            </label>
            <input
                type="number"
                id="result_image"
                name="result_image"
                value="<?php echo esc_attr($result_image); ?>"
                style="width: 100px;"
            >
            <p class="description"><?php _e('WordPress media library attachment ID for the result image.', 'epic-prompts'); ?></p>

            <?php if ($result_image) : ?>
                <div style="margin-top: 10px;">
                    <?php echo wp_get_attachment_image($result_image, 'medium'); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Prompt stats meta box
     */
    public static function prompt_stats_meta_box($post) {
        $views = get_post_meta($post->ID, 'views_count', true);
        $saves = get_post_meta($post->ID, 'saves_count', true);
        $verified = get_post_meta($post->ID, 'verified_count', true);
        $rating = get_post_meta($post->ID, 'rating_avg', true);
        $reactions = get_post_meta($post->ID, 'reactions', true);

        ?>
        <div style="padding: 10px 0;">
            <p><strong><?php _e('Views:', 'epic-prompts'); ?></strong> <?php echo number_format((int) $views); ?></p>
            <p><strong><?php _e('Saves:', 'epic-prompts'); ?></strong> <?php echo number_format((int) $saves); ?></p>
            <p><strong><?php _e('Verified:', 'epic-prompts'); ?></strong> <?php echo number_format((int) $verified); ?></p>
            <p><strong><?php _e('Rating:', 'epic-prompts'); ?></strong> <?php echo $rating > 0 ? number_format($rating, 1) . '/5 ⭐' : '—'; ?></p>

            <?php if (is_array($reactions)) : ?>
                <hr style="margin: 15px 0;">
                <p><strong><?php _e('Reactions:', 'epic-prompts'); ?></strong></p>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>🔥 Fire: <?php echo (int) ($reactions['fire'] ?? 0); ?></li>
                    <li>💎 Gem: <?php echo (int) ($reactions['gem'] ?? 0); ?></li>
                    <li>🎨 Creative: <?php echo (int) ($reactions['creative'] ?? 0); ?></li>
                    <li>🚀 Rocket: <?php echo (int) ($reactions['rocket'] ?? 0); ?></li>
                    <li>💡 Mindblown: <?php echo (int) ($reactions['mindblown'] ?? 0); ?></li>
                </ul>
                <p><strong><?php _e('Total:', 'epic-prompts'); ?></strong> <?php echo array_sum($reactions); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save prompt meta
     */
    public static function save_prompt_meta($post_id, $post) {
        // Security checks
        if (!isset($_POST['epic_prompts_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['epic_prompts_meta_box_nonce'], 'epic_prompts_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save prompt text
        if (isset($_POST['prompt_text'])) {
            update_post_meta($post_id, 'prompt_text', sanitize_textarea_field($_POST['prompt_text']));
        }

        // Save result image
        if (isset($_POST['result_image'])) {
            $image_id = intval($_POST['result_image']);
            if ($image_id > 0) {
                update_post_meta($post_id, 'result_image', $image_id);
                set_post_thumbnail($post_id, $image_id);
            }
        }
    }

    /**
     * Settings page
     */
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Epic Prompts Settings', 'epic-prompts'); ?></h1>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2><?php _e('Platform Information', 'epic-prompts'); ?></h2>
                <p><?php _e('Epic Prompts is a gamified platform for sharing AI prompts.', 'epic-prompts'); ?></p>

                <h3><?php _e('Quick Stats', 'epic-prompts'); ?></h3>
                <?php
                $prompts_count = wp_count_posts('ai_prompt')->publish;
                $users_count = count_users()['total_users'];
                $platforms_count = wp_count_terms(array('taxonomy' => 'ai_platform', 'hide_empty' => false));
                $types_count = wp_count_terms(array('taxonomy' => 'prompt_type', 'hide_empty' => false));
                ?>
                <ul>
                    <li><strong><?php echo number_format($prompts_count); ?></strong> published prompts</li>
                    <li><strong><?php echo number_format($users_count); ?></strong> registered users</li>
                    <li><strong><?php echo number_format($platforms_count); ?></strong> AI platforms</li>
                    <li><strong><?php echo number_format($types_count); ?></strong> prompt types</li>
                </ul>

                <h3><?php _e('Manage', 'epic-prompts'); ?></h3>
                <p>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=ai_platform&post_type=ai_prompt'); ?>" class="button">
                        <?php _e('Manage AI Platforms', 'epic-prompts'); ?>
                    </a>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=prompt_type&post_type=ai_prompt'); ?>" class="button">
                        <?php _e('Manage Prompt Types', 'epic-prompts'); ?>
                    </a>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=prompt_category&post_type=ai_prompt'); ?>" class="button">
                        <?php _e('Manage Categories', 'epic-prompts'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Platforms management page
     */
    public static function platforms_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Platform Manager', 'epic-prompts'); ?></h1>
            <p><?php _e('Manage AI platforms directly from the WordPress taxonomy editor:', 'epic-prompts'); ?></p>

            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=ai_platform&post_type=ai_prompt'); ?>" class="button button-primary button-large">
                    <?php _e('Manage AI Platforms', 'epic-prompts'); ?> →
                </a>
            </p>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2><?php _e('Current Platforms', 'epic-prompts'); ?></h2>

                <?php
                $platforms = get_terms(array(
                    'taxonomy' => 'ai_platform',
                    'hide_empty' => false,
                    'orderby' => 'name',
                ));

                if ($platforms) {
                    $grouped = array();
                    foreach ($platforms as $platform) {
                        $name = $platform->name;
                        if (strpos($name, 'ChatGPT') !== false || strpos($name, 'Claude') !== false ||
                            strpos($name, 'Gemini') !== false || strpos($name, 'Copilot') !== false) {
                            $grouped['Text/Chat AI'][] = $platform;
                        } elseif (strpos($name, 'Midjourney') !== false || strpos($name, 'DALL-E') !== false ||
                                  strpos($name, 'Stable Diffusion') !== false) {
                            $grouped['Image Generation'][] = $platform;
                        } elseif (strpos($name, 'Runway') !== false || strpos($name, 'Sora') !== false) {
                            $grouped['Video Generation'][] = $platform;
                        } elseif (strpos($name, 'Copilot') !== false || strpos($name, 'Cursor') !== false) {
                            $grouped['Code Generation'][] = $platform;
                        } else {
                            $grouped['Other'][] = $platform;
                        }
                    }

                    foreach ($grouped as $group => $group_platforms) {
                        echo '<h3>' . esc_html($group) . '</h3>';
                        echo '<ul>';
                        foreach ($group_platforms as $platform) {
                            echo '<li><strong>' . esc_html($platform->name) . '</strong> (' . $platform->count . ' prompts)</li>';
                        }
                        echo '</ul>';
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Statistics page
     */
    public static function stats_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Epic Prompts Statistics', 'epic-prompts'); ?></h1>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <?php
                $stats = array(
                    array('title' => 'Total Prompts', 'value' => wp_count_posts('ai_prompt')->publish, 'icon' => '💡'),
                    array('title' => 'Total Users', 'value' => count_users()['total_users'], 'icon' => '👥'),
                    array('title' => 'AI Platforms', 'value' => wp_count_terms(array('taxonomy' => 'ai_platform')), 'icon' => '🤖'),
                    array('title' => 'Prompt Types', 'value' => wp_count_terms(array('taxonomy' => 'prompt_type')), 'icon' => '📝'),
                );

                foreach ($stats as $stat) {
                    ?>
                    <div class="card" style="padding: 20px; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;"><?php echo $stat['icon']; ?></div>
                        <h2 style="margin: 0; font-size: 2.5rem;"><?php echo number_format($stat['value']); ?></h2>
                        <p style="margin: 5px 0 0; color: #666;"><?php echo $stat['title']; ?></p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2><?php _e('Top Platforms by Prompts', 'epic-prompts'); ?></h2>
                <?php
                $top_platforms = get_terms(array(
                    'taxonomy' => 'ai_platform',
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 10,
                    'hide_empty' => true,
                ));

                if ($top_platforms) {
                    echo '<ol>';
                    foreach ($top_platforms as $platform) {
                        echo '<li><strong>' . esc_html($platform->name) . '</strong> - ' . $platform->count . ' prompts</li>';
                    }
                    echo '</ol>';
                } else {
                    echo '<p>' . __('No platforms with prompts yet.', 'epic-prompts') . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Admin notices
     */
    public static function admin_notices() {
        $screen = get_current_screen();

        if ($screen && $screen->post_type === 'ai_prompt' && $screen->base === 'edit') {
            $prompts_count = wp_count_posts('ai_prompt')->publish;

            if ($prompts_count < 10) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong><?php _e('Getting started with Epic Prompts!', 'epic-prompts'); ?></strong>
                        <?php printf(__('You have %d prompts. Add more to build your community!', 'epic-prompts'), $prompts_count); ?>
                    </p>
                </div>
                <?php
            }
        }
    }

    /**
     * Enqueue admin scripts
     */
    public static function enqueue_admin_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }

        $screen = get_current_screen();
        if ('ai_prompt' !== $screen->post_type) {
            return;
        }

        // Add custom admin styles
        wp_add_inline_style('wp-admin', '
            .epic-prompts-meta-box .form-field {
                margin-bottom: 20px;
            }
            .epic-prompts-stats {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
            }
            .badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
            }
        ');
    }
}

// Initialize admin
add_action('admin_init', array('EP_Admin', 'init'));
