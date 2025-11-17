<?php
/**
 * AJAX Handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_Ajax_Handlers {

    /**
     * Handle add reaction AJAX request
     */
    public static function add_reaction() {
        // Security check
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        // Get data
        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;
        $reaction_type = isset($_POST['reaction_type']) ? sanitize_text_field($_POST['reaction_type']) : '';
        $user_id = get_current_user_id();

        // Validation
        if (!$user_id) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'epic-prompts')));
        }

        if (!$prompt_id || !get_post($prompt_id)) {
            wp_send_json_error(array('message' => __('Invalid prompt.', 'epic-prompts')));
        }

        // Valid reaction types
        $valid_reactions = array('fire', 'gem', 'creative', 'rocket', 'mindblown');
        if (!in_array($reaction_type, $valid_reactions)) {
            wp_send_json_error(array('message' => __('Invalid reaction type.', 'epic-prompts')));
        }

        // Get current reactions
        $reactions = get_post_meta($prompt_id, 'reactions', true);
        if (!is_array($reactions) || empty($reactions)) {
            $reactions = array(
                'fire' => 0,
                'gem' => 0,
                'creative' => 0,
                'rocket' => 0,
                'mindblown' => 0,
            );
        }

        // Check if user already reacted (using transient for MVP)
        $user_reaction_key = 'user_reaction_' . $user_id . '_' . $prompt_id;
        $previous_reaction = get_transient($user_reaction_key);

        $xp_awarded = 0;

        if ($previous_reaction) {
            // Remove previous reaction
            if (isset($reactions[$previous_reaction]) && $reactions[$previous_reaction] > 0) {
                $reactions[$previous_reaction]--;
            }
        } else {
            // First time reacting - award XP
            EP_XP_System::award_vote_xp($user_id);
            EP_User_Functions::increment_stat($user_id, 'votes_cast');

            // Award XP to prompt creator
            $prompt_author = get_post_field('post_author', $prompt_id);
            EP_XP_System::award_received_reaction_xp($prompt_author);

            $xp_awarded = EP_XP_System::XP_VOTE_REACT;
        }

        // Add new reaction
        $reactions[$reaction_type]++;
        update_post_meta($prompt_id, 'reactions', $reactions);

        // Store user's current reaction (expires in 1 year)
        set_transient($user_reaction_key, $reaction_type, YEAR_IN_SECONDS);

        // Return success
        wp_send_json_success(array(
            'reactions' => $reactions,
            'xp_awarded' => $xp_awarded,
            'user_reaction' => $reaction_type,
            'message' => __('Reaction added!', 'epic-prompts'),
        ));
    }

    /**
     * Handle submit prompt AJAX request
     */
    public static function submit_prompt() {
        // Security check
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'epic-prompts')));
        }

        // Get and sanitize data
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $prompt_text = isset($_POST['prompt_text']) ? sanitize_textarea_field($_POST['prompt_text']) : '';
        $description = isset($_POST['description']) ? wp_kses_post($_POST['description']) : '';
        $platform = isset($_POST['platform']) ? intval($_POST['platform']) : 0;
        $prompt_type = isset($_POST['prompt_type']) ? intval($_POST['prompt_type']) : 0;
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $tags = isset($_POST['tags']) ? sanitize_text_field($_POST['tags']) : '';
        $result_image_id = isset($_POST['result_image_id']) && !empty($_POST['result_image_id']) ? intval($_POST['result_image_id']) : 0;

        // Validation
        if (empty($title) || empty($prompt_text)) {
            wp_send_json_error(array('message' => __('Title and prompt text are required.', 'epic-prompts')));
        }

        // Determine post status based on user level
        $user_level = EP_User_Functions::get_user_level($user_id);
        $post_status = ($user_level >= 6) ? 'publish' : 'pending';

        // Create prompt post
        $post_data = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_type' => 'ai_prompt',
            'post_status' => $post_status,
            'post_author' => $user_id,
        );

        $prompt_id = wp_insert_post($post_data);

        if (is_wp_error($prompt_id)) {
            wp_send_json_error(array('message' => __('Failed to create prompt.', 'epic-prompts')));
        }

        // Set post meta
        update_post_meta($prompt_id, 'prompt_text', $prompt_text);

        // Set image only if provided
        if ($result_image_id) {
            update_post_meta($prompt_id, 'result_image', $result_image_id);
            set_post_thumbnail($prompt_id, $result_image_id);
        }

        // Initialize counts
        update_post_meta($prompt_id, 'views_count', 0);
        update_post_meta($prompt_id, 'saves_count', 0);
        update_post_meta($prompt_id, 'verified_count', 0);
        update_post_meta($prompt_id, 'rating_avg', 0);
        update_post_meta($prompt_id, 'reactions', array(
            'fire' => 0,
            'gem' => 0,
            'creative' => 0,
            'rocket' => 0,
            'mindblown' => 0,
        ));

        // Set taxonomies
        if ($platform) {
            wp_set_object_terms($prompt_id, $platform, 'ai_platform');
        }

        if ($prompt_type) {
            wp_set_object_terms($prompt_id, $prompt_type, 'prompt_type');
        }

        if ($category) {
            wp_set_object_terms($prompt_id, $category, 'prompt_category');
        }

        if (!empty($tags)) {
            $tags_array = array_map('trim', explode(',', $tags));
            wp_set_object_terms($prompt_id, $tags_array, 'prompt_tag');
        }

        // Award XP
        EP_XP_System::award_submit_prompt_xp($user_id);
        EP_User_Functions::increment_stat($user_id, 'prompts_submitted');

        // Check for first prompt badge
        $prompts_count = (int) get_user_meta($user_id, 'prompts_submitted', true);
        if ($prompts_count === 1) {
            EP_User_Functions::award_badge($user_id, 'first_prompt', 'First Steps');
        }

        wp_send_json_success(array(
            'message' => __('Prompt submitted successfully!', 'epic-prompts'),
            'prompt_id' => $prompt_id,
            'status' => $post_status,
            'redirect' => get_permalink($prompt_id),
        ));
    }

    /**
     * Handle vote prompt AJAX request
     */
    public static function vote_prompt() {
        // Security check
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'epic-prompts')));
        }

        // Get data
        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;
        $rating_overall = isset($_POST['rating_overall']) ? intval($_POST['rating_overall']) : 0;
        $rating_detail = isset($_POST['rating_detail']) ? intval($_POST['rating_detail']) : 0;
        $rating_creativity = isset($_POST['rating_creativity']) ? intval($_POST['rating_creativity']) : 0;
        $rating_usability = isset($_POST['rating_usability']) ? intval($_POST['rating_usability']) : 0;
        $rating_consistency = isset($_POST['rating_consistency']) ? intval($_POST['rating_consistency']) : 0;
        $rating_originality = isset($_POST['rating_originality']) ? intval($_POST['rating_originality']) : 0;
        $review_text = isset($_POST['review_text']) ? wp_kses_post($_POST['review_text']) : '';

        // Validation
        if (!$prompt_id || !get_post($prompt_id)) {
            wp_send_json_error(array('message' => __('Invalid prompt.', 'epic-prompts')));
        }

        if ($rating_overall < 1 || $rating_overall > 5) {
            wp_send_json_error(array('message' => __('Invalid rating.', 'epic-prompts')));
        }

        // Check if user already voted
        $existing_vote = get_posts(array(
            'post_type' => 'prompt_vote',
            'meta_query' => array(
                array(
                    'key' => 'user_id',
                    'value' => $user_id,
                ),
                array(
                    'key' => 'prompt_id',
                    'value' => $prompt_id,
                ),
            ),
            'posts_per_page' => 1,
        ));

        if (!empty($existing_vote)) {
            $vote_id = $existing_vote[0]->ID;
            // Update existing vote
            wp_update_post(array(
                'ID' => $vote_id,
                'post_content' => $review_text,
            ));
        } else {
            // Create new vote
            $vote_id = wp_insert_post(array(
                'post_type' => 'prompt_vote',
                'post_title' => 'Vote by ' . $user_id . ' for prompt ' . $prompt_id,
                'post_content' => $review_text,
                'post_status' => 'publish',
            ));

            // Award XP for new vote
            if (!empty($review_text)) {
                EP_XP_System::award_review_xp($user_id);
                EP_User_Functions::increment_stat($user_id, 'reviews_written');
            } else {
                EP_XP_System::award_vote_xp($user_id);
            }

            EP_User_Functions::increment_stat($user_id, 'votes_cast');
        }

        // Update vote meta
        update_post_meta($vote_id, 'user_id', $user_id);
        update_post_meta($vote_id, 'prompt_id', $prompt_id);
        update_post_meta($vote_id, 'rating_overall', $rating_overall);
        update_post_meta($vote_id, 'rating_detail', $rating_detail);
        update_post_meta($vote_id, 'rating_creativity', $rating_creativity);
        update_post_meta($vote_id, 'rating_usability', $rating_usability);
        update_post_meta($vote_id, 'rating_consistency', $rating_consistency);
        update_post_meta($vote_id, 'rating_originality', $rating_originality);

        // Recalculate average rating for prompt
        self::recalculate_prompt_rating($prompt_id);

        wp_send_json_success(array(
            'message' => __('Vote recorded successfully!', 'epic-prompts'),
            'vote_id' => $vote_id,
        ));
    }

    /**
     * Recalculate prompt average rating
     */
    private static function recalculate_prompt_rating($prompt_id) {
        global $wpdb;

        $votes = get_posts(array(
            'post_type' => 'prompt_vote',
            'meta_key' => 'prompt_id',
            'meta_value' => $prompt_id,
            'posts_per_page' => -1,
        ));

        if (empty($votes)) {
            return;
        }

        $total = 0;
        $count = 0;

        foreach ($votes as $vote) {
            $rating = (int) get_post_meta($vote->ID, 'rating_overall', true);
            if ($rating > 0) {
                $total += $rating;
                $count++;
            }
        }

        $average = $count > 0 ? ($total / $count) : 0;
        update_post_meta($prompt_id, 'rating_avg', round($average, 2));
    }

    /**
     * Handle increment views
     */
    public static function increment_views() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;

        if (!$prompt_id) {
            wp_send_json_error();
        }

        $views = (int) get_post_meta($prompt_id, 'views_count', true);
        update_post_meta($prompt_id, 'views_count', $views + 1);

        wp_send_json_success(array('views' => $views + 1));
    }

    /**
     * Handle image upload
     */
    public static function upload_prompt_image() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'epic-prompts')));
        }

        if (!isset($_FILES['image']) || empty($_FILES['image']['name'])) {
            wp_send_json_error(array('message' => __('No image uploaded.', 'epic-prompts')));
        }

        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
            );
            $error_msg = isset($error_messages[$_FILES['image']['error']])
                ? $error_messages[$_FILES['image']['error']]
                : 'Unknown upload error';
            wp_send_json_error(array('message' => $error_msg));
        }

        // Validate file
        $file = $_FILES['image'];
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif');

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            wp_send_json_error(array('message' => __('Invalid file type. Only JPG, PNG, WebP and GIF are allowed.', 'epic-prompts')));
        }

        // 10MB max (aumentato per screenshots)
        if ($file['size'] > 10 * 1024 * 1024) {
            wp_send_json_error(array('message' => __('File size must be less than 10MB.', 'epic-prompts')));
        }

        // Upload file
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Override default upload handling
        add_filter('upload_dir', function($uploads) use ($user_id) {
            $uploads['subdir'] = '/epic-prompts/' . date('Y/m');
            $uploads['path'] = $uploads['basedir'] . $uploads['subdir'];
            $uploads['url'] = $uploads['baseurl'] . $uploads['subdir'];
            return $uploads;
        });

        $attachment_id = media_handle_upload('image', 0, array(
            'post_title' => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
            'post_author' => $user_id,
        ));

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => $attachment_id->get_error_message()));
        }

        wp_send_json_success(array(
            'attachment_id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id),
            'thumb' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
        ));
    }

    /**
     * Handle get leaderboard AJAX request
     */
    public static function get_leaderboard() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'total';
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 50;

        // Check cache first
        $cache_key = 'epic_prompts_leaderboard_' . $type . '_' . $limit;
        $cached_data = get_transient($cache_key);

        if ($cached_data !== false) {
            wp_send_json_success(array(
                'leaderboard' => $cached_data,
                'cached' => true
            ));
        }

        // Get leaderboard data
        $leaderboard = EP_XP_System::get_leaderboard($limit, $type);

        // Format data for frontend
        $formatted_data = array();
        foreach ($leaderboard as $entry) {
            $formatted_data[] = array(
                'rank' => $entry['rank'],
                'user_id' => $entry['user_id'],
                'username' => $entry['username'],
                'avatar' => $entry['avatar'],
                'level' => $entry['level'],
                'title' => $entry['title'],
                'xp' => $entry['xp'],
                'xp_formatted' => epic_prompts_format_number($entry['xp']),
                'profile_url' => get_author_posts_url($entry['user_id'])
            );
        }

        // Cache for 5 minutes
        set_transient($cache_key, $formatted_data, 5 * MINUTE_IN_SECONDS);

        wp_send_json_success(array(
            'leaderboard' => $formatted_data,
            'cached' => false
        ));
    }

    /**
     * Handle bookmark/save prompt AJAX request
     */
    public static function bookmark_prompt() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'epic-prompts')));
        }

        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;

        if (!$prompt_id || !get_post($prompt_id)) {
            wp_send_json_error(array('message' => __('Invalid prompt.', 'epic-prompts')));
        }

        // Get user's saved prompts
        $saved_prompts = get_user_meta($user_id, 'saved_prompts', true);
        if (!is_array($saved_prompts)) {
            $saved_prompts = array();
        }

        $is_saved = in_array($prompt_id, $saved_prompts);

        if ($is_saved) {
            // Remove bookmark
            $saved_prompts = array_diff($saved_prompts, array($prompt_id));
            $message = __('Bookmark removed!', 'epic-prompts');
            $action = 'removed';

            // Decrement save count
            $saves = (int) get_post_meta($prompt_id, 'saves_count', true);
            update_post_meta($prompt_id, 'saves_count', max(0, $saves - 1));
        } else {
            // Add bookmark
            $saved_prompts[] = $prompt_id;
            $message = __('Prompt bookmarked!', 'epic-prompts');
            $action = 'added';

            // Increment save count
            $saves = (int) get_post_meta($prompt_id, 'saves_count', true);
            update_post_meta($prompt_id, 'saves_count', $saves + 1);

            // Award XP for first bookmark
            $bookmarks_count = count($saved_prompts);
            if ($bookmarks_count === 1) {
                EP_User_Functions::award_badge($user_id, 'first_bookmark', 'Collector');
            }
        }

        update_user_meta($user_id, 'saved_prompts', array_values($saved_prompts));

        wp_send_json_success(array(
            'message' => $message,
            'is_saved' => !$is_saved,
            'action' => $action,
            'saves_count' => (int) get_post_meta($prompt_id, 'saves_count', true)
        ));
    }

    /**
     * Handle copy prompt AJAX request (track usage)
     */
    public static function copy_prompt() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;

        if (!$prompt_id) {
            wp_send_json_error();
        }

        // Increment copy count
        $copies = (int) get_post_meta($prompt_id, 'copies_count', true);
        update_post_meta($prompt_id, 'copies_count', $copies + 1);

        // Track user copy if logged in
        $user_id = get_current_user_id();
        if ($user_id) {
            EP_User_Functions::increment_stat($user_id, 'prompts_copied');
        }

        wp_send_json_success(array(
            'copies' => $copies + 1,
            'message' => __('Prompt copied to clipboard!', 'epic-prompts')
        ));
    }

    /**
     * Handle share prompt tracking
     */
    public static function share_prompt() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $prompt_id = isset($_POST['prompt_id']) ? intval($_POST['prompt_id']) : 0;
        $platform = isset($_POST['platform']) ? sanitize_text_field($_POST['platform']) : '';

        if (!$prompt_id) {
            wp_send_json_error();
        }

        // Increment share count
        $shares = (int) get_post_meta($prompt_id, 'shares_count', true);
        update_post_meta($prompt_id, 'shares_count', $shares + 1);

        // Track platform-specific shares
        $platform_key = 'shares_' . $platform;
        $platform_shares = (int) get_post_meta($prompt_id, $platform_key, true);
        update_post_meta($prompt_id, $platform_key, $platform_shares + 1);

        // Track user shares if logged in
        $user_id = get_current_user_id();
        if ($user_id) {
            EP_User_Functions::increment_stat($user_id, 'prompts_shared');

            // Award badge for sharing
            $total_shared = (int) get_user_meta($user_id, 'prompts_shared', true);
            if ($total_shared === 1) {
                EP_User_Functions::award_badge($user_id, 'first_share', 'Influencer');
            }
        }

        wp_send_json_success(array(
            'shares' => $shares + 1,
            'message' => __('Thanks for sharing!', 'epic-prompts')
        ));
    }

    /**
     * Handle search prompts AJAX request
     */
    public static function search_prompts() {
        check_ajax_referer('epic_prompts_nonce', 'nonce');

        $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $platform = isset($_POST['platform']) ? intval($_POST['platform']) : 0;
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $prompt_type = isset($_POST['prompt_type']) ? intval($_POST['prompt_type']) : 0;
        $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'recent';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $args = array(
            'post_type' => 'ai_prompt',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'paged' => $page,
        );

        // Search
        if (!empty($search_term)) {
            $args['s'] = $search_term;
        }

        // Taxonomies
        $tax_query = array();

        if ($platform) {
            $tax_query[] = array(
                'taxonomy' => 'ai_platform',
                'field' => 'term_id',
                'terms' => $platform,
            );
        }

        if ($category) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_category',
                'field' => 'term_id',
                'terms' => $category,
            );
        }

        if ($prompt_type) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_type',
                'field' => 'term_id',
                'terms' => $prompt_type,
            );
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        // Sorting
        switch ($sort_by) {
            case 'popular':
                $args['meta_key'] = 'views_count';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'rated':
                $args['meta_key'] = 'rating_avg';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'saved':
                $args['meta_key'] = 'saves_count';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'recent':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }

        $query = new WP_Query($args);

        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $prompt_id = get_the_ID();
                $reactions = epic_prompts_get_reactions($prompt_id);
                $platforms = get_the_terms($prompt_id, 'ai_platform');

                $results[] = array(
                    'id' => $prompt_id,
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'excerpt' => get_the_excerpt(),
                    'author' => array(
                        'name' => get_the_author(),
                        'url' => get_author_posts_url(get_the_author_meta('ID'))
                    ),
                    'platform' => $platforms && !is_wp_error($platforms) ? $platforms[0]->name : '',
                    'reactions_total' => array_sum($reactions),
                    'views' => (int) get_post_meta($prompt_id, 'views_count', true),
                    'saves' => (int) get_post_meta($prompt_id, 'saves_count', true),
                    'rating' => (float) get_post_meta($prompt_id, 'rating_avg', true),
                    'date' => get_the_date('c')
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success(array(
            'results' => $results,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $page
        ));
    }
}

