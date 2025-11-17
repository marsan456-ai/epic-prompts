<?php
/**
 * Epic Prompts - SEO Functions
 *
 * Advanced SEO features including:
 * - Dynamic meta descriptions
 * - Open Graph tags
 * - Twitter Cards
 * - Schema.org structured data
 * - Canonical URLs
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get dynamic meta description based on page type
 */
function epic_prompts_get_meta_description() {
    $description = '';

    if (is_front_page()) {
        $description = 'Discover and share the best AI prompts for ChatGPT, Claude, Midjourney, and 50+ AI platforms. Join our gamified community with XP rewards, leaderboards, and 20+ categories.';
    }
    elseif (is_singular('ai_prompt')) {
        global $post;
        $excerpt = wp_strip_all_tags(get_the_excerpt());
        $description = mb_substr($excerpt, 0, 155) . '...';
        if (empty($description) || $description === '...') {
            $description = 'Discover this AI prompt for ' . epic_prompts_get_platform(get_the_ID()) . '. Learn how to use it effectively and get better results from your AI interactions.';
        }
    }
    elseif (is_post_type_archive('ai_prompt')) {
        $description = 'Browse our complete library of AI prompts. Filter by platform (ChatGPT, Claude, Midjourney) or category. Find the perfect prompt for your needs.';
    }
    elseif (is_tax('ai_platform')) {
        $term = get_queried_object();
        $description = 'Explore the best AI prompts specifically designed for ' . $term->name . '. Community-verified prompts with ratings and examples.';
    }
    elseif (is_tax('prompt_category')) {
        $term = get_queried_object();
        $description = 'Discover top-rated prompts in the ' . $term->name . ' category. Learn from examples created by our community of AI enthusiasts.';
    }
    elseif (is_author()) {
        $author = get_queried_object();
        $stats = epic_prompts_user_stats($author->ID);
        $description = 'View ' . $author->display_name . '\'s AI prompts and contributions. Level ' . $stats['level'] . ' with ' . $stats['xp_total'] . ' XP earned.';
    }
    elseif (is_page('leaderboard')) {
        $description = 'Check out the Epic Prompts leaderboard. See top contributors ranked by XP, prompts submitted, and community engagement. Compete for the top spot!';
    }
    elseif (is_page('how-it-works')) {
        $description = 'Learn how Epic Prompts works. Earn XP by submitting prompts, get verified by the community, level up from Novice to Legend, and climb the leaderboard.';
    }
    elseif (is_page('faq')) {
        $description = 'Frequently asked questions about Epic Prompts. Learn about the XP system, prompt submission, verification process, and community guidelines.';
    }
    elseif (is_page('about')) {
        $description = 'Epic Prompts is the world\'s largest gamified community for sharing AI prompts across 50+ platforms. Join thousands of creators democratizing AI knowledge.';
    }
    elseif (is_page('submit')) {
        $description = 'Submit your AI prompt to Epic Prompts. Earn 10 XP instantly, get verified for +5 XP bonus, and help the community discover better AI interactions.';
    }
    else {
        // Default fallback
        $description = get_bloginfo('description');
    }

    return esc_attr($description);
}

/**
 * Output meta tags in head
 */
function epic_prompts_output_meta_tags() {
    // Meta description
    echo '<meta name="description" content="' . epic_prompts_get_meta_description() . '">' . "\n";

    // Additional meta tags
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";

    // Author meta for single posts
    if (is_singular('ai_prompt')) {
        echo '<meta name="author" content="' . esc_attr(get_the_author()) . '">' . "\n";
    }
}
add_action('wp_head', 'epic_prompts_output_meta_tags', 1);

/**
 * Output Open Graph tags
 */
