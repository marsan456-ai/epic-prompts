<?php
/**
 * Single Prompt Template
 */

get_header();

// Increment views
if (have_posts()) {
    while (have_posts()) {
        the_post();
        epic_prompts_increment_views(get_the_ID());
        ?>

        <div class="container" style="padding: 3rem 0;">
            <article id="prompt-<?php the_ID(); ?>" <?php post_class('single-prompt'); ?>>
                <div class="single-prompt-hero">
                    <?php
                    $prompt_id = get_the_ID();
                    $author_id = get_the_author_meta('ID');
                    $prompt_text = get_post_meta($prompt_id, 'prompt_text', true);
                    $result_image = get_post_meta($prompt_id, 'result_image', true);
                    $rating_avg = get_post_meta($prompt_id, 'rating_avg', true);
                    $views_count = get_post_meta($prompt_id, 'views_count', true);
                    $saves_count = get_post_meta($prompt_id, 'saves_count', true);
                    $verified_count = get_post_meta($prompt_id, 'verified_count', true);
                    $reactions = epic_prompts_get_reactions($prompt_id);
                    $total_reactions = epic_prompts_get_total_reactions($prompt_id);
                    $platform = epic_prompts_get_platform($prompt_id);
                    $category = epic_prompts_get_category($prompt_id);
                    $user_reaction = is_user_logged_in() ? epic_prompts_user_reaction($prompt_id, get_current_user_id()) : '';
                    ?>

                    <!-- Image Section -->
                    <div class="prompt-image-container">
                        <?php if ($result_image) : ?>
                            <?php echo wp_get_attachment_image($result_image, 'prompt-large', false, array('class' => 'prompt-main-image')); ?>
                        <?php elseif (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('prompt-large', array('class' => 'prompt-main-image')); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Header Section -->
                    <div class="prompt-header">
                        <div>
                            <h1 class="prompt-title"><?php the_title(); ?></h1>

                            <div class="prompt-meta" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                <div class="author-info" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <?php echo get_avatar($author_id, 40); ?>
                                    <div>
                                        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" style="font-weight: 600;">
                                            <?php the_author(); ?>
                                        </a>
                                        <?php epic_prompts_user_level_badge($author_id); ?>
                                        <div style="font-size: 0.875rem; color: #6B7280;">
                                            Posted <?php echo epic_prompts_time_ago(get_the_date('c')); ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($platform) : ?>
                                    <span class="badge badge-platform"><?php echo esc_html($platform); ?></span>
                                <?php endif; ?>

                                <?php if ($category) : ?>
                                    <span class="badge badge-category"><?php echo esc_html($category); ?></span>
                                <?php endif; ?>

                                <?php if ($verified_count >= 5) : ?>
                                    <span class="badge badge-verified">✓ Verified by <?php echo esc_html($verified_count); ?> users</span>
                                <?php endif; ?>
                            </div>

                            <!-- Stats Row -->
                            <div style="display: flex; gap: 2rem; margin-top: 1rem; flex-wrap: wrap;">
                                <?php if ($rating_avg > 0) : ?>
                                    <div class="stat-item">
                                        <span style="font-size: 1.125rem;">⭐ <?php echo number_format($rating_avg, 1); ?>/5</span>
                                    </div>
                                <?php endif; ?>
                                <div class="stat-item">
                                    <span style="font-size: 1.125rem;">🔥 <?php echo esc_html(epic_prompts_format_number($total_reactions)); ?> reactions</span>
                                </div>
                                <div class="stat-item">
                                    <span style="font-size: 1.125rem;">👁️ <?php echo esc_html(epic_prompts_format_number($views_count)); ?> views</span>
                                </div>
                                <div class="stat-item">
                                    <span style="font-size: 1.125rem;">🔖 <?php echo esc_html(epic_prompts_format_number($saves_count)); ?> saves</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <?php if (get_the_content()) : ?>
                        <div class="prompt-description" style="margin: 2rem 0; line-height: 1.8;">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- The Prompt -->
                    <div class="prompt-text-box">
                        <h3 style="margin-bottom: 1rem; font-size: 1.25rem;">The Prompt:</h3>
                        <div class="prompt-text" id="prompt-text-content">
                            <?php echo nl2br(esc_html($prompt_text)); ?>
                        </div>
                        <button class="btn btn-primary copy-prompt-btn" id="copy-prompt-btn" data-clipboard-text="<?php echo esc_attr($prompt_text); ?>">
                            📋 Copy Prompt
                        </button>
                        <span id="copy-feedback" style="margin-left: 1rem; color: #10B981; font-weight: 600; display: none;">
                            ✓ Copied!
                        </span>
                    </div>

                    <!-- Reactions Section -->
                    <div class="reactions-section" style="margin: 2rem 0;">
                        <h3 style="margin-bottom: 1rem;">React to this prompt:</h3>
                        <div class="reactions-container" id="reactions-container">
                            <?php
                            $reaction_emojis = array(
                                'fire' => '🔥',
                                'gem' => '💎',
                                'creative' => '🎨',
                                'rocket' => '🚀',
                                'mindblown' => '💡',
                            );

                            foreach ($reaction_emojis as $type => $emoji) :
                                $is_active = ($user_reaction === $type) ? 'active' : '';
                                $count = isset($reactions[$type]) ? $reactions[$type] : 0;
                            ?>
                                <button
                                    class="reaction-btn <?php echo esc_attr($is_active); ?>"
                                    data-reaction-type="<?php echo esc_attr($type); ?>"
                                    data-prompt-id="<?php echo esc_attr($prompt_id); ?>"
                                >
                                    <span class="reaction-emoji"><?php echo $emoji; ?></span>
                                    <span class="reaction-count" data-reaction="<?php echo esc_attr($type); ?>"><?php echo esc_html($count); ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!is_user_logged_in()) : ?>
                            <p style="margin-top: 1rem; color: #6B7280;">
                                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" style="color: #6366F1; font-weight: 600;">Login</a> to react to this prompt
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Tags -->
                    <?php
                    $tags = get_the_terms($prompt_id, 'prompt_tag');
                    if ($tags && !is_wp_error($tags)) :
                    ?>
                        <div class="prompt-tags" style="margin: 2rem 0;">
                            <h4 style="margin-bottom: 0.5rem;">Tags:</h4>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php foreach ($tags as $tag) : ?>
                                    <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="badge" style="background: #F3F4F6; color: #374151;">
                                        #<?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <?php
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>
            </article>
        </div>

        <?php
    }
}

get_footer();
?>
