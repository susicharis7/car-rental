<?php 
use Firebase\JWT\JWT;
use Firebase\JWT\key;

Flight::group('/auth', function() {

     /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email", "password"},
     *             @OA\Property(property="first_name", type="string", example="Haris"),
     *             @OA\Property(property="last_name",type="string",example="Susic"),
     *             @OA\Property(property="email", type="string", example="haris.susic@stu.ibu.edu.ba"),
     *             @OA\Property(property="password", type="string", example="mypassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User has successfully registered."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Email already registered or internal error."
     *     )
     * )
     */
    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();
        $response = Flight::authService()->register($data);

        if($response['success']) {
            Flight::json([
                'message' => 'User has registered successfully.',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }
    });

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login user and return JWT token",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin.bob@gmail.com"),
     *             @OA\Property(property="password", type="string", example="mypassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful and token returned"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Invalid credentials"
     *     )
     * )
     */
    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();
        $response = Flight::authService()->login($data);

        if($response['success']) {
            Flight::json([
                'message' => 'User has logged in successfully.',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }

    });

});