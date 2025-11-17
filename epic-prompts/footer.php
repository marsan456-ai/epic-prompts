</main>

<footer class="site-footer" style="background-color: #111827; color: #9CA3AF; padding: 4rem 0 2rem; margin-top: 4rem;">
    <div class="container">
        <div class="footer-content" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 3rem;">

            <!-- Brand Section -->
            <div class="footer-section">
                <h3 style="color: #6366F1; margin-bottom: 1rem; font-size: 1.5rem;">Epic<span style="color: #EC4899;">Prompts</span></h3>
                <p style="line-height: 1.6; margin-bottom: 1.5rem;">
                    The world's largest gamified community for sharing and discovering AI prompts across 50+ platforms.
                </p>
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #F9FAFB;">Join <?php echo number_format(count_users()['total_users']); ?>+ creators</strong>
                </div>
                <?php if (!is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary" style="display: inline-block; font-size: 0.875rem; padding: 0.5rem 1rem;">
                    Sign Up Free
                </a>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h4 style="color: #F9FAFB; margin-bottom: 1rem; font-size: 1.125rem;">Explore</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(get_post_type_archive_link('ai_prompt')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Browse Prompts</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/submit')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Submit Prompt</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/leaderboard')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Leaderboard</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/how-it-works')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">How It Works</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="footer-section">
                <h4 style="color: #F9FAFB; margin-bottom: 1rem; font-size: 1.125rem;">Resources</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/about')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">About Us</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/faq')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">FAQ</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/api-docs')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">API Documentation</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/blog')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Blog</a></li>
                </ul>
            </div>

            <!-- Legal & Social -->
            <div class="footer-section">
                <h4 style="color: #F9FAFB; margin-bottom: 1rem; font-size: 1.125rem;">Legal</h4>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0;">
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/privacy')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Privacy Policy</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/terms')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Terms of Service</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="<?php echo esc_url(home_url('/guidelines')); ?>" style="color: #9CA3AF; transition: color 0.2s;" onmouseover="this.style.color='#F9FAFB'" onmouseout="this.style.color='#9CA3AF'">Community Guidelines</a></li>
                </ul>

                <!-- Social Links -->
                <?php
                $social_networks = array(
                    'facebook' => array('icon' => 'Facebook', 'label' => 'Facebook'),
                    'twitter' => array('icon' => '𝕏', 'label' => 'X (Twitter)'),
                    'instagram' => array('icon' => 'Instagram', 'label' => 'Instagram'),
                    'discord' => array('icon' => 'Discord', 'label' => 'Discord'),
                    'github' => array('icon' => 'GitHub', 'label' => 'GitHub'),
                );

                $has_social = false;
                foreach ($social_networks as $network => $data) {
                    if (get_theme_mod("epic_prompts_social_{$network}")) {
                        $has_social = true;
                        break;
                    }
                }

                if ($has_social) :
                ?>
                <h4 style="color: #F9FAFB; margin-bottom: 1rem; font-size: 1.125rem;">Connect</h4>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <?php foreach ($social_networks as $network => $data) :
                        $url = get_theme_mod("epic_prompts_social_{$network}");
                        if ($url) :
                    ?>
                        <a href="<?php echo esc_url($url); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="<?php echo esc_attr($data['label']); ?>"
                           style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #1F2937; color: #9CA3AF; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none;"
                           onmouseover="this.style.background='#6366F1'; this.style.color='white'"
                           onmouseout="this.style.background='#1F2937'; this.style.color='#9CA3AF'">
                            <?php echo esc_html(substr($data['icon'], 0, 1)); ?>
                        </a>
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Stats Bar -->
        <div style="background: #1F2937; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; text-align: center;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #6366F1;">
                        <?php echo number_format(wp_count_posts('ai_prompt')->publish); ?>+
                    </div>
                    <div style="font-size: 0.875rem;">AI Prompts</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #EC4899;">
                        50+
                    </div>
                    <div style="font-size: 0.875rem;">AI Platforms</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #10B981;">
                        <?php echo number_format(count_users()['total_users']); ?>+
                    </div>
                    <div style="font-size: 0.875rem;">Community Members</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #F59E0B;">
                        20+
                    </div>
                    <div style="font-size: 0.875rem;">Categories</div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom" style="padding-top: 2rem; border-top: 1px solid #374151; text-align: center;">
            <p style="margin-bottom: 0.5rem;">
                &copy; <?php echo date('Y'); ?> Epic Prompts. All rights reserved. Made with ❤️ for the AI community.
            </p>
            <p style="font-size: 0.875rem; opacity: 0.7;">
                Powered by <a href="https://wordpress.org" target="_blank" style="color: #6366F1;">WordPress</a>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
