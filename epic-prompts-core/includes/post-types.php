<?php
/**
 * Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_Post_Types {

    /**
     * Register all custom post types
     */
    public static function register() {
        self::register_ai_prompt();
        self::register_prompt_vote();
        self::register_prompt_collection();
    }

    /**
     * Register AI Prompt post type
     */
    private static function register_ai_prompt() {
        $labels = array(
            'name'                  => __('Prompts', 'epic-prompts'),
            'singular_name'         => __('Prompt', 'epic-prompts'),
            'menu_name'             => __('Prompts', 'epic-prompts'),
            'name_admin_bar'        => __('Prompt', 'epic-prompts'),
            'add_new'               => __('Add New', 'epic-prompts'),
            'add_new_item'          => __('Add New Prompt', 'epic-prompts'),
            'new_item'              => __('New Prompt', 'epic-prompts'),
            'edit_item'             => __('Edit Prompt', 'epic-prompts'),
            'view_item'             => __('View Prompt', 'epic-prompts'),
            'all_items'             => __('All Prompts', 'epic-prompts'),
            'search_items'          => __('Search Prompts', 'epic-prompts'),
            'parent_item_colon'     => __('Parent Prompts:', 'epic-prompts'),
            'not_found'             => __('No prompts found.', 'epic-prompts'),
            'not_found_in_trash'    => __('No prompts found in Trash.', 'epic-prompts'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'prompts'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-lightbulb',
            'supports'           => array('title', 'editor', 'thumbnail', 'author', 'comments', 'excerpt'),
            'show_in_rest'       => true,
        );

        register_post_type('ai_prompt', $args);

        // Register meta fields
        self::register_prompt_meta();
    }

    /**
     * Register prompt meta fields
     */
    private static function register_prompt_meta() {
        // Prompt text
        register_post_meta('ai_prompt', 'prompt_text', array(
            'type'              => 'string',
            'description'       => 'The actual prompt text',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        // Result image
        register_post_meta('ai_prompt', 'result_image', array(
            'type'         => 'integer',
            'description'  => 'Example result image attachment ID',
            'single'       => true,
            'show_in_rest' => true,
        ));

        // Views count
        register_post_meta('ai_prompt', 'views_count', array(
            'type'         => 'integer',
            'description'  => 'Number of views',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ));

        // Saves count
        register_post_meta('ai_prompt', 'saves_count', array(
            'type'         => 'integer',
            'description'  => 'Number of times saved',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ));

        // Verified count
        register_post_meta('ai_prompt', 'verified_count', array(
            'type'         => 'integer',
            'description'  => 'Number of verifications',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ));

        // Rating average
        register_post_meta('ai_prompt', 'rating_avg', array(
            'type'         => 'number',
            'description'  => 'Average rating',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ));

        // Reactions array
        register_post_meta('ai_prompt', 'reactions', array(
            'type'         => 'object',
            'description'  => 'Reactions count object',
            'single'       => true,
            'show_in_rest' => array(
                'schema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'fire'      => array('type' => 'integer'),
                        'gem'       => array('type' => 'integer'),
                        'creative'  => array('type' => 'integer'),
                        'rocket'    => array('type' => 'integer'),
                        'mindblown' => array('type' => 'integer'),
                    ),
                ),
            ),
            'default' => array(
                'fire'      => 0,
                'gem'       => 0,
                'creative'  => 0,
                'rocket'    => 0,
                'mindblown' => 0,
            ),
        ));
    }

    /**
     * Register Prompt Vote post type
     */
    private static function register_prompt_vote() {
        $args = array(
            'labels' => array(
                'name'          => __('Votes', 'epic-prompts'),
                'singular_name' => __('Vote', 'epic-prompts'),
            ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'edit.php?post_type=ai_prompt',
            'supports'     => array('title', 'editor'),
            'show_in_rest' => true,
        );

        register_post_type('prompt_vote', $args);

        // Register vote meta fields
        register_post_meta('prompt_vote', 'user_id', array(
            'type'   => 'integer',
            'single' => true,
        ));

        register_post_meta('prompt_vote', 'prompt_id', array(
            'type'   => 'integer',
            'single' => true,
        ));

        register_post_meta('prompt_vote', 'rating_overall', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'rating_detail', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'rating_creativity', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'rating_usability', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'rating_consistency', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'rating_originality', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_vote', 'is_verified', array(
            'type'    => 'boolean',
            'single'  => true,
            'default' => false,
        ));

        register_post_meta('prompt_vote', 'verified_image', array(
            'type'   => 'string',
            'single' => true,
        ));
    }

    /**
     * Register Prompt Collection post type
     */
    private static function register_prompt_collection() {
        $args = array(
            'labels' => array(
                'name'          => __('Collections', 'epic-prompts'),
                'singular_name' => __('Collection', 'epic-prompts'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'collections'),
            'supports'     => array('title', 'editor', 'author', 'thumbnail'),
            'menu_icon'    => 'dashicons-portfolio',
            'show_in_rest' => true,
        );

        register_post_type('prompt_collection', $args);

        // Register collection meta
        register_post_meta('prompt_collection', 'prompts', array(
            'type'         => 'array',
            'single'       => true,
            'show_in_rest' => array(
                'schema' => array(
                    'type'  => 'array',
                    'items' => array('type' => 'integer'),
                ),
            ),
            'default' => array(),
        ));

        register_post_meta('prompt_collection', 'is_public', array(
            'type'    => 'boolean',
            'single'  => true,
            'default' => true,
        ));

        register_post_meta('prompt_collection', 'votes_count', array(
            'type'    => 'integer',
            'single'  => true,
            'default' => 0,
        ));

        register_post_meta('prompt_collection', 'featured', array(
            'type'    => 'boolean',
            'single'  => true,
            'default' => false,
        ));
    }
}
