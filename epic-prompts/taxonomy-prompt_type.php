<?php
/**
 * Taxonomy Archive: Prompt Type
 * Displays all prompts of a specific type (e.g., Text Generation, Image Generation)
 */

get_header();

$term = get_queried_object();
$term_id = $term->term_id;
$term_name = $term->name;
$term_description = $term->description;
$term_count = $term->count;

// Define icons for different prompt types
$type_icons = array(
    'Text Generation' => '✍️',
    'Image Generation' => '🎨',
    'Code Generation' => '💻',
    'Video Generation' => '🎬',
    'Audio Generation' => '🎤',
    'Music Generation' => '🎵',
    'Data Analysis' => '📊',
    'Content Writing' => '📝',
    'Creative Writing' => '✨',
    'Translation' => '🌐',
    'Summarization' => '📋',
    'Question Answering' => '❓',
    'Chatbot' => '🤖',
    'Role-Playing' => '🎭',
    'Brainstorming' => '💡',
    'Problem Solving' => '🧩',
    'Education' => '📚',
    'Business' => '💼',
    'Marketing' => '📢',
    'SEO' => '🔍',
);

$icon = isset($type_icons[$term_name]) ? $type_icons[$term_name] : '🚀';
?>

<div class="container" style="padding: 3rem 0;">
    <div class="archive-header" style="margin-bottom: 2rem; text-align: center;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">
            <?php echo $icon; ?>
        </div>
        <div style="display: inline-block; padding: 0.5rem 1rem; background: #FEF3C7; color: #F59E0B; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">
            PROMPT TYPE
        </div>
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">
            <?php echo esc_html($term_name); ?>
        </h1>
        <?php if ($term_description) : ?>
            <p style="color: #6B7280; font-size: 1.125rem; margin-bottom: 0.5rem;">
                <?php echo esc_html($term_description); ?>
            </p>
        <?php endif; ?>
        <p style="color: #6B7280;">
            <strong><?php echo number_format($term_count); ?></strong> prompts available
        </p>
    </div>

    <div class="filters-container" style="margin-bottom: 2rem;">
        <form id="prompts-filter-form" method="get">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div class="filter-group">
                    <label for="search-prompts">Search</label>
                    <input
                        type="text"
                        id="search-prompts"
                        name="s"
                        class="search-input"
                        placeholder="Search prompts..."
                        value="<?php echo esc_attr(get_search_query()); ?>"
                    >
                </div>

                <div class="filter-group">
                    <label for="filter-platform">AI Platform</label>
                    <select id="filter-platform" name="platform" class="search-input">
                        <option value="">All Platforms</option>
                        <?php
                        $platforms = get_terms(array(
                            'taxonomy' => 'ai_platform',
                            'hide_empty' => false,
                        ));
                        $current_platform = isset($_GET['platform']) ? $_GET['platform'] : '';
                        foreach ($platforms as $platform) {
                            $selected = ($current_platform == $platform->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($platform->slug) . '" ' . $selected . '>' . esc_html($platform->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter-category">Category</label>
                    <select id="filter-category" name="category" class="search-input">
                        <option value="">All Categories</option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'prompt_category',
                            'hide_empty' => false,
                        ));
                        $current_category = isset($_GET['category']) ? $_GET['category'] : '';
                        foreach ($categories as $category) {
                            $selected = ($current_category == $category->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter-sort">Sort By</label>
                    <select id="filter-sort" name="orderby" class="search-input">
                        <?php
                        $current_sort = isset($_GET['orderby']) ? $_GET['orderby'] : 'newest';
                        ?>
                        <option value="newest" <?php selected($current_sort, 'newest'); ?>>Newest</option>
                        <option value="top_rated" <?php selected($current_sort, 'top_rated'); ?>>Top Rated</option>
                        <option value="most_views" <?php selected($current_sort, 'most_views'); ?>>Most Views</option>
                        <option value="most_verified" <?php selected($current_sort, 'most_verified'); ?>>Most Verified</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="<?php echo esc_url(get_term_link($term)); ?>" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <?php
    // Build query args
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $query_args = array(
        'post_type' => 'ai_prompt',
        'posts_per_page' => 12,
        'paged' => $paged,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'prompt_type',
                'field' => 'term_id',
                'terms' => $term_id,
            ),
        ),
    );

    // Search
    if (!empty($_GET['s'])) {
        $query_args['s'] = sanitize_text_field($_GET['s']);
    }

    // Platform filter
    if (!empty($_GET['platform'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'ai_platform',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['platform']),
        );
    }

    // Category filter
    if (!empty($_GET['category'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'prompt_category',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['category']),
        );
    }

    // Sort
    $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'newest';
    switch ($orderby) {
        case 'top_rated':
            $query_args['meta_key'] = 'rating_avg';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
        case 'most_views':
            $query_args['meta_key'] = 'views_count';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
        case 'most_verified':
            $query_args['meta_key'] = 'verified_count';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
        default: // newest
            $query_args['orderby'] = 'date';
            $query_args['order'] = 'DESC';
    }

    $prompts_query = new WP_Query($query_args);

    if ($prompts_query->have_posts()) :
    ?>
        <div class="prompts-grid" id="prompts-grid">
            <?php while ($prompts_query->have_posts()) : $prompts_query->the_post(); ?>
                <?php get_template_part('template-parts/prompt-card'); ?>
            <?php endwhile; ?>
        </div>

        <?php
        // Pagination
        $total_pages = $prompts_query->max_num_pages;
        if ($total_pages > 1) :
        ?>
            <div class="pagination" style="margin-top: 3rem; text-align: center;">
                <?php
                echo paginate_links(array(
                    'total' => $total_pages,
                    'current' => $paged,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                ));
                ?>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <div style="text-align: center; padding: 4rem 0;">
            <p style="font-size: 1.25rem; color: #6B7280; margin-bottom: 1rem;">
                No <?php echo esc_html($term_name); ?> prompts found matching your criteria.
            </p>
            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="btn btn-outline">
                Clear Filters
            </a>
        </div>
    <?php endif; ?>

    <div style="margin-top: 3rem; text-align: center; padding: 2rem; background: #FEF9E7; border-radius: 1rem;">
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
            Have an amazing <?php echo esc_html($term_name); ?> prompt?
        </h3>
        <p style="color: #6B7280; margin-bottom: 1.5rem;">
            Share it with the community and earn XP, coins, and badges!
        </p>
        <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn btn-primary">
            Submit Prompt (+10 XP)
        </a>
    </div>
</div>

<?php get_footer(); ?>
