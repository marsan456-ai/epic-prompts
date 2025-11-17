<?php
/**
 * Template part for displaying prompt cards
 */

$prompt_id = get_the_ID();
$author_id = get_the_author_meta('ID');
$result_image = get_post_meta($prompt_id, 'result_image', true);
$rating_avg = get_post_meta($prompt_id, 'rating_avg', true);
$views_count = get_post_meta($prompt_id, 'views_count', true);
$verified_count = get_post_meta($prompt_id, 'verified_count', true);
$reactions = epic_prompts_get_reactions($prompt_id);
$total_reactions = epic_prompts_get_total_reactions($prompt_id);
$platform = epic_prompts_get_platform($prompt_id);
$category = epic_prompts_get_category($prompt_id);
?>

<article class="prompt-card" data-prompt-id="<?php echo esc_attr($prompt_id); ?>">
    <a href="<?php the_permalink(); ?>">
        <?php if ($result_image) : ?>
            <?php echo wp_get_attachment_image($result_image, 'prompt-thumbnail', false, array('class' => 'prompt-card-image')); ?>
        <?php elseif (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('prompt-thumbnail', array('class' => 'prompt-card-image')); ?>
        <?php else : ?>
            <div class="prompt-card-image" style="background: linear-gradient(135deg, #6366F1, #EC4899); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                💡
            </div>
        <?php endif; ?>
    </a>

    <div class="prompt-card-content">
        <h3 class="prompt-card-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>

        <div class="prompt-card-meta">
            <div class="prompt-card-author">
                <?php echo get_avatar($author_id, 24, '', '', array('class' => 'author-avatar')); ?>
                <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>">
                    <?php echo esc_html(get_the_author()); ?>
                </a>
                <?php epic_prompts_user_level_badge($author_id); ?>
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
            <?php if ($platform) : ?>
                <span class="badge badge-platform"><?php echo esc_html($platform); ?></span>
            <?php endif; ?>
            <?php if ($category) : ?>
                <span class="badge badge-category"><?php echo esc_html($category); ?></span>
            <?php endif; ?>
            <?php if ($verified_count >= 5) : ?>
                <span class="badge badge-verified">✓ Verified</span>
            <?php endif; ?>
        </div>

        <div class="prompt-card-stats">
            <?php if ($rating_avg > 0) : ?>
                <div class="stat-item">
                    <span>⭐</span>
                    <span><?php echo number_format($rating_avg, 1); ?>/5</span>
                </div>
            <?php endif; ?>

            <?php if ($total_reactions > 0) : ?>
                <div class="stat-item">
                    <span>🔥</span>
                    <span><?php echo esc_html(epic_prompts_format_number($total_reactions)); ?></span>
                </div>
            <?php endif; ?>

            <div class="stat-item">
                <span>👁️</span>
                <span><?php echo esc_html(epic_prompts_format_number($views_count)); ?></span>
            </div>

            <div class="stat-item" style="margin-left: auto; color: #9CA3AF; font-size: 0.75rem;">
                <?php echo esc_html(epic_prompts_time_ago(get_the_date('c'))); ?>
            </div>
        </div>
    </div>
</article>
