<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\key;

class AuthMiddleware {

    // takes `$token` from index.php, and verifies it
    public function verifyToken($token) {
        // if the token doesn't exist ~ return `error 401 - missing ...`
        if(!$token) Flight::halt(401, "Missing authentication header.");
        /* decode ~ razbije token na `header, payload, signature` 
        *  decodes it from base64 to JSON 
        *  new Key() ~ use this key + this algorithm to check the token 
        * JWT_SECRET() ~ checks if the token is valid 
        * if everything is OK ~ `$decoded` has got users data & time
        */
        $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

        // if the token IS VALID ~ zapamti korisnika globalno ~     from $decoded to $user 
        Flight::set('user', $decoded->user); // sprema payload od usera
        Flight::set('jwt_token', $token); // sprema ORIGINALNI TOKEN STRING 

        return true;
    }

    /* Allow access only to users with specific role
     * Takes `user` that went through `verifyToken` ~ Flight::set('user', $decoded->user);
     * looks at property `role`
     * if user doesn't have that `role` - Flight::halt
     */
    public function authorizeRole($requiredRole) {
        $user = Flight::get('user');
        if (!isset($user->role) || $user->role !== $requiredRole) {
            Flight::halt(403, 'Access denied: insufficient privileges');
        }
    }

    /* Does the same thing as authorizeRole, but here it checks if user has ANY role
     * we forward a list instead of only one role
     * 
     */
    public function authorizeRoles($roles = []) {
        $user = Flight::get('user');
        if (!isset($user->role) || !in_array($user->role, $roles)) {
            Flight::halt(403, 'Forbidden: role not allowed');
        }
    }

    //  Allow access based on a permission string in JWT
     public function authorizePermission($permission) {
        $user = Flight::get('user');
        if (!isset($user->permissions) || !in_array($permission, $user->permissions)) {
            Flight::halt(403, 'Access denied: missing permission');
        }
    }


}