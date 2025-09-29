<?php

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all users"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error while fetching users"
 *     )
 * )
 */
Flight::route('GET /users', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } 
});


/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /users/@id:[0-9]+', function($id){

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->getById((int)$id));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Create a new user",
 *     security={{"Authentication": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="secret123"),
 *             @OA\Property(property="role", type="string", example="customer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User successfully created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */
Flight::route('POST /users', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::userService()->createUser($data),201);
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update an existing user",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Jane Doe"),
 *             @OA\Property(property="email", type="string", example="jane@example.com"),
 *             @OA\Property(property="password", type="string", example="newpassword"),
 *             @OA\Property(property="role", type="string", example="admin")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully updated"
 *     )
 * )
 */
Flight::route('PUT /users/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::userService()->updateUser((int)$id, $data));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete a user by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully deleted"
 *     )
 * )
 */
Flight::route('DELETE /users/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->delete((int)$id));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Patch(
 *     path="/users/{id}/activate",
 *     tags={"users"},
 *     summary="Activate a user",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User activated"
 *     )
 * )
 */
Flight::route('PATCH /users/@id:[0-9]+/activate', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->activate((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Patch(
 *     path="/users/{id}/deactivate",
 *     tags={"users"},
 *     summary="Deactivate a user",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deactivated"
 *     )
 * )
 */
Flight::route('PATCH /users/@id:[0-9]+/deactivate', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->deactivate((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 *     path="/users/active",
 *     tags={"users"},
 *     summary="Get all active users",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of active users"
 *     )
 * )
 */
Flight::route('GET /users/active', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->getActive());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/users/inactive",
 *     tags={"users"},
 *     summary="Get all inactive users",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of inactive users"
 *     )
 * )
 */
Flight::route('GET /users/inactive', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::userService()->getInactive());
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 *     path="/users/search",
 *     tags={"users"},
 *     summary="Search users by name",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=false,
 *         description="Name to search for",
 *         @OA\Schema(type="string", example="John")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of matching users"
 *     )
 * )
 */
Flight::route('GET /users/search', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');


    try {
        $q = Flight::request()->query['name'] ?? '';
        Flight::json(Flight::userService()->searchByName($q));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/users/by-email",
 *     tags={"users"},
 *     summary="Search user by email",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         description="Email address",
 *         @OA\Schema(type="string", example="john@example.com")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /users/by-email', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $email = Flight::request()->query['email'] ?? '';
        Flight::json(Flight::userService()->searchByEmail($email));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});


/**
 * @OA\Post(
 *     path="/login",
 *     tags={"users"},
 *     summary="User login",
 *     security={{"Authentication": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="secret123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful, returns JWT token"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */
Flight::route('POST /login', function() {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    
    try {
        $data = Flight::request()->data->getData();
        $email = $data['email'] ?? '';
        $pass  = $data['password'] ?? '';
        Flight::json(Flight::userService()->login($email, $pass));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 401);
    }
});


/**
 * @OA\Get(
 *     path="/users/me",
 *     tags={"users"},
 *     summary="Get the currently logged-in user's profile",
 *     security={{"Authentication": {}}}, 
 *     @OA\Response(
 *         response=200,
 *         description="User info retrieved successfully"
 *     )
 * )
 */
Flight::route('GET /users/me', function () {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    $user = Flight::get('user');
    $userId = is_array($user) ? $user['id'] : $user->id;

    Flight::json(Flight::userService()->getById($userId));
});


/**
 * @OA\Put(
 *     path="/users/me",
 *     tags={"users"},
 *     summary="Update password for logged-in user",
 *     security={{"Authentication": {}}}, 
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"password"},
 *             @OA\Property(property="password", type="string", example="newpass123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input or missing password"
 *     )
 * )
 */
Flight::route('PUT /users/me', function () {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    $user = Flight::get('user');
    $data = Flight::request()->data->getData();

    if (!isset($data['password']) || trim($data['password']) === '') {
        Flight::halt(400, "Password is required.");
    }

    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

    // Update samo password_hash
    Flight::json(Flight::userService()->update([
        'password_hash' => $hashed
    ], $user['id']));
});


