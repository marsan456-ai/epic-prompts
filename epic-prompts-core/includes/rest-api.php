<?php
/**
 * REST API Endpoints
 * Provides public API for accessing prompts, platforms, and stats
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_REST_API {

    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Prompts endpoints
        register_rest_route('epic-prompts/v1', '/prompts', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_prompts'),
            'permission_callback' => '__return_true',
            'args' => array(
                'per_page' => array(
                    'default' => 10,
                    'sanitize_callback' => 'absint',
                ),
                'page' => array(
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ),
                'platform' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'type' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'category' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'orderby' => array(
                    'default' => 'date',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'search' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

        register_rest_route('epic-prompts/v1', '/prompts/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_prompt'),
            'permission_callback' => '__return_true',
        ));

        // Taxonomies endpoints
        register_rest_route('epic-prompts/v1', '/platforms', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_platforms'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('epic-prompts/v1', '/types', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_types'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('epic-prompts/v1', '/categories', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_categories'),
            'permission_callback' => '__return_true',
        ));

        // Stats endpoints
        register_rest_route('epic-prompts/v1', '/stats', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_stats'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('epic-prompts/v1', '/leaderboard', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_leaderboard'),
            'permission_callback' => '__return_true',
            'args' => array(
                'limit' => array(
                    'default' => 10,
                    'sanitize_callback' => 'absint',
                ),
                'type' => array(
                    'default' => 'total',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
    }

    /**
     * Get prompts list
     */
    public static function get_prompts($request) {
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $platform = $request->get_param('platform');
        $type = $request->get_param('type');
        $category = $request->get_param('category');
        $orderby = $request->get_param('orderby');
        $search = $request->get_param('search');

        $query_args = array(
            'post_type' => 'ai_prompt',
            'posts_per_page' => min($per_page, 100), // Max 100
            'paged' => $page,
            'post_status' => 'publish',
        );

        // Search
        if (!empty($search)) {
            $query_args['s'] = $search;
        }

        // Tax query
        $tax_query = array();

        if (!empty($platform)) {
            $tax_query[] = array(
                'taxonomy' => 'ai_platform',
                'field' => 'slug',
                'terms' => $platform,
            );
        }

        if (!empty($type)) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_type',
                'field' => 'slug',
                'terms' => $type,
            );
        }

        if (!empty($category)) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }

        if (!empty($tax_query)) {
            $query_args['tax_query'] = $tax_query;
        }

        // Sorting
        switch ($orderby) {
            case 'rating':
                $query_args['meta_key'] = 'rating_avg';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'DESC';
                break;
            case 'views':
                $query_args['meta_key'] = 'views_count';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'DESC';
                break;
            case 'verified':
                $query_args['meta_key'] = 'verified_count';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'DESC';
                break;
            default:
                $query_args['orderby'] = 'date';
                $query_args['order'] = 'DESC';
        }

        $query = new WP_Query($query_args);

        $prompts = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $prompts[] = self::format_prompt_data(get_the_ID());
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $prompts,
            'pagination' => array(
                'total' => $query->found_posts,
                'total_pages' => $query->max_num_pages,
                'current_page' => $page,
                'per_page' => $per_page,
            ),
        ), 200);
    }

    /**
     * Get single prompt
     */
    public static function get_prompt($request) {
        $prompt_id = $request->get_param('id');
        $prompt = get_post($prompt_id);

        if (!$prompt || $prompt->post_type !== 'ai_prompt' || $prompt->post_status !== 'publish') {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Prompt not found',
            ), 404);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => self::format_prompt_data($prompt_id, true),
        ), 200);
    }

    /**
     * Format prompt data
     */
    private static function format_prompt_data($prompt_id, $detailed = false) {
        $prompt_text = get_post_meta($prompt_id, 'prompt_text', true);
        $reactions = get_post_meta($prompt_id, 'reactions', true);
        $rating_avg = get_post_meta($prompt_id, 'rating_avg', true);
        $views_count = get_post_meta($prompt_id, 'views_count', true);
        $verified_count = get_post_meta($prompt_id, 'verified_count', true);

        // Get taxonomies
        $platforms = wp_get_post_terms($prompt_id, 'ai_platform');
        $types = wp_get_post_terms($prompt_id, 'prompt_type');
        $categories = wp_get_post_terms($prompt_id, 'prompt_category');
        $tags = wp_get_post_terms($prompt_id, 'prompt_tag');

        $data = array(
            'id' => $prompt_id,
            'title' => get_the_title($prompt_id),
            'slug' => get_post_field('post_name', $prompt_id),
            'url' => get_permalink($prompt_id),
            'author' => array(
                'id' => get_post_field('post_author', $prompt_id),
                'name' => get_the_author_meta('display_name', get_post_field('post_author', $prompt_id)),
            ),
            'date' => get_the_date('c', $prompt_id),
            'platform' => !empty($platforms) ? $platforms[0]->name : null,
            'type' => !empty($types) ? $types[0]->name : null,
            'category' => !empty($categories) ? $categories[0]->name : null,
            'tags' => array_map(function($tag) {
                return $tag->name;
            }, $tags),
            'stats' => array(
                'rating' => floatval($rating_avg),
                'views' => intval($views_count),
                'verified' => intval($verified_count),
                'reactions' => $reactions,
            ),
        );

        // Add featured image if exists
        if (has_post_thumbnail($prompt_id)) {
            $data['image'] = array(
                'url' => get_the_post_thumbnail_url($prompt_id, 'large'),
                'thumbnail' => get_the_post_thumbnail_url($prompt_id, 'thumbnail'),
            );
        }

        // Add detailed info if requested
        if ($detailed) {
            $data['prompt_text'] = $prompt_text;
            $data['description'] = get_post_field('post_content', $prompt_id);
        }

        return $data;
    }

    /**
     * Get platforms
     */
    public static function get_platforms($request) {
        $platforms = get_terms(array(
            'taxonomy' => 'ai_platform',
            'hide_empty' => false,
        ));

        $data = array();
        foreach ($platforms as $platform) {
            $data[] = array(
                'id' => $platform->term_id,
                'name' => $platform->name,
                'slug' => $platform->slug,
                'count' => $platform->count,
                'url' => get_term_link($platform),
            );
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $data,
        ), 200);
    }

    /**
     * Get prompt types
     */
    public static function get_types($request) {
        $types = get_terms(array(
            'taxonomy' => 'prompt_type',
            'hide_empty' => false,
        ));

        $data = array();
        foreach ($types as $type) {
            $data[] = array(
                'id' => $type->term_id,
                'name' => $type->name,
                'slug' => $type->slug,
                'count' => $type->count,
                'url' => get_term_link($type),
            );
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $data,
        ), 200);
    }

    /**
     * Get categories
     */
    public static function get_categories($request) {
        $categories = get_terms(array(
            'taxonomy' => 'prompt_category',
            'hide_empty' => false,
        ));

        $data = array();
        foreach ($categories as $category) {
            $data[] = array(
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => $category->count,
                'url' => get_term_link($category),
            );
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $data,
        ), 200);
    }

    /**
     * Get platform stats
     */
    public static function get_stats($request) {
        $prompts_count = wp_count_posts('ai_prompt');
        $votes_count = wp_count_posts('prompt_vote');
        $users_count = count_users();

        // Get platforms count
        $platforms = get_terms(array(
            'taxonomy' => 'ai_platform',
            'hide_empty' => false,
        ));

        return new WP_REST_Response(array(
            'success' => true,
            'data' => array(
                'prompts' => array(
                    'total' => intval($prompts_count->publish),
                    'pending' => intval($prompts_count->pending),
                ),
                'users' => array(
                    'total' => intval($users_count['total_users']),
                ),
                'votes' => intval($votes_count->publish),
                'platforms' => count($platforms),
            ),
        ), 200);
    }

    /**
     * Get leaderboard
     */
    public static function get_leaderboard($request) {
        $limit = $request->get_param('limit');
        $type = $request->get_param('type');

        $leaderboard = EP_XP_System::get_leaderboard($limit, $type);

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $leaderboard,
            'type' => $type,
        ), 200);
    }
}

// Register routes
add_action('rest_api_init', array('EP_REST_API', 'register_routes'));
