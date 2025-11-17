<?php
/**
 * XP System
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_XP_System {

    /**
     * XP Award amounts
     */
    const XP_SUBMIT_PROMPT = 10;
    const XP_VOTE_REACT = 2;
    const XP_COMMENT = 5;
    const XP_RECEIVED_REACTION = 5;
    const XP_DAILY_LOGIN = 5;
    const XP_PROMPT_VERIFIED = 50;
    const XP_VERIFY_PROMPT = 10;
    const XP_DETAILED_REVIEW = 10;

    /**
     * Award XP to user
     */
    public static function award_xp($user_id, $xp_amount, $reason = '') {
        // Get current XP
        $current_xp = (int) get_user_meta($user_id, 'xp_total', true);
        $current_monthly_xp = (int) get_user_meta($user_id, 'xp_monthly', true);

        // Calculate old level
        $old_level = EP_User_Functions::calculate_level($current_xp);

        // Add XP
        $new_xp = $current_xp + $xp_amount;
        $new_monthly_xp = $current_monthly_xp + $xp_amount;

        // Update XP
        update_user_meta($user_id, 'xp_total', $new_xp);
        update_user_meta($user_id, 'xp_monthly', $new_monthly_xp);

        // Calculate new level
        $new_level = EP_User_Functions::calculate_level($new_xp);
        update_user_meta($user_id, 'level', $new_level);

        // Check for level up
        if ($new_level > $old_level) {
            self::handle_level_up($user_id, $new_level, $old_level);
        }

        // Log XP award (for analytics)
        do_action('epic_prompts_xp_awarded', $user_id, $xp_amount, $reason, $new_xp);

        return array(
            'awarded' => $xp_amount,
            'total' => $new_xp,
            'level' => $new_level,
            'leveled_up' => ($new_level > $old_level),
        );
    }

    /**
     * Handle level up
     */
    private static function handle_level_up($user_id, $new_level, $old_level) {
        // Award coins bonus
        $coins_bonus = $new_level * 10;
        $current_coins = (int) get_user_meta($user_id, 'coins', true);
        update_user_meta($user_id, 'coins', $current_coins + $coins_bonus);

        // Update title
        $new_title = EP_User_Functions::get_user_title($user_id);
        update_user_meta($user_id, 'current_title', $new_title);

        // Check for level-based badges
        self::check_level_badges($user_id, $new_level);

        // Trigger level up action
        do_action('epic_prompts_level_up', $user_id, $new_level, $old_level);
    }

    /**
     * Check and award level-based badges
     */
    private static function check_level_badges($user_id, $level) {
        $badges_to_award = array();

        if ($level >= 5) {
            $badges_to_award['level_5'] = 'Level 5 Achiever';
        }
        if ($level >= 10) {
            $badges_to_award['level_10'] = 'Double Digits';
        }
        if ($level >= 15) {
            $badges_to_award['level_15'] = 'Expert Territory';
        }
        if ($level >= 20) {
            $badges_to_award['level_20'] = 'Master Class';
        }
        if ($level >= 25) {
            $badges_to_award['level_25'] = 'Architect Status';
        }

        foreach ($badges_to_award as $badge_id => $badge_name) {
            EP_User_Functions::award_badge($user_id, $badge_id, $badge_name);
        }
    }

    /**
     * Award XP for submitting prompt
     */
    public static function award_submit_prompt_xp($user_id) {
        return self::award_xp($user_id, self::XP_SUBMIT_PROMPT, 'Submit prompt');
    }

    /**
     * Award XP for voting/reacting
     */
    public static function award_vote_xp($user_id) {
        return self::award_xp($user_id, self::XP_VOTE_REACT, 'Vote/React');
    }

    /**
     * Award XP for commenting
     */
    public static function award_comment_xp($user_id) {
        return self::award_xp($user_id, self::XP_COMMENT, 'Comment');
    }

    /**
     * Award XP for receiving reaction
     */
    public static function award_received_reaction_xp($user_id) {
        return self::award_xp($user_id, self::XP_RECEIVED_REACTION, 'Received reaction');
    }

    /**
     * Award XP for daily login
     */
    public static function award_daily_login_xp($user_id) {
        // Check if already awarded today
        $last_login_xp = get_user_meta($user_id, 'last_login_xp_date', true);
        $today = current_time('Y-m-d');

        if ($last_login_xp === $today) {
            return false; // Already awarded today
        }

        update_user_meta($user_id, 'last_login_xp_date', $today);
        return self::award_xp($user_id, self::XP_DAILY_LOGIN, 'Daily login');
    }

    /**
     * Award XP for verified prompt
     */
    public static function award_verified_prompt_xp($user_id) {
        return self::award_xp($user_id, self::XP_PROMPT_VERIFIED, 'Prompt verified');
    }

    /**
     * Award XP for verifying a prompt
     */
    public static function award_verify_action_xp($user_id) {
        return self::award_xp($user_id, self::XP_VERIFY_PROMPT, 'Verified prompt');
    }

    /**
     * Award XP for detailed review
     */
    public static function award_review_xp($user_id) {
        return self::award_xp($user_id, self::XP_DETAILED_REVIEW, 'Detailed review');
    }

    /**
     * Reset monthly XP for all users (run via cron)
     */
    public static function reset_monthly_xp() {
        global $wpdb;

        $wpdb->query("
            UPDATE {$wpdb->usermeta}
            SET meta_value = 0
            WHERE meta_key = 'xp_monthly'
        ");

        do_action('epic_prompts_monthly_xp_reset');
    }

    /**
     * Get top users by XP
     */
    public static function get_leaderboard($limit = 20, $type = 'total') {
        $meta_key = ($type === 'monthly') ? 'xp_monthly' : 'xp_total';

        $args = array(
            'number' => $limit,
            'meta_key' => $meta_key,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'fields' => 'all',
        );

        $users = get_users($args);
        $leaderboard = array();

        $rank = 1;
        foreach ($users as $user) {
            $leaderboard[] = array(
                'rank' => $rank++,
                'user_id' => $user->ID,
                'username' => $user->display_name,
                'xp' => (int) get_user_meta($user->ID, $meta_key, true),
                'level' => EP_User_Functions::get_user_level($user->ID),
                'title' => EP_User_Functions::get_user_title($user->ID),
                'avatar' => get_avatar_url($user->ID),
            );
        }

        return $leaderboard;
    }
}
