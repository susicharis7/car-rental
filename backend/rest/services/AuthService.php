<?php 
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
use Firebase\JWT\JWT;
use Firebase\JWT\key;


class AuthService extends BaseService {
    private $auth_dao;
    public function __construct() {
        $this->auth_dao = new AuthDao();
        parent::__construct(new AuthDao);
    }




    public function getUserByEmail($email) {
        return $this->auth_dao->get_user_by_email($email);
    }


    

    public function register($entity) {
        if(empty($entity['email']) || empty($entity['password'])) return ['success' => false, 'error' => 'Email & password are required.'];


        $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
        if ($email_exists) return ['success' => false, 'error' => 'Email already registered.'];

        $entity['password_hash'] = password_hash($entity['password'], PASSWORD_BCRYPT);
        unset($entity['password']);

        $entity = parent::create($entity);
        

        return ['success' => true, 'data' => $entity];
    }




    public function login($entity) {
        if (empty($entity['email']) || empty($entity['password'])) return ['success' => false, 'error' => 'Email and password are required.']; 

        $user = $this->auth_dao->get_user_by_email($entity['email']);
        if(!$user) return ['success' => false, 'error' => 'Invalid username OR password'];

        if (!$user || !password_verify($entity['password'], $user['password_hash'])) return ['success' => false, 'error' => 'Invalid username OR password'];

        unset($user['password_hash']);

        $jwt_payload = [
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'permissions' => $user['role'] === 'admin' 
                    ? ['can_create','can_edit','can_delete'] : ['can_view']
            ], 
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24h valid (for testing)
        ];

        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
    }

}


?>