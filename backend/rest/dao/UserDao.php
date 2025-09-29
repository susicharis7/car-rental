<?php
require_once __DIR__ . '/BaseDao.php';


class UserDao extends BaseDao {
    protected string $table_name;

    public function __construct() {
        $this->table_name = 'users';
        parent::__construct($this->table_name);
    }


    // Find by email OR null if we don't have him
    public function find_by_email(string $email): ?array {
        return $this->query_unique(
            "SELECT * FROM `{$this->table_name}` WHERE email = :email LIMIT 1", [':email' => $email]
        ) ?: null;
    }

    // Find by name/surname (in concat also) 
    public function find_by_name(string $q): array {
        $sql = "SELECT *
                FROM `{$this->table_name}`
                WHERE first_name LIKE CONCAT('%', :q, '%')
                OR last_name  LIKE CONCAT('%', :q, '%')
                OR CONCAT(first_name, ' ', last_name) LIKE CONCAT('%', :q, '%')
                ORDER BY id DESC";
        return $this->query($sql, [':q' => $q]);
    }   
                

    // Find ALL Active
    public function find_active(): array {
        return $this->query(
            "SELECT * FROM `{$this->table_name}` WHERE is_active = 1 ORDER BY id DESC"
        );
    }

    // Find ALL Inactive
    public function find_inactive(): array {
        return $this->query(
            "SELECT * FROM `{$this->table_name}` WHERE is_active = 0 ORDER BY id DESC"
        );
    }

    // Activate/Inactivate user
    public function set_active(int $userId, bool $active): array {
        return $this->update(['is_active' => $active ? 1 : 0], $userId);
    }

    // Create User (if role is not sent - default is 'user')
    public function create(array $user): array {
        if (!isset($user['role'])) $user['role'] = 'user';
        if (!isset($user['is_active'])) $user['is_active'] = 1;
        return $this->add($user);
    }

    



}

?>