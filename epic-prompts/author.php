<?php
/**
 * Author/User Profile Template
 */

get_header();

$author_id = get_queried_object_id();
$author = get_user_by('id', $author_id);

if (!$author) {
    echo '<div class="container" style="padding: 4rem 0; text-align: center;"><h1>User not found</h1></div>';
    get_footer();
    exit;
}

$user_stats = epic_prompts_user_stats($author_id);
$is_own_profile = (is_user_logged_in() && get_current_user_id() == $author_id);
?>

<div class="container" style="padding: 3rem 0;">

    <!-- Profile Header -->
    <div class="profile-header" style="background: linear-gradient(135deg, #6366F1, #EC4899); color: white; padding: 3rem 2rem; border-radius: 1rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div class="profile-avatar">
                <?php echo get_avatar($author_id, 120, '', '', array('style' => 'border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);')); ?>
            </div>

            <div class="profile-info" style="flex: 1;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                    <?php echo esc_html($author->display_name); ?>
                </h1>

                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                    <span class="level-badge" style="font-size: 1rem;">
                        Level <?php echo esc_html($user_stats['level']); ?>
                    </span>
                    <span style="opacity: 0.9;">
                        <?php echo esc_html($user_stats['title']); ?>
                    </span>
                </div>

                <?php if ($author->description) : ?>
                    <p style="opacity: 0.95; font-size: 1.125rem; max-width: 600px;">
                        <?php echo esc_html($author->description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="profile-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 3rem;">
        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #6366F1;">
                <?php echo esc_html(epic_prompts_format_number($user_stats['xp_total'])); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Total XP</div>
        </div>

        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #EC4899;">
                <?php echo esc_html($user_stats['prompts_submitted']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Prompts</div>
        </div>

        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #10B981;">
                <?php echo esc_html($user_stats['reputation']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Reputation</div>
        </div>

        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #F59E0B;">
                <?php echo esc_html($user_stats['votes_cast']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Votes Cast</div>
        </div>

        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #8B5CF6;">
                <?php echo esc_html($user_stats['streak_days']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Day Streak 🔥</div>
        </div>

        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #14B8A6;">
                💰 <?php echo esc_html($user_stats['coins']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Coins</div>
        </div>
    </div>

    <!-- Badges Section -->
    <?php if (!empty($user_stats['badges'])) : ?>
        <div class="profile-badges" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">
                🏆 Badges (<?php echo count($user_stats['badges']); ?>)
            </h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <?php foreach ($user_stats['badges'] as $badge_id => $badge) : ?>
                    <div class="badge-item" style="background: #F9FAFB; padding: 1rem 1.5rem; border-radius: 0.5rem; border: 2px solid #E5E7EB;">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">
                            <?php echo esc_html($badge['name']); ?>
                        </div>
                        <div style="font-size: 0.75rem; color: #6B7280;">
                            Earned <?php echo date('M d, Y', strtotime($badge['date'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="profile-tabs" style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="tabs-header" style="display: flex; border-bottom: 2px solid #E5E7EB;">
            <button class="tab-btn active" data-tab="prompts" style="padding: 1rem 2rem; border: none; background: transparent; cursor: pointer; font-weight: 600; border-bottom: 3px solid #6366F1; margin-bottom: -2px;">
                Prompts (<?php echo esc_html($user_stats['prompts_submitted']); ?>)
            </button>
            <?php if ($is_own_profile) : ?>
            <button class="tab-btn" data-tab="saved" style="padding: 1rem 2rem; border: none; background: transparent; cursor: pointer; font-weight: 600;">
                Saved
            </button>
            <?php endif; ?>
            <button class="tab-btn" data-tab="activity" style="padding: 1rem 2rem; border: none; background: transparent; cursor: pointer; font-weight: 600;">
                Activity
            </button>
        </div>

        <div class="tabs-content" style="padding: 2rem;">
            <!-- Prompts Tab -->
            <div id="prompts-tab" class="tab-content">
                <?php
                $user_prompts = new WP_Query(array(
                    'post_type' => 'ai_prompt',
                    'author' => $author_id,
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));

                if ($user_prompts->have_posts()) :
                ?>
                    <div class="prompts-grid">
                        <?php while ($user_prompts->have_posts()) : $user_prompts->the_post(); ?>
                            <?php get_template_part('template-parts/prompt-card'); ?>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p style="text-align: center; color: #6B7280; padding: 2rem 0;">
                        <?php echo $is_own_profile ? 'You haven\'t submitted any prompts yet.' : 'This user hasn\'t submitted any prompts yet.'; ?>
                    </p>
                    <?php if ($is_own_profile) : ?>
                        <div style="text-align: center;">
                            <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn btn-primary">
                                Submit Your First Prompt
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Saved Tab (only for own profile) -->
            <?php if ($is_own_profile) : ?>
            <div id="saved-tab" class="tab-content" style="display: none;">
                <p style="text-align: center; color: #6B7280; padding: 2rem 0;">
                    Saved prompts feature coming soon!
                </p>
            </div>
            <?php endif; ?>

            <!-- Activity Tab -->
            <div id="activity-tab" class="tab-content" style="display: none;">
                <div class="activity-feed">
                    <?php
                    // Get recent comments by user
                    $comments = get_comments(array(
                        'user_id' => $author_id,
                        'number' => 10,
                        'status' => 'approve',
                    ));

                    if ($comments) :
                        foreach ($comments as $comment) :
                    ?>
                        <div class="activity-item" style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                            <div style="color: #6B7280; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                Commented on <a href="<?php echo esc_url(get_comment_link($comment)); ?>" style="font-weight: 600; color: #6366F1;">
                                    <?php echo esc_html(get_the_title($comment->comment_post_ID)); ?>
                                </a>
                                • <?php echo epic_prompts_time_ago($comment->comment_date); ?>
                            </div>
                            <div>
                                <?php echo wp_trim_words($comment->comment_content, 20); ?>
                            </div>
                        </div>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <p style="text-align: center; color: #6B7280; padding: 2rem 0;">
                            No recent activity
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Tabs functionality
    $('.tab-btn').on('click', function() {
        var tab = $(this).data('tab');

        $('.tab-btn').removeClass('active').css({
            'border-bottom': 'none',
            'color': '#6B7280'
        });

        $(this).addClass('active').css({
            'border-bottom': '3px solid #6366F1',
            'color': '#111827'
        });

        $('.tab-content').hide();
        $('#' + tab + '-tab').show();
    });
});
</script>

<?php get_footer(); ?>
