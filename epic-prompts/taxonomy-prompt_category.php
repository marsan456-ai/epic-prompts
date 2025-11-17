<?php
/**
 * Taxonomy Archive: Prompt Category
 * Displays all prompts in a specific category
 */

get_header();

$term = get_queried_object();
$term_id = $term->term_id;
$term_name = $term->name;
$term_description = $term->description;
$term_count = $term->count;

// Define colors for different category groups
$category_colors = array(
    // Visual/Image
    'Character Design' => array('bg' => '#FEE2E2', 'color' => '#991B1B'),
    'Landscapes' => array('bg' => '#DBEAFE', 'color' => '#1E40AF'),
    'Product Photography' => array('bg' => '#FCE7F3', 'color' => '#9F1239'),
    'Logo & Branding' => array('bg' => '#E0E7FF', 'color' => '#3730A3'),
    'Fantasy Art' => array('bg' => '#F3E8FF', 'color' => '#6B21A8'),
    'Portraits' => array('bg' => '#FEE2E2', 'color' => '#991B1B'),

    // Code
    'Web Development' => array('bg' => '#DBEAFE', 'color' => '#1E3A8A'),
    'Mobile Apps' => array('bg' => '#DBEAFE', 'color' => '#1E40AF'),
    'Data Science' => array('bg' => '#D1FAE5', 'color' => '#065F46'),
    'Machine Learning' => array('bg' => '#D1FAE5', 'color' => '#047857'),

    // Business
    'Business Strategy' => array('bg' => '#FEF3C7', 'color' => '#92400E'),
    'Market Research' => array('bg' => '#FEF3C7', 'color' => '#78350F'),
    'Sales' => array('bg' => '#DCFCE7', 'color' => '#14532D'),
);

$colors = isset($category_colors[$term_name])
    ? $category_colors[$term_name]
    : array('bg' => '#F3F4F6', 'color' => '#1F2937');
?>

<div class="container" style="padding: 3rem 0;">
    <div class="archive-header" style="margin-bottom: 2rem; text-align: center;">
        <div style="display: inline-block; padding: 0.5rem 1rem; background: <?php echo $colors['bg']; ?>; color: <?php echo $colors['color']; ?>; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">
            CATEGORY
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
                'taxonomy' => 'prompt_category',
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

    // Type filter
    if (!empty($_GET['prompt_type'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'prompt_type',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['prompt_type']),
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

    <!-- Related Categories -->
    <?php
    $parent_id = $term->parent;
    if ($parent_id) {
        // Show sibling categories
        $siblings = get_terms(array(
            'taxonomy' => 'prompt_category',
            'parent' => $parent_id,
            'exclude' => $term_id,
            'hide_empty' => false,
            'number' => 5,
        ));
    } else {
        // Show child categories or popular categories
        $siblings = get_terms(array(
            'taxonomy' => 'prompt_category',
            'orderby' => 'count',
            'order' => 'DESC',
            'exclude' => $term_id,
            'hide_empty' => false,
            'number' => 5,
        ));
    }

    if (!empty($siblings)) :
    ?>
        <div style="margin-top: 3rem; padding: 2rem; background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">
                Related Categories
            </h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <?php foreach ($siblings as $sibling) : ?>
                    <a href="<?php echo esc_url(get_term_link($sibling)); ?>"
                       style="display: inline-block; padding: 0.5rem 1rem; background: #F3F4F6; color: #374151; border-radius: 9999px; font-size: 0.875rem; text-decoration: none; transition: background 0.2s;"
                       onmouseover="this.style.background='#E5E7EB'"
                       onmouseout="this.style.background='#F3F4F6'">
                        <?php echo esc_html($sibling->name); ?>
                        <span style="color: #9CA3AF; margin-left: 0.25rem;">(<?php echo $sibling->count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 3rem; text-align: center; padding: 2rem; background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); border-radius: 1rem; color: white;">
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
            Master of <?php echo esc_html($term_name); ?>?
        </h3>
        <p style="opacity: 0.9; margin-bottom: 1.5rem;">
            Share your expertise with the community!
        </p>
        <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn" style="background: white; color: #667EEA;">
            Submit Prompt (+10 XP)
        </a>
    </div>
</div>

<?php get_footer(); ?>
