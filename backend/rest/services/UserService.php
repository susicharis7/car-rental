<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService {
    protected $dao;

    public function __construct() {
        parent::__construct(new UserDao());
    }

    // Active ~ Inactive
    public function getActive(): array {
        $users = $this->dao->find_active();
        foreach ($users as &$u) unset($u['password_hash']);
        return $users;
    }

    public function getInactive(): array {
        $users = $this->dao->find_inactive();
        foreach ($users as &$u) unset($u['password_hash']);
        return $users;
    }


    // Get All Users
    public function getAll(): array {
        $users = $this->dao->getAll();
        foreach ($users as &$u) {
            unset($u['password_hash']); 
        }
        return $users;
    }



    // find_by_name
    public function searchByName(string $name): array {
        if (trim($name) === '') throw new Exception("Search term CANNOT be empty.");
        return $this->dao->find_by_name($name);
    }

    // find_by_email
    public function searchByEmail(string $email): array {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email format");
        $user = $this->dao->find_by_email($email);
        if (!$user) throw new Exception("User with email: '$email' was not found.");
        return $user;
    }

    // Activate ~ Deactivate
    public function activate(int $userId): array {
        return $this->dao->set_active($userId, true);
    }

    public function deactivate(int $userId): array {
        return $this->dao->set_active($userId, false);
    }

    // Create User
    public function createUser(array $data): array {
        
        $this->requiredFields($data, ['first_name', 'last_name', 'email', 'password']);

        $data['email'] = trim($data['email']);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        if ($this->dao->find_by_email($data['email'])) {
            throw new Exception("Email already in use.");
        }

        // hash password => save it in password_hash
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']); // ne snimaj plain password u DB

        return $this->dao->create($data);
    }

    // Update User
    public function updateUser(int $id, array $data): array {
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email format.");
            $existing = $this->dao->find_by_email($data['email']);
            if ($existing && (int)$existing['id'] !== $id) throw new Exception("Email already in use by another user.");
        }

        // if user sends new password => hash it 
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->dao->update($data, $id);
    }

    // Update Password Only 


    // Login Check
    public function login(string $email, string $password): array {
        $user = $this->searchByEmail($email);

        if (!$user || !isset($user['password_hash'])) {
            throw new Exception("User not found or no password set.");
        }


        if (!password_verify($password, $user['password_hash'])) {
            throw new Exception("Invalid credentials.");
        }

        if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
            throw new Exception("Account is deactivated.");
        }

        unset($user['password_hash']); // we never return hash 
        return $user;
    }
}
