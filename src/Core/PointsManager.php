<?php

namespace App\Core;

use App\Models\Setting;
use App\Models\User;

class PointsManager
{
    protected static $settings = [];

    /**
     * Initialize the points manager and register listeners.
     */
    public static function init()
    {
        self::$settings = Setting::getAll();

        Hooks::listen('user_created_content', [self::class, 'awardPoints']);
    }

    /**
     * Award points to a user for a specific action.
     *
     * @param int $user_id
     * @param string $action_type e.g., 'new_topic', 'new_post'
     */
    public static function awardPoints($user_id, $action_type)
    {
        $points_to_award = 0;

        if ($action_type === 'new_topic') {
            $points_to_award = (int)(self::$settings['points_for_new_topic'] ?? 0);
        } elseif ($action_type === 'new_post') {
            $points_to_award = (int)(self::$settings['points_for_new_post'] ?? 0);
        }

        if ($points_to_award > 0) {
            User::addPoints($user_id, $points_to_award);
        }
    }
}
