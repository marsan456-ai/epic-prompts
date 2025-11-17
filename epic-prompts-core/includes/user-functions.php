<?php
/**
 * User Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

class EP_User_Functions {

    /**
     * Initialize user meta fields on registration
     */
    public static function init_user_meta($user_id) {
        // XP and level
        update_user_meta($user_id, 'xp_total', 0);
        update_user_meta($user_id, 'xp_monthly', 0);
        update_user_meta($user_id, 'level', 1);

        // Reputation and coins
        update_user_meta($user_id, 'reputation', 0);
        update_user_meta($user_id, 'coins', 50); // Welcome bonus
        update_user_meta($user_id, 'voting_power', 1.0);

        // Badges and titles
        update_user_meta($user_id, 'badges', array());
        update_user_meta($user_id, 'titles', array());
        update_user_meta($user_id, 'current_title', 'Novice Prompter');

        // Activity tracking
        update_user_meta($user_id, 'streak_days', 0);
        update_user_meta($user_id, 'last_active_date', current_time('Y-m-d'));

        // Stats
        update_user_meta($user_id, 'prompts_submitted', 0);
        update_user_meta($user_id, 'prompts_verified', 0);
        update_user_meta($user_id, 'votes_cast', 0);
        update_user_meta($user_id, 'reviews_written', 0);
        update_user_meta($user_id, 'referrals', array());
    }

    /**
     * Update last active date and streak
     */
    public static function update_last_active($user_id) {
        $today = current_time('Y-m-d');
        $last_active = get_user_meta($user_id, 'last_active_date', true);

        if (empty($last_active)) {
            update_user_meta($user_id, 'last_active_date', $today);
            update_user_meta($user_id, 'streak_days', 1);
            return;
        }

        $last_date = new DateTime($last_active);
        $current_date = new DateTime($today);
        $diff = $last_date->diff($current_date)->days;

        if ($diff == 1) {
            // Consecutive day - increment streak
            $streak = (int) get_user_meta($user_id, 'streak_days', true);
            update_user_meta($user_id, 'streak_days', $streak + 1);
        } elseif ($diff > 1) {
            // Streak broken - reset
            update_user_meta($user_id, 'streak_days', 1);
        }
        // If diff == 0, same day, do nothing

        update_user_meta($user_id, 'last_active_date', $today);
    }

    /**
     * Get user level from XP
     */
    public static function get_user_level($user_id) {
        $xp_total = (int) get_user_meta($user_id, 'xp_total', true);
        return self::calculate_level($xp_total);
    }

    /**
     * Calculate level from XP
     */
    public static function calculate_level($xp_total) {
        // Level = floor(XP / 100) + 1
        // Level 1: 0-99 XP
        // Level 2: 100-199 XP
        // etc.
        return floor($xp_total / 100) + 1;
    }

    /**
     * Get XP needed for next level
     */
    public static function get_xp_for_next_level($user_id) {
        $current_xp = (int) get_user_meta($user_id, 'xp_total', true);
        $current_level = self::get_user_level($user_id);
        $next_level_xp = ($current_level) * 100;

        return array(
            'current' => $current_xp,
            'needed' => $next_level_xp,
            'remaining' => $next_level_xp - $current_xp,
            'percentage' => ($current_xp % 100),
        );
    }

    /**
     * Get user title based on level
     */
    public static function get_user_title($user_id) {
        $custom_title = get_user_meta($user_id, 'current_title', true);

        if (!empty($custom_title)) {
            return $custom_title;
        }

        $level = self::get_user_level($user_id);

        if ($level >= 26) {
            return 'Legendary Prompter';
        } elseif ($level >= 21) {
            return 'Prompt Architect';
        } elseif ($level >= 16) {
            return 'Master Prompter';
        } elseif ($level >= 11) {
            return 'Expert Prompter';
        } elseif ($level >= 6) {
            return 'Skilled Prompter';
        } else {
            return 'Novice Prompter';
        }
    }

    /**
     * Calculate voting power
     */
    public static function calculate_voting_power($user_id) {
        $level = self::get_user_level($user_id);
        $base_power = 1.0;

        // Level multiplier
        if ($level >= 26) {
            $base_power = 3.0;
        } elseif ($level >= 21) {
            $base_power = 2.5;
        } elseif ($level >= 16) {
            $base_power = 2.0;
        } elseif ($level >= 11) {
            $base_power = 1.5;
        } elseif ($level >= 6) {
            $base_power = 1.3;
        }

        return $base_power;
    }

    /**
     * Increment user stat
     */
    public static function increment_stat($user_id, $stat_name, $amount = 1) {
        $current = (int) get_user_meta($user_id, $stat_name, true);
        update_user_meta($user_id, $stat_name, $current + $amount);
    }

    /**
     * Award badge to user
     */
    public static function award_badge($user_id, $badge_id, $badge_name) {
        $badges = get_user_meta($user_id, 'badges', true);

        if (!is_array($badges)) {
            $badges = array();
        }

        // Check if already has badge
        if (isset($badges[$badge_id])) {
            return false;
        }

        $badges[$badge_id] = array(
            'name' => $badge_name,
            'date' => current_time('mysql'),
        );

        update_user_meta($user_id, 'badges', $badges);

        // Trigger action for badge unlock notification
        do_action('epic_prompts_badge_unlocked', $user_id, $badge_id, $badge_name);

        return true;
    }

    /**
     * Get user badges
     */
    public static function get_user_badges($user_id) {
        $badges = get_user_meta($user_id, 'badges', true);

        if (!is_array($badges)) {
            return array();
        }

        return $badges;
    }

    /**
     * Get user stats summary
     */
    public static function get_user_stats($user_id) {
        return array(
            'xp_total' => (int) get_user_meta($user_id, 'xp_total', true),
            'level' => self::get_user_level($user_id),
            'title' => self::get_user_title($user_id),
            'reputation' => (int) get_user_meta($user_id, 'reputation', true),
            'coins' => (int) get_user_meta($user_id, 'coins', true),
            'voting_power' => self::calculate_voting_power($user_id),
            'streak_days' => (int) get_user_meta($user_id, 'streak_days', true),
            'prompts_submitted' => (int) get_user_meta($user_id, 'prompts_submitted', true),
            'prompts_verified' => (int) get_user_meta($user_id, 'prompts_verified', true),
            'votes_cast' => (int) get_user_meta($user_id, 'votes_cast', true),
            'reviews_written' => (int) get_user_meta($user_id, 'reviews_written', true),
            'badges' => self::get_user_badges($user_id),
        );
    }
}
