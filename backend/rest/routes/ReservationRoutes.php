<?php 

/**
 * @OA\Get(
 *     path="/reservations",
 *     tags={"reservations"},
 *     summary="Get all reservations",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all reservations"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error while fetching reservations"
 *     )
 * )
 */
Flight::route('GET /reservations', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    try {
        Flight::json(Flight::reservationService()->getAll());
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/reservations/mine",
 *     tags={"reservations"},
 *     summary="Get reservations for currently logged-in user",
 *     security={{"Authentication": {}}}, 
 *     @OA\Response(
 *         response=200,
 *         description="List of reservations for current user"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
Flight::route('GET /reservations/mine', function () {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    $user = Flight::get('user');
    $userId = is_array($user) ? $user['id'] : $user->id;
    Flight::json(Flight::reservationService()->getForUser($userId));
});



/**
 * @OA\Get(
 *     path="/reservations/{id}",
 *     tags={"reservations"},
 *     summary="Get detailed reservation by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detailed reservation data"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid reservation ID"
 *     )
 * )
 */
Flight::route('GET /reservations/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::reservationService()->getDetailedById((int)$id));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 *     path="/reservations",
 *     tags={"reservations"},
 *     summary="Create a new reservation",
 *     security={{"Authentication": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","car_id","pickup_location_id","return_location_id","pickup_dt","return_dt"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="car_id", type="integer", example=5),
 *             @OA\Property(property="pickup_location_id", type="integer", example=2),
 *             @OA\Property(property="return_location_id", type="integer", example=3),
 *             @OA\Property(property="pickup_dt", type="string", format="date-time", example="2025-09-20 10:00:00"),
 *             @OA\Property(property="return_dt", type="string", format="date-time", example="2025-09-22 10:00:00"),
 *             @OA\Property(property="status", type="string", example="PENDING"),
 *             @OA\Property(property="total_price", type="number", format="float", example=150.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Reservation successfully created"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Car or user not found"
 *     )
 * )
 */
Flight::route('POST /reservations', function() {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    $user = Flight::get('user');
    $userId = is_array($user) ? $user['id'] : $user->id;

    try {
        $data = Flight::request()->data->getData();
        $data['user_id'] = $userId;
        Flight::json(Flight::reservationService()->createReservation($data), 201);
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});


/**
 * @OA\Patch(
 *     path="/reservations/{id}/status",
 *     tags={"reservations"},
 *     summary="Update reservation status",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="CANCELLED")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation status updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input or reservation not found"
 *     )
 * )
 */
Flight::route('PATCH /reservations/@id:[0-9]+/status', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        $status = $data['status'] ?? '';
        Flight::json(Flight::reservationService()->updateStatus((int)$id, $status));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 *     path="/reservations/user/{user_id}",
 *     tags={"reservations"},
 *     summary="Get all reservations for a specific user",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="Optional status filter (e.g., PENDING, CONFIRMED, CANCELLED)",
 *         @OA\Schema(type="string", example="CONFIRMED")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of reservations for user"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user ID or parameters"
 *     )
 * )
 */
Flight::route('GET /reservations/user/@user_id:[0-9]+', function($user_id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $status = Flight::request()->query['status'] ?? null;
        Flight::json(Flight::reservationService()->getForUser((int)$user_id, $status));
    } catch(Exception $e) {
       Flight::json(['error' => $e->getMessage()], 400); 
    }
});

/**
 * @OA\Get(
 *     path="/reservations/car/{car_id}",
 *     tags={"reservations"},
 *     summary="Get all reservations for a specific car",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="car_id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="Optional status filter (e.g., PENDING, CONFIRMED, CANCELLED)",
 *         @OA\Schema(type="string", example="PENDING")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of reservations for car"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid car ID or parameters"
 *     )
 * )
 */
Flight::route('GET /reservations/car/@car_id:[0-9]+', function($car_id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');


    try {
        $status = Flight::request()->query['status'] ?? null;
        Flight::json(Flight::reservationService()->getForCar((int)$car_id, $status));
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/reservations/{id}",
 *     tags={"reservations"},
 *     summary="Delete a reservation by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error deleting reservation"
 *     )
 * )
 */
Flight::route('DELETE /reservations/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');
    
    try {
        Flight::json(Flight::reservationService()->delete((int)$id));
        Flight::json(['message' => "Reservation deleted successfully."]);
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});



/**
 * @OA\Put(
 *     path="/reservations/{id}/cancel",
 *     tags={"reservations"},
 *     summary="Cancel a reservation by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation cancelled successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Reservation not found"
 *     )
 * )
 */
Flight::route('PUT /reservations/@id/cancel', function($id){
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    try {
        Flight::json(Flight::reservationService()->updateStatus($id, "CANCELLED"));
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 500);
    }
});











?> 