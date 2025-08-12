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
}
