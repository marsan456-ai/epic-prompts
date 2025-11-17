<?php get_header(); ?>

<!-- Hero Section -->
<div style="background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); padding: 4rem 0; color: white;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 1rem;">
            Epic Prompts for Every AI
        </h1>
        <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 2rem; max-width: 700px; margin-left: auto; margin-right: auto;">
            Discover, share, and master AI prompts for ChatGPT, Claude, Midjourney, Suno, GitHub Copilot, and 50+ AI platforms. Earn XP, level up, and compete!
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" class="btn" style="background: white; color: #667EEA;">
                Browse Prompts
            </a>
            <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn btn-secondary">
                Submit Prompt (+10 XP)
            </a>
        </div>
    </div>
</div>

<div class="container" style="padding: 3rem 0;">
    <!-- Stats Section -->
    <div class="featured-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 4rem;">
        <div class="stat-card" style="background: white; padding: 2rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #6366F1;">
                <?php echo number_format(wp_count_posts('ai_prompt')->publish); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Prompts Shared</div>
        </div>
        <div class="stat-card" style="background: white; padding: 2rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #EC4899;">
                <?php echo number_format(count_users()['total_users']); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Community Members</div>
        </div>
        <div class="stat-card" style="background: white; padding: 2rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #10B981;">
                50+
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">AI Platforms</div>
        </div>
        <div class="stat-card" style="background: white; padding: 2rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; font-weight: 700; color: #F59E0B;">
                <?php echo number_format(wp_count_posts('prompt_vote')->publish); ?>
            </div>
            <div style="color: #6B7280; margin-top: 0.5rem;">Votes Cast</div>
        </div>
    </div>

    <!-- Browse by Prompt Type -->
    <div style="margin-bottom: 4rem;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center;">
            Browse by Prompt Type
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
            <?php
            $featured_types = array(
                array('name' => 'Text Generation', 'icon' => '✍️', 'color' => '#6366F1'),
                array('name' => 'Image Generation', 'icon' => '🎨', 'color' => '#EC4899'),
                array('name' => 'Code Generation', 'icon' => '💻', 'color' => '#10B981'),
                array('name' => 'Video Generation', 'icon' => '🎬', 'color' => '#F59E0B'),
                array('name' => 'Audio Generation', 'icon' => '🎤', 'color' => '#8B5CF6'),
                array('name' => 'Music Generation', 'icon' => '🎵', 'color' => '#EF4444'),
                array('name' => 'Data Analysis', 'icon' => '📊', 'color' => '#3B82F6'),
                array('name' => 'Content Writing', 'icon' => '📝', 'color' => '#14B8A6'),
            );

            foreach ($featured_types as $type_data) {
                $type = get_term_by('name', $type_data['name'], 'prompt_type');
                if ($type) :
                    $link = get_term_link($type);
                    ?>
                    <a href="<?php echo esc_url($link); ?>"
                       style="display: block; background: white; padding: 1.5rem; border-radius: 1rem; text-align: center; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;"
                       onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)';">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                            <?php echo $type_data['icon']; ?>
                        </div>
                        <div style="font-weight: 600; color: <?php echo $type_data['color']; ?>; font-size: 0.875rem;">
                            <?php echo esc_html($type->name); ?>
                        </div>
                        <div style="color: #9CA3AF; font-size: 0.75rem; margin-top: 0.25rem;">
                            <?php echo $type->count; ?> prompts
                        </div>
                    </a>
                <?php endif;
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" class="btn btn-outline">
                View All Types →
            </a>
        </div>
    </div>

    <!-- Popular AI Platforms -->
    <div style="margin-bottom: 4rem; background: #F9FAFB; padding: 2rem; border-radius: 1rem;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center;">
            Popular AI Platforms
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <?php
            $popular_platforms = get_terms(array(
                'taxonomy' => 'ai_platform',
                'orderby' => 'count',
                'order' => 'DESC',
                'number' => 8,
                'hide_empty' => false,
            ));

            foreach ($popular_platforms as $platform) :
                $link = get_term_link($platform);
                ?>
                <a href="<?php echo esc_url($link); ?>"
                   style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem 1.5rem; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#F3F4F6'"
                   onmouseout="this.style.background='white'">
                    <span style="font-weight: 600; color: #111827;">
                        <?php echo esc_html($platform->name); ?>
                    </span>
                    <span style="background: #EEF2FF; color: #6366F1; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                        <?php echo $platform->count; ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Latest Prompts -->
    <div style="margin-bottom: 4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 2rem; font-weight: 700;">
                🔥 Latest Prompts
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" style="color: #6366F1; font-weight: 600; text-decoration: none;">
                View All →
            </a>
        </div>

        <?php
        $latest_prompts = new WP_Query(array(
            'post_type' => 'ai_prompt',
            'posts_per_page' => 6,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ));

        if ($latest_prompts->have_posts()) :
        ?>
            <div class="prompts-grid">
                <?php while ($latest_prompts->have_posts()) : $latest_prompts->the_post(); ?>
                    <?php get_template_part('template-parts/prompt-card'); ?>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 1rem;">
                <p style="font-size: 1.125rem; color: #6B7280; margin-bottom: 1rem;">
                    No prompts yet. Be the first to submit one!
                </p>
                <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn btn-primary">
                    Submit First Prompt
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Leaderboard Preview -->
    <?php
    $leaderboard = EP_XP_System::get_leaderboard(5, 'total');
    if (!empty($leaderboard)) :
    ?>
        <div style="margin-bottom: 4rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 2rem; font-weight: 700;">
                    🏆 Top Contributors
                </h2>
                <a href="<?php echo esc_url(home_url('/leaderboard')); ?>" style="color: #6366F1; font-weight: 600; text-decoration: none;">
                    Full Leaderboard →
                </a>
            </div>
            <div style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
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
                                <div style="font-weight: 600;">
                                    <?php echo esc_html($entry['username']); ?>
                                </div>
                                <div style="color: #6B7280; font-size: 0.875rem;">
                                    <?php echo esc_html($entry['title']); ?>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <span class="level-badge">Lv <?php echo esc_html($entry['level']); ?></span>
                        </div>
                        <div style="text-align: right; font-weight: 700; color: #6366F1;">
                            <?php echo number_format($entry['xp']); ?> XP
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- CTA Section -->
    <div style="text-align: center; padding: 3rem; background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); border-radius: 1rem; color: white;">
        <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">
            Ready to Share Your Best Prompts?
        </h2>
        <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Join thousands of AI enthusiasts sharing prompts, earning XP, and climbing the leaderboard. Level up your AI game!
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn" style="background: white; color: #667EEA;">
                Submit Prompt (+10 XP)
            </a>
            <?php if (!is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-outline" style="border-color: white; color: white;">
                    Join Community
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