function epic_prompts_output_og_tags() {
    echo '<!-- Open Graph Meta Tags -->' . "\n";

    // OG Type
    $og_type = is_singular('ai_prompt') ? 'article' : 'website';
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";

    // OG Title
    if (is_singular()) {
        $og_title = get_the_title();
    } elseif (is_front_page()) {
        $og_title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
    } else {
        $og_title = wp_get_document_title();
    }
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";

    // OG Description
    echo '<meta property="og:description" content="' . epic_prompts_get_meta_description() . '">' . "\n";

    // OG URL
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";

    // OG Site Name
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

    // OG Image
    $og_image = '';
    if (is_singular() && has_post_thumbnail()) {
        $og_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
    } else {
        // Default OG image - could be site logo or custom image
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $og_image = wp_get_attachment_image_url($custom_logo_id, 'full');
        }
    }

    if ($og_image) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
    }

    // OG Locale
    echo '<meta property="og:locale" content="' . esc_attr(str_replace('-', '_', get_bloginfo('language'))) . '">' . "\n";

    // Article specific tags
    if (is_singular('ai_prompt')) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
        echo '<meta property="article:author" content="' . esc_attr(get_the_author()) . '">' . "\n";

        // Article tags
        $platforms = get_the_terms(get_the_ID(), 'ai_platform');
        if ($platforms && !is_wp_error($platforms)) {
            foreach ($platforms as $platform) {
                echo '<meta property="article:tag" content="' . esc_attr($platform->name) . '">' . "\n";
            }
        }
    }
}
add_action('wp_head', 'epic_prompts_output_og_tags', 2);

/**
 * Output Twitter Card tags
 */
function epic_prompts_output_twitter_tags() {
    echo '<!-- Twitter Card Meta Tags -->' . "\n";

    // Card type - summary with large image for posts, summary for others
    $card_type = is_singular('ai_prompt') && has_post_thumbnail() ? 'summary_large_image' : 'summary';
    echo '<meta name="twitter:card" content="' . esc_attr($card_type) . '">' . "\n";

    // Twitter site handle (if set in customizer)
    $twitter_handle = get_theme_mod('epic_prompts_social_twitter');
    if ($twitter_handle) {
        // Extract handle from URL if needed
        if (strpos($twitter_handle, 'twitter.com/') !== false || strpos($twitter_handle, 'x.com/') !== false) {
            preg_match('/(?:twitter|x)\.com\/([^\/\?]+)/', $twitter_handle, $matches);
            if (isset($matches[1])) {
                $twitter_handle = '@' . $matches[1];
            }
        }
        echo '<meta name="twitter:site" content="' . esc_attr($twitter_handle) . '">' . "\n";
    }

    // Twitter title
    if (is_singular()) {
        $twitter_title = get_the_title();
    } else {
        $twitter_title = wp_get_document_title();
    }
    echo '<meta name="twitter:title" content="' . esc_attr($twitter_title) . '">' . "\n";

    // Twitter description
    echo '<meta name="twitter:description" content="' . epic_prompts_get_meta_description() . '">' . "\n";

    // Twitter image
    if (is_singular() && has_post_thumbnail()) {
        $twitter_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
        echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '">' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr(get_the_title()) . '">' . "\n";
    }
}
add_action('wp_head', 'epic_prompts_output_twitter_tags', 3);

/**
 * Output Schema.org structured data
 */
