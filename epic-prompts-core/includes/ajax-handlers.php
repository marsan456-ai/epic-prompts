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
}

// Register additional AJAX handlers
add_action('wp_ajax_increment_views', array('EP_Ajax_Handlers', 'increment_views'));
add_action('wp_ajax_nopriv_increment_views', array('EP_Ajax_Handlers', 'increment_views'));
add_action('wp_ajax_upload_prompt_image', array('EP_Ajax_Handlers', 'upload_prompt_image'));
