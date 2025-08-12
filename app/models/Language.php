<?php

class Language extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all languages from the database
     * @return array|false
     */
    public function getAllLanguages() {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}languages ORDER BY name ASC");
        return $this->db->resultSet();
    }
}
