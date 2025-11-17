<?php
/**
 * Archive template for AI Prompts
 */

get_header();
?>

<div class="container" style="padding: 3rem 0;">
    <div class="archive-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">
            Browse Prompts
        </h1>
        <p style="color: #6B7280;">Discover and vote on the best AI prompts from the community</p>
    </div>

    <div class="filters-container">
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
                    <label for="filter-type">Prompt Type</label>
                    <select id="filter-type" name="prompt_type" class="search-input">
                        <option value="">All Types</option>
                        <?php
                        $types = get_terms(array(
                            'taxonomy' => 'prompt_type',
                            'hide_empty' => false,
                        ));
                        $current_type = isset($_GET['prompt_type']) ? $_GET['prompt_type'] : '';
                        foreach ($types as $type) {
                            $selected = ($current_type == $type->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($type->slug) . '" ' . $selected . '>' . esc_html($type->name) . '</option>';
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
                <a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" class="btn btn-outline">Reset</a>
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

    // Prompt type filter
    if (!empty($_GET['prompt_type'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'prompt_type',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['prompt_type']),
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
                No prompts found matching your criteria.
            </p>
            <a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" class="btn btn-outline">
                Clear Filters
            </a>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
