<?php

class User extends Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Find user by email
     * @param string $email
     * @return array|false|null
     */
    public function findUserByEmail($email) {
        $prefix = $this->db->getPrefix();
        $this->db->query("SELECT * FROM {$prefix}users WHERE email = ?");
        return $this->db->single([$email]);
    }

    /**
     * Find user by ID and join their role information
     * @param int $id
     * @return array|false|null
     */
    public function findUserById($id) {
        $prefix = $this->db->getPrefix();
        $this->db->query("
            SELECT u.*, r.name as role_name, r.permissions
            FROM {$prefix}users u
            JOIN {$prefix}roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        return $this->db->single([$id]);
    }

    /**
     * Register a new user
     * @param array $data
     * @return bool
     */
    public function register($data) {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}users (username, email, password, role_id, created_at) VALUES (?, ?, ?, ?, NOW())");

        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        // Default role is 'Member' (ID 2)
        $role_id = $data['role_id'] ?? 2;

        return $this->db->execute([$data['username'], $data['email'], $hashed_password, $role_id]);
    }

    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return array|false The user data or false if login fails
     */
    public function login($email, $password) {
        $row = $this->findUserByEmail($email);

        if ($row) {
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }
}
