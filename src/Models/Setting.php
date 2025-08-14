<?php

namespace App\Models;

use App\Core\Database;

class Setting
{
    /**
     * Get all settings from the database.
     */
    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "SELECT * FROM {$prefix}settings";
        $result = $db->query($sql);
        $settings_raw = $result->fetch_all(MYSQLI_ASSOC);

        // Return as a key => value array
        $settings = [];
        foreach ($settings_raw as $setting) {
            $settings[$setting['name']] = $setting['value'];
        }
        return $settings;
    }

    /**
     * Update a batch of settings.
     */
    public static function updateBatch($data)
    {
        $db = Database::getInstance()->getConnection();
        $prefix = DB_PREFIX;
        $sql = "INSERT INTO {$prefix}settings (name, value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)";
        $stmt = $db->prepare($sql);

        foreach ($data as $name => $value) {
            $stmt->bind_param('ss', $name, $value);
            $stmt->execute();
        }
        return true;
    }
}