// Register additional AJAX handlers
add_action('wp_ajax_increment_views', array('EP_Ajax_Handlers', 'increment_views'));
add_action('wp_ajax_nopriv_increment_views', array('EP_Ajax_Handlers', 'increment_views'));
add_action('wp_ajax_upload_prompt_image', array('EP_Ajax_Handlers', 'upload_prompt_image'));
add_action('wp_ajax_get_leaderboard', array('EP_Ajax_Handlers', 'get_leaderboard'));
add_action('wp_ajax_nopriv_get_leaderboard', array('EP_Ajax_Handlers', 'get_leaderboard'));
add_action('wp_ajax_bookmark_prompt', array('EP_Ajax_Handlers', 'bookmark_prompt'));
add_action('wp_ajax_copy_prompt', array('EP_Ajax_Handlers', 'copy_prompt'));
add_action('wp_ajax_nopriv_copy_prompt', array('EP_Ajax_Handlers', 'copy_prompt'));
add_action('wp_ajax_share_prompt', array('EP_Ajax_Handlers', 'share_prompt'));
add_action('wp_ajax_nopriv_share_prompt', array('EP_Ajax_Handlers', 'share_prompt'));
add_action('wp_ajax_search_prompts', array('EP_Ajax_Handlers', 'search_prompts'));
add_action('wp_ajax_nopriv_search_prompts', array('EP_Ajax_Handlers', 'search_prompts'));
