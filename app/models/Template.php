<?php

class Template extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the active template from the database
     * @return array|false|null
     */
    public function getActiveTemplate() {
        $prefix = $this->db->getPrefix();
        // Note: The base Model and Database classes need to support this kind of query.
        // My mysqli wrapper's single() method should work here.
        $this->db->query("SELECT folder_name FROM {$prefix}templates WHERE is_active = 1 LIMIT 1");
        return $this->db->single();
    }

    /**
     * Get all templates from the database
     * @return array|false
     */
    public function getAllTemplates() {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}templates ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Set a template to be active
     * @param int $id The ID of the template to activate
     * @return bool
     */
    public function setActiveTemplate($id) {
        $prefix = $this->db->getPrefix();
        // First, deactivate all templates
        $this->db->query("UPDATE {$prefix}templates SET is_active = 0");
        if (!$this->db->execute()) {
            return false;
        }

        // Then, activate the new one
        $this->db->query("UPDATE {$prefix}templates SET is_active = 1 WHERE id = ?");
        return $this->db->execute([$id]);
    }

    /**
     * Scan the templates directory and add new templates to the database.
     */
    public function sync_templates() {
        $prefix = $this->db->getPrefix();
        $templates_dir = __DIR__ . '/../../templates/';

        // Get templates from filesystem
        $fs_templates = array_filter(scandir($templates_dir), function ($item) use ($templates_dir) {
            return is_dir($templates_dir . $item) && !in_array($item, ['.', '..']);
        });

        // Get templates from database
        $this->db->query("SELECT folder_name FROM {$prefix}templates");
        $db_templates_raw = $this->db->resultSet();
        $db_templates = array_column($db_templates_raw, 'folder_name');

        // Find new templates and add them
        $new_templates = array_diff($fs_templates, $db_templates);

        if (!empty($new_templates)) {
            $this->db->query("INSERT INTO {$prefix}templates (name, folder_name, is_active) VALUES (?, ?, 0)");
            foreach ($new_templates as $template_folder) {
                // Use the folder name as the default name, capitalizing it for display
                $template_name = ucwords(str_replace('-', ' ', $template_folder));
                $this->db->execute([$template_name, $template_folder]);
            }
        }
    }
}
