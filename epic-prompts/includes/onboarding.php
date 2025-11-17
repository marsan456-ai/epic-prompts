<?php
/**
 * Onboarding & Tutorial System
 * Guides new users through platform features
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if user needs onboarding
 */
function epic_prompts_show_onboarding() {
    if (!is_user_logged_in()) {
        return false;
    }

    $user_id = get_current_user_id();
    $onboarding_completed = get_user_meta($user_id, 'ep_onboarding_completed', true);

    // Show onboarding if not completed and user is new (less than 7 days)
    $user_data = get_userdata($user_id);
    $registration_date = strtotime($user_data->user_registered);
    $days_since_registration = (time() - $registration_date) / (60 * 60 * 24);

    return !$onboarding_completed && $days_since_registration < 7;
}

/**
 * Display onboarding overlay
 */
function epic_prompts_onboarding_overlay() {
    if (!epic_prompts_show_onboarding()) {
        return;
    }

    $user_name = wp_get_current_user()->display_name;
    ?>
    <div id="ep-onboarding-overlay" style="display: none;">
        <div class="onboarding-backdrop" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); z-index: 99999; backdrop-filter: blur(4px);"></div>

        <div class="onboarding-modal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 1rem; max-width: 600px; width: 90%; z-index: 100000; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

            <!-- Step 1: Welcome -->
            <div class="onboarding-step" data-step="1">
                <div style="padding: 3rem 2rem; text-align: center;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">👋</div>
                    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">
                        Welcome to Epic Prompts, <?php echo esc_html($user_name); ?>!
                    </h2>
                    <p style="color: #6B7280; font-size: 1.125rem; line-height: 1.6; margin-bottom: 2rem;">
                        Let's take a quick tour to help you get started with sharing and discovering amazing AI prompts!
                    </p>
                    <button onclick="epNextStep()" class="btn btn-primary" style="width: 100%;">
                        Let's Go! 🚀
                    </button>
                </div>
            </div>

            <!-- Step 2: Submit Prompts -->
            <div class="onboarding-step" data-step="2" style="display: none;">
                <div style="padding: 3rem 2rem;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📝</div>
                        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">
                            Share Your Best Prompts
                        </h2>
                    </div>
                    <div style="background: #F9FAFB; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: start;">
                                <span style="font-size: 1.5rem; flex-shrink: 0;">✅</span>
                                <span style="color: #374151;">Click <strong>"Submit Prompt"</strong> in the navigation menu</span>
                            </li>
                            <li style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: start;">
                                <span style="font-size: 1.5rem; flex-shrink: 0;">✅</span>
                                <span style="color: #374151;">Enter your prompt text and choose the AI platform</span>
                            </li>
                            <li style="display: flex; gap: 1rem; align-items: start;">
                                <span style="font-size: 1.5rem; flex-shrink: 0;">✅</span>
                                <span style="color: #374151;">Optionally add a screenshot to show the result</span>
                            </li>
                        </ul>
                    </div>
                    <div style="background: #EEF2FF; padding: 1rem; border-radius: 0.5rem; text-align: center; margin-bottom: 2rem;">
                        <strong style="color: #6366F1;">Earn +10 XP</strong> for each prompt you submit!
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <button onclick="epPrevStep()" class="btn btn-outline" style="flex: 1;">Back</button>
                        <button onclick="epNextStep()" class="btn btn-primary" style="flex: 2;">Next</button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Earn XP -->
            <div class="onboarding-step" data-step="3" style="display: none;">
                <div style="padding: 3rem 2rem;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">⭐</div>
                        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">
                            Earn XP & Level Up
                        </h2>
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #F9FAFB; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="color: #374151;">Submit a Prompt</span>
                            <span style="background: #6366F1; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600; font-size: 0.875rem;">+10 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #F9FAFB; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="color: #374151;">Get Verified</span>
                            <span style="background: #10B981; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600; font-size: 0.875rem;">+5 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #F9FAFB; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="color: #374151;">Daily Login</span>
                            <span style="background: #F59E0B; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600; font-size: 0.875rem;">+2 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #F9FAFB; border-radius: 0.5rem;">
                            <span style="color: #374151;">React/Rate Prompts</span>
                            <span style="background: #EC4899; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600; font-size: 0.875rem;">+1 XP</span>
                        </div>
                    </div>
                    <p style="color: #6B7280; text-align: center; margin-bottom: 2rem;">
                        Climb from <strong>Level 1 (Novice)</strong> to <strong>Level 10 (Mythical)</strong> and compete on the leaderboard!
                    </p>
                    <div style="display: flex; gap: 0.75rem;">
                        <button onclick="epPrevStep()" class="btn btn-outline" style="flex: 1;">Back</button>
                        <button onclick="epNextStep()" class="btn btn-primary" style="flex: 2;">Next</button>
                    </div>
                </div>
            </div>

            <!-- Step 4: Reactions & Community -->
            <div class="onboarding-step" data-step="4" style="display: none;">
                <div style="padding: 3rem 2rem;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">
                            Interact with the Community
                        </h2>
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <p style="color: #6B7280; margin-bottom: 1.5rem;">
                            Show appreciation with <strong>5 reaction emojis</strong>:
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div style="background: #FEF2F2; padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔥</div>
                                <div style="font-weight: 600; color: #991B1B; font-size: 0.875rem;">Fire</div>
                            </div>
                            <div style="background: #F0F9FF; padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💎</div>
                                <div style="font-weight: 600; color: #075985; font-size: 0.875rem;">Gem</div>
                            </div>
                            <div style="background: #FEF9C3; padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✨</div>
                                <div style="font-weight: 600; color: #854D0E; font-size: 0.875rem;">Creative</div>
                            </div>
                            <div style="background: #F0FDF4; padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🚀</div>
                                <div style="font-weight: 600; color: #14532D; font-size: 0.875rem;">Rocket</div>
                            </div>
                        </div>
                    </div>
                    <p style="color: #6B7280; text-align: center; margin-bottom: 2rem;">
                        Plus: <strong>Rate prompts</strong> (1-5 stars) and mark them as <strong>"Verified"</strong> when they work!
                    </p>
                    <div style="display: flex; gap: 0.75rem;">
                        <button onclick="epPrevStep()" class="btn btn-outline" style="flex: 1;">Back</button>
                        <button onclick="epCompleteOnboarding()" class="btn btn-primary" style="flex: 2;">Get Started!</button>
                    </div>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div style="padding: 0 2rem 2rem; display: flex; justify-content: center; gap: 0.5rem;">
                <span class="progress-dot" data-dot="1" style="width: 12px; height: 12px; border-radius: 50%; background: #6366F1; transition: all 0.3s;"></span>
                <span class="progress-dot" data-dot="2" style="width: 12px; height: 12px; border-radius: 50%; background: #E5E7EB; transition: all 0.3s;"></span>
                <span class="progress-dot" data-dot="3" style="width: 12px; height: 12px; border-radius: 50%; background: #E5E7EB; transition: all 0.3s;"></span>
                <span class="progress-dot" data-dot="4" style="width: 12px; height: 12px; border-radius: 50%; background: #E5E7EB; transition: all 0.3s;"></span>
            </div>

        </div>
    </div>

    <script>
    let currentStep = 1;
    const totalSteps = 4;

    // Show onboarding on page load
    jQuery(document).ready(function($) {
        $('#ep-onboarding-overlay').fadeIn(300);
    });

    function epNextStep() {
        if (currentStep < totalSteps) {
            jQuery('.onboarding-step[data-step="' + currentStep + '"]').fadeOut(200, function() {
                currentStep++;
                jQuery('.onboarding-step[data-step="' + currentStep + '"]').fadeIn(200);
                epUpdateProgress();
            });
        }
    }

    function epPrevStep() {
        if (currentStep > 1) {
            jQuery('.onboarding-step[data-step="' + currentStep + '"]').fadeOut(200, function() {
                currentStep--;
                jQuery('.onboarding-step[data-step="' + currentStep + '"]').fadeIn(200);
                epUpdateProgress();
            });
        }
    }

    function epUpdateProgress() {
        jQuery('.progress-dot').each(function() {
            const dotNum = parseInt(jQuery(this).data('dot'));
            if (dotNum === currentStep) {
                jQuery(this).css('background', '#6366F1');
                jQuery(this).css('width', '16px');
            } else {
                jQuery(this).css('background', '#E5E7EB');
                jQuery(this).css('width', '12px');
            }
        });
    }

    function epCompleteOnboarding() {
        jQuery.ajax({
            url: epicPromptsTheme.ajaxurl,
            type: 'POST',
            data: {
                action: 'ep_complete_onboarding',
                nonce: epicPromptsTheme.nonce,
            },
            success: function(response) {
                jQuery('#ep-onboarding-overlay').fadeOut(300);

                // Show success toast
                const toast = jQuery('<div class="toast-notification success" style="position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem; background: white; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-left: 4px solid #10B981; z-index: 99999;">' +
                    '<strong>Welcome aboard! 🎉</strong><br>' +
                    'You earned +5 XP for completing the tutorial!' +
                    '</div>');
                jQuery('body').append(toast);
                setTimeout(function() {
                    toast.fadeOut(300, function() {
                        jQuery(this).remove();
                    });
                }, 4000);
            }
        });
    }

    // Allow closing with Escape key
    jQuery(document).on('keydown', function(e) {
        if (e.key === 'Escape' && jQuery('#ep-onboarding-overlay').is(':visible')) {
            if (confirm('Are you sure you want to skip the tutorial?')) {
                epCompleteOnboarding();
            }
        }
    });
    </script>

    <style>
    .onboarding-modal {
        animation: slideUpFade 0.4s ease-out;
    }

    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translate(-50%, -40%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    @media (max-width: 640px) {
        .onboarding-modal {
            width: 95%;
        }
        .onboarding-step {
            padding: 2rem 1rem !important;
        }
    }
    </style>
    <?php
}
add_action('wp_footer', 'epic_prompts_onboarding_overlay');

/**
 * AJAX handler to complete onboarding
 */
function epic_prompts_complete_onboarding_ajax() {
    check_ajax_referer('epic_prompts_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Not logged in'));
    }

    $user_id = get_current_user_id();
    update_user_meta($user_id, 'ep_onboarding_completed', true);

    // Award bonus XP for completing onboarding
    EP_XP_System::award_xp($user_id, 5, 'Completed onboarding tutorial');

    wp_send_json_success(array(
        'message' => 'Onboarding completed',
    ));
}
add_action('wp_ajax_ep_complete_onboarding', 'epic_prompts_complete_onboarding_ajax');
