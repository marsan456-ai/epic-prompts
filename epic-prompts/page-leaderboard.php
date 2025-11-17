<?php
/**
 * Template Name: Leaderboard
 */

get_header();
?>

<div class="container" style="padding: 3rem 0;">
    <div class="leaderboard-header" style="text-align: center; margin-bottom: 3rem;">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
            🏆 Top Prompters
        </h1>
        <p style="color: #6B7280; font-size: 1.125rem;">
            The most active and valuable members of our community
        </p>
    </div>

    <div class="leaderboard-tabs" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem;">
        <button class="leaderboard-tab active" data-type="total" style="padding: 0.75rem 1.5rem; border: none; background: #6366F1; color: white; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
            All Time
        </button>
        <button class="leaderboard-tab" data-type="monthly" style="padding: 0.75rem 1.5rem; border: 2px solid #6366F1; background: transparent; color: #6366F1; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
            This Month
        </button>
    </div>

    <div id="leaderboard-content">
        <?php
        // Get top 50 users by total XP
        $leaderboard = EP_XP_System::get_leaderboard(50, 'total');

        if (!empty($leaderboard)) :
        ?>
            <div class="leaderboard-table">
                <?php foreach ($leaderboard as $entry) : ?>
                    <div class="leaderboard-row">
                        <div class="rank-number <?php echo ($entry['rank'] <= 3) ? 'top-3' : ''; ?>">
                            <?php if ($entry['rank'] == 1) : ?>
                                🥇
                            <?php elseif ($entry['rank'] == 2) : ?>
                                🥈
                            <?php elseif ($entry['rank'] == 3) : ?>
                                🥉
                            <?php else : ?>
                                #<?php echo $entry['rank']; ?>
                            <?php endif; ?>
                        </div>

                        <div class="user-info">
                            <img src="<?php echo esc_url($entry['avatar']); ?>" alt="<?php echo esc_attr($entry['username']); ?>" class="user-avatar">
                            <div>
                                <div style="font-weight: 600; font-size: 1.125rem;">
                                    <a href="<?php echo esc_url(get_author_posts_url($entry['user_id'])); ?>">
                                        <?php echo esc_html($entry['username']); ?>
                                    </a>
                                </div>
                                <div style="color: #6B7280; font-size: 0.875rem;">
                                    <?php echo esc_html($entry['title']); ?>
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center;">
                            <span class="level-badge">Lv <?php echo esc_html($entry['level']); ?></span>
                        </div>

                        <div style="text-align: right; font-weight: 700; color: #6366F1; font-size: 1.25rem;">
                            <?php echo esc_html(epic_prompts_format_number($entry['xp'])); ?> XP
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="text-align: center; padding: 4rem 0;">
                <p style="font-size: 1.25rem; color: #6B7280;">
                    No users in the leaderboard yet. Be the first!
                </p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (is_user_logged_in()) : ?>
        <?php
        $current_user_id = get_current_user_id();
        $user_stats = epic_prompts_user_stats($current_user_id);

        // Find current user rank
        $user_rank = 0;
        foreach ($leaderboard as $entry) {
            if ($entry['user_id'] == $current_user_id) {
                $user_rank = $entry['rank'];
                break;
            }
        }
        ?>

        <div class="your-rank" style="background: linear-gradient(135deg, #6366F1, #EC4899); color: white; padding: 2rem; border-radius: 1rem; margin-top: 3rem; text-align: center;">
            <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">Your Current Rank</h3>
            <div style="display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 3rem; font-weight: 700;">
                        <?php echo $user_rank > 0 ? '#' . $user_rank : 'Not Ranked'; ?>
                    </div>
                    <div style="opacity: 0.9;">Rank</div>
                </div>
                <div>
                    <div style="font-size: 3rem; font-weight: 700;">
                        Lv <?php echo $user_stats['level']; ?>
                    </div>
                    <div style="opacity: 0.9;">Level</div>
                </div>
                <div>
                    <div style="font-size: 3rem; font-weight: 700;">
                        <?php echo epic_prompts_format_number($user_stats['xp_total']); ?>
                    </div>
                    <div style="opacity: 0.9;">Total XP</div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Leaderboard tabs (simple client-side for now, can make AJAX later)
    $('.leaderboard-tab').on('click', function() {
        var type = $(this).data('type');

        $('.leaderboard-tab').removeClass('active').css({
            'background': 'transparent',
            'color': '#6366F1'
        });

        $(this).addClass('active').css({
            'background': '#6366F1',
            'color': 'white'
        });

        // AJAX call to get leaderboard data
        $.ajax({
            url: epicPromptsTheme.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_leaderboard',
                nonce: epicPromptsTheme.nonce,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // Update leaderboard content (would need server endpoint)
                    console.log('Leaderboard data:', response.data);
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
