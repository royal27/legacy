<?php

class Role extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get a role by its ID
     * @param int $id
     * @return array|false|null
     */
    public function getRoleById($id) {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}roles WHERE id = ?");
        return $this->db->single([$id]);
    }

    /**
     * Get all roles
     * @return array|false
     */
    public function getAllRoles() {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}roles ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Create a new role
     * @param string $name
     * @param array $permissions
     * @return bool
     */
    public function createRole($name, $permissions = []) {
        $prefix = $this->db->getPrefix();
        $permissionsJson = json_encode($permissions);
        $this->db->query("INSERT INTO {$prefix}roles (name, permissions) VALUES (?, ?)");
        return $this->db->execute([$name, $permissionsJson]);
    }

    /**
     * Update an existing role
     * @param int $id
     * @param string $name
     * @param array $permissions
     * @return bool
     */
    public function updateRole($id, $name, $permissions) {
        $prefix = $this->db->getPrefix();
        $permissionsJson = json_encode($permissions);
        $this->db->query("UPDATE {$prefix}roles SET name = ?, permissions = ? WHERE id = ?");
        return $this->db->execute([$name, $permissionsJson, $id]);
    }

    /**
     * Delete a role
     * @param int $id
     * @return bool
     */
    public function deleteRole($id) {
        // Prevent deleting the Founder or Member roles
        if ($id <= 2) {
            return false;
        }
        $prefix = $this->db->getPrefix();
        $this->db->query("DELETE FROM {$prefix}roles WHERE id = ?");
        return $this->db->execute([$id]);
    }
}