function epic_prompts_output_schema() {
    $schema = array();

    // Always add WebSite schema on all pages
    $schema[] = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}')
            ),
            'query-input' => 'required name=search_term_string'
        )
    );

    // Organization schema
    $schema[] = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => 'Epic Prompts',
        'url' => home_url('/'),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : ''
        ),
        'sameAs' => epic_prompts_get_social_urls()
    );

    // Page-specific schemas
    if (is_front_page()) {
        // Add WebPage schema for homepage
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => home_url('/#webpage'),
            'url' => home_url('/'),
            'name' => get_bloginfo('name'),
            'description' => epic_prompts_get_meta_description(),
            'isPartOf' => array(
                '@id' => home_url('/#website')
            )
        );
    }
    elseif (is_singular('ai_prompt')) {
        // CreativeWork schema for prompts
        global $post;
        $author_id = $post->post_author;

        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            '@id' => get_permalink() . '#creativework',
            'headline' => get_the_title(),
            'description' => wp_strip_all_tags(get_the_excerpt()),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $author_id),
                'url' => get_author_posts_url($author_id)
            ),
            'publisher' => array(
                '@id' => home_url('/#organization')
            ),
            'inLanguage' => get_bloginfo('language'),
            'url' => get_permalink()
        );

        // Add BreadcrumbList
        $schema[] = epic_prompts_get_breadcrumb_schema();
    }
    elseif (is_page('faq')) {
        // FAQPage schema
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            '@id' => get_permalink() . '#faqpage',
            'name' => get_the_title(),
            'description' => epic_prompts_get_meta_description()
        );
    }
    elseif (is_page('how-it-works')) {
        // HowTo schema
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            '@id' => get_permalink() . '#howto',
            'name' => 'How to Use Epic Prompts',
            'description' => epic_prompts_get_meta_description(),
            'step' => array(
                array(
                    '@type' => 'HowToStep',
                    'position' => 1,
                    'name' => 'Sign Up',
                    'text' => 'Create your free account to join the community'
                ),
                array(
                    '@type' => 'HowToStep',
                    'position' => 2,
                    'name' => 'Submit Prompts',
                    'text' => 'Share your AI prompts and earn 10 XP instantly'
                ),
                array(
                    '@type' => 'HowToStep',
                    'position' => 3,
                    'name' => 'Earn Rewards',
                    'text' => 'Get verified, level up, and climb the leaderboard'
                )
            )
        );
    }
    elseif (is_author()) {
        // Person schema for author pages
        $author = get_queried_object();
        $stats = epic_prompts_user_stats($author->ID);

        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            '@id' => get_author_posts_url($author->ID) . '#profilepage',
            'mainEntity' => array(
                '@type' => 'Person',
                'name' => $author->display_name,
                'url' => get_author_posts_url($author->ID),
                'description' => get_the_author_meta('description', $author->ID),
                'image' => get_avatar_url($author->ID, array('size' => 200))
            )
        );
    }

    // Output schema as JSON-LD
    if (!empty($schema)) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        echo "\n" . '</script>' . "\n";
    }
}
add_action('wp_head', 'epic_prompts_output_schema', 4);

/**
 * Get breadcrumb schema
 */
function epic_prompts_get_breadcrumb_schema() {
    $breadcrumbs = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array()
    );

    // Home
    $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => home_url('/')
    );

    $position = 2;

    // Add intermediate breadcrumbs based on page type
    if (is_singular('ai_prompt')) {
        $categories = get_the_terms(get_the_ID(), 'prompt_category');
        if ($categories && !is_wp_error($categories)) {
            $category = $categories[0];
            $breadcrumbs['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category->name,
                'item' => get_term_link($category)
            );
        }

        // Current page
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }

    return $breadcrumbs;
}

/**
 * Get social URLs for schema
 */
function epic_prompts_get_social_urls() {
    $social_urls = array();

    $networks = array('facebook', 'twitter', 'instagram', 'discord', 'github');
    foreach ($networks as $network) {
        $url = get_theme_mod("epic_prompts_social_{$network}");
        if ($url) {
            $social_urls[] = esc_url($url);
        }
    }

    return $social_urls;
}

/**
 * Add canonical URL
 */
function epic_prompts_canonical_url() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        echo '<link rel="canonical" href="' . esc_url(get_term_link($term)) . '">' . "\n";
    } elseif (is_author()) {
        $author = get_queried_object();
        echo '<link rel="canonical" href="' . esc_url(get_author_posts_url($author->ID)) . '">' . "\n";
    }
}
add_action('wp_head', 'epic_prompts_canonical_url', 5);

/**
 * Improve title tag separator and format
 */
function epic_prompts_document_title_separator($separator) {
    return '|';
}
add_filter('document_title_separator', 'epic_prompts_document_title_separator');

/**
 * Customize document title parts
 */
function epic_prompts_document_title_parts($title) {
    if (is_front_page()) {
        $title['title'] = get_bloginfo('name');
        $title['tagline'] = get_bloginfo('description');
    }

    return $title;
}
add_filter('document_title_parts', 'epic_prompts_document_title_parts');
