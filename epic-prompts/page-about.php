<?php
/**
 * Template Name: About Us
 * Description: About Epic Prompts platform
 */

get_header();
?>

<div style="background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); padding: 4rem 0; color: white;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">
            About Epic Prompts
        </h1>
        <p style="font-size: 1.25rem; opacity: 0.9;">
            The World's Largest AI Prompts Community
        </p>
    </div>
</div>

<div class="container" style="padding: 4rem 0;">

    <!-- Mission Statement -->
    <div style="max-width: 800px; margin: 0 auto 5rem;">
        <h2 style="font-size: 2.5rem; font-weight: 700; text-align: center; margin-bottom: 2rem;">
            Our Mission
        </h2>
        <p style="font-size: 1.25rem; line-height: 1.8; color: #374151; text-align: center;">
            We believe that AI is transforming how we create, work, and think. Epic Prompts exists to democratize AI prompting knowledge by creating a vibrant, gamified community where everyone can share, discover, and master the art of prompting across all AI platforms.
        </p>
    </div>

    <!-- What We Do -->
    <div style="margin-bottom: 5rem;">
        <h2 style="font-size: 2.5rem; font-weight: 700; text-align: center; margin-bottom: 3rem;">
            What We Do
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <div style="padding: 2rem; background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Build Community</h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Connect prompt creators, AI enthusiasts, and learners in a supportive, collaborative environment.
                </p>
            </div>

            <div style="padding: 2rem; background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Share Knowledge</h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Create the world's most comprehensive library of AI prompts across 50+ platforms.
                </p>
            </div>

            <div style="padding: 2rem; background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎮</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Make It Fun</h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Gamify learning with XP, levels, badges, and leaderboards that reward quality contributions.
                </p>
            </div>
        </div>
    </div>

    <!-- Our Values -->
    <div style="margin-bottom: 5rem; background: #F9FAFB; padding: 3rem; border-radius: 1rem;">
        <h2 style="font-size: 2.5rem; font-weight: 700; text-align: center; margin-bottom: 3rem;">
            Our Values
        </h2>

        <div style="max-width: 700px; margin: 0 auto;">
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #6366F1;">
                    🌟 Quality First
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    We prioritize high-quality, tested prompts that actually work. Our verification system ensures the community gets the best.
                </p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #EC4899;">
                    🤲 Open & Inclusive
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Everyone is welcome. From beginners to experts, all AI platforms, all prompt types. No gatekeeping.
                </p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #10B981;">
                    🚀 Innovation Driven
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    We constantly evolve with the AI landscape, adding new platforms, features, and ways to help the community succeed.
                </p>
            </div>

            <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #F59E0B;">
                    💡 Learning Focused
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Every feature is designed to help you learn, improve, and master AI prompting through practice and community feedback.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div style="margin-bottom: 5rem;">
        <h2 style="font-size: 2.5rem; font-weight: 700; text-align: center; margin-bottom: 3rem;">
            Epic Prompts by Numbers
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); border-radius: 1rem; color: white;">
                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <?php echo number_format(wp_count_posts('ai_prompt')->publish); ?>+
                </div>
                <div style="opacity: 0.9;">AI Prompts Shared</div>
            </div>

            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #EC4899 0%, #F59E0B 100%); border-radius: 1rem; color: white;">
                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
                    50+
                </div>
                <div style="opacity: 0.9;">AI Platforms Supported</div>
            </div>

            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #10B981 0%, #3B82F6 100%); border-radius: 1rem; color: white;">
                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <?php echo number_format(count_users()['total_users']); ?>+
                </div>
                <div style="opacity: 0.9;">Community Members</div>
            </div>

            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); border-radius: 1rem; color: white;">
                <div style="font-size: 3rem; font-weight: 700; margin-bottom: 0.5rem;">
                    20+
                </div>
                <div style="opacity: 0.9;">Prompt Categories</div>
            </div>
        </div>
    </div>

    <!-- Platform Features -->
    <div style="margin-bottom: 5rem;">
        <h2 style="font-size: 2.5rem; font-weight: 700; text-align: center; margin-bottom: 3rem;">
            Supported AI Platforms
        </h2>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4 style="font-weight: 700; margin-bottom: 0.75rem; color: #6366F1;">💬 Text & Chat AI</h4>
                    <p style="color: #6B7280; font-size: 0.875rem; line-height: 1.6;">
                        ChatGPT, Claude, Gemini, Perplexity, Pi, Character.AI, Replika, and more
                    </p>
                </div>

                <div>
                    <h4 style="font-weight: 700; margin-bottom: 0.75rem; color: #EC4899;">🎨 Image Generation</h4>
                    <p style="color: #6B7280; font-size: 0.875rem; line-height: 1.6;">
                        Midjourney, DALL-E, Stable Diffusion, Leonardo AI, Adobe Firefly, Ideogram
                    </p>
                </div>

                <div>
                    <h4 style="font-weight: 700; margin-bottom: 0.75rem; color: #10B981;">💻 Code Assistants</h4>
                    <p style="color: #6B7280; font-size: 0.875rem; line-height: 1.6;">
                        GitHub Copilot, Cursor, Tabnine, Amazon CodeWhisperer, Replit AI
                    </p>
                </div>

                <div>
                    <h4 style="font-weight: 700; margin-bottom: 0.75rem; color: #F59E0B;">🎬 Video & Audio</h4>
                    <p style="color: #6B7280; font-size: 0.875rem; line-height: 1.6;">
                        Suno AI, Runway, Pika Labs, ElevenLabs, Descript, and more
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact CTA -->
    <div style="text-align: center; padding: 3rem; background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); border-radius: 1rem; color: white;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem;">
            Join Our Community Today
        </h2>
        <p style="opacity: 0.9; margin-bottom: 2rem; font-size: 1.125rem;">
            Start sharing your best prompts, earn XP, and connect with AI enthusiasts worldwide!
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <?php if (!is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn" style="background: white; color: #667EEA;">
                    Sign Up Free
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url(home_url('/submit')); ?>" class="btn btn-secondary">
                Submit Prompt
            </a>
        </div>
    </div>

</div>

<?php get_footer(); ?>
