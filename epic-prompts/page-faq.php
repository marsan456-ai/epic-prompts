<?php
/**
 * Template Name: FAQ
 * Description: Frequently Asked Questions
 */

get_header();
?>

<div style="background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); padding: 4rem 0; color: white;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">
            Frequently Asked Questions
        </h1>
        <p style="font-size: 1.25rem; opacity: 0.9;">
            Everything you need to know about Epic Prompts
        </p>
    </div>
</div>

<div class="container" style="padding: 4rem 0; max-width: 900px;">

    <!-- FAQ Items -->
    <div class="faq-container">

        <!-- General Questions -->
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem; color: #111827;">
            General Questions
        </h2>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>What is Epic Prompts?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Epic Prompts is a gamified community platform where users share, discover, and rate AI prompts for 50+ AI platforms including ChatGPT, Claude, Midjourney, Suno AI, and more. We support all prompt types: text, image, code, video, audio, and music generation.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>Is Epic Prompts free to use?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Yes! Epic Prompts is 100% free to use. You can browse, submit, rate, and react to prompts without any subscription or payment. All features are available to all users.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>Which AI platforms are supported?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    We support 50+ AI platforms including:<br>
                    <strong>Text/Chat:</strong> ChatGPT, Claude, Gemini, Perplexity, Pi, Llama, Mistral<br>
                    <strong>Image:</strong> Midjourney, DALL-E, Stable Diffusion, Leonardo AI, Ideogram<br>
                    <strong>Code:</strong> GitHub Copilot, Cursor, Tabnine, Amazon CodeWhisperer<br>
                    <strong>Video/Audio:</strong> Suno AI, Runway, Pika Labs, ElevenLabs, Descript<br>
                    ...and many more!
                </p>
            </div>
        </div>

        <!-- Account & Profile -->
        <h2 style="font-size: 2rem; font-weight: 700; margin: 3rem 0 2rem; color: #111827;">
            Account & Profile
        </h2>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>How do I create an account?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Click "Sign Up" in the navigation menu and provide your email, username, and password. It takes less than 30 seconds! After registration, you'll start at Level 1 (Novice) with 0 XP.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>Can I change my username or avatar?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Yes! Go to your profile page and click "Edit Profile". You can update your avatar, bio, username (if allowed), and other profile information.
                </p>
            </div>
        </div>

        <!-- XP & Leveling -->
        <h2 style="font-size: 2rem; font-weight: 700; margin: 3rem 0 2rem; color: #111827;">
            XP & Leveling System
        </h2>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>How do I earn XP?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    You can earn XP through several actions:<br>
                    • Submit a prompt: +10 XP<br>
                    • Your prompt gets verified: +5 XP<br>
                    • Daily login: +2 XP<br>
                    • Rate a prompt: +1 XP<br>
                    • React to a prompt: +1 XP
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>What are the different levels?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    There are 10 levels:<br>
                    Level 1: Novice (0-99 XP)<br>
                    Level 2: Apprentice (100-249 XP)<br>
                    Level 3: Skilled (250-499 XP)<br>
                    Level 4: Expert (500-999 XP)<br>
                    Level 5: Master (1,000-1,999 XP)<br>
                    Level 6: Grandmaster (2,000-3,999 XP)<br>
                    Level 7: Champion (4,000-6,499 XP)<br>
                    Level 8: Hero (6,500-8,999 XP)<br>
                    Level 9: Legend (9,000-9,999 XP)<br>
                    Level 10: Mythical (10,000+ XP)
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>What do I get for leveling up?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Each level up rewards you with coins, badges, and special profile badges. Higher levels also give you more visibility in the community and access to exclusive features (coming soon).
                </p>
            </div>
        </div>

        <!-- Prompts -->
        <h2 style="font-size: 2rem; font-weight: 700; margin: 3rem 0 2rem; color: #111827;">
            Submitting & Managing Prompts
        </h2>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>How do I submit a prompt?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Click "Submit Prompt" in the navigation. Fill in the prompt title, text, select the AI platform, prompt type, and category. You can optionally upload a screenshot of the result. Submit and earn +10 XP!
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>Do I need to upload an image with my prompt?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    No, images are optional! For text or code prompts, an image isn't necessary. For image/video generation prompts, it's helpful to show the result but still optional.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>Can I edit or delete my prompts?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Yes! Go to your profile, find the prompt, and click "Edit" or "Delete". You can update the title, text, category, or image at any time.
                </p>
            </div>
        </div>

        <!-- Community -->
        <h2 style="font-size: 2rem; font-weight: 700; margin: 3rem 0 2rem; color: #111827;">
            Community & Interaction
        </h2>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>What are reactions and how do they work?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    Reactions are emoji responses to prompts. We have 5 reactions:<br>
                    🔥 Fire - Amazing prompt!<br>
                    💎 Gem - Valuable and rare<br>
                    ✨ Creative - Unique and creative<br>
                    🚀 Rocket - Fast and effective<br>
                    🤯 Mind Blown - Absolutely incredible<br><br>
                    Click a reaction to show your appreciation. Each reaction earns you +1 XP.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>What does "Verified" mean?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    When users mark a prompt as "Verified", it means they tested it and it worked perfectly. High verification counts indicate reliable, quality prompts. The prompt author earns +5 XP for each verification.
                </p>
            </div>
        </div>

        <div class="faq-item" style="background: white; border-radius: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="faq-question" style="padding: 1.5rem; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; align-items: center;" onclick="toggleFaq(this)">
                <span>How does the leaderboard work?</span>
                <span class="faq-icon" style="font-size: 1.5rem; transition: transform 0.3s;">+</span>
            </div>
            <div class="faq-answer" style="padding: 0 1.5rem 0 1.5rem; max-height: 0; overflow: hidden; transition: all 0.3s;">
                <p style="padding-bottom: 1.5rem; color: #6B7280; line-height: 1.6;">
                    The leaderboard ranks users by total XP. There are two views: All-Time (total XP ever) and Monthly (XP earned this month). Compete with the community and climb to the top!
                </p>
            </div>
        </div>

    </div>

    <!-- Still Have Questions -->
    <div style="margin-top: 4rem; text-align: center; padding: 3rem; background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); border-radius: 1rem; color: white;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem;">
            Still Have Questions?
        </h2>
        <p style="opacity: 0.9; margin-bottom: 2rem;">
            Can't find the answer you're looking for? Check out our detailed How It Works page.
        </p>
        <a href="<?php echo esc_url(home_url('/how-it-works')); ?>" class="btn" style="background: white; color: #667EEA;">
            How It Works
        </a>
    </div>

</div>

<script>
function toggleFaq(element) {
    const faqItem = element.parentElement;
    const answer = faqItem.querySelector('.faq-answer');
    const icon = element.querySelector('.faq-icon');
    const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

    // Close all other FAQs
    document.querySelectorAll('.faq-answer').forEach(item => {
        item.style.maxHeight = '0';
    });
    document.querySelectorAll('.faq-icon').forEach(item => {
        item.textContent = '+';
        item.style.transform = 'rotate(0deg)';
    });

    // Toggle current FAQ
    if (!isOpen) {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.textContent = '−';
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>

<?php get_footer(); ?>
