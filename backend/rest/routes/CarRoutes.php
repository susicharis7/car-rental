<?php


/**
 * @OA\Get(
 *     path="/cars/active",
 *     tags={"cars"},
 *     summary="Get all active cars",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of ALL Active cars"
 *     )
 * )
 */
Flight::route('GET /cars/active', function () {
    /* I commented it because I want the guest to see the active cars also */
    // Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    try {
        Flight::json(Flight::carService()->getActive());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Get(
 *     path="/cars/available",
 *     tags={"cars"},
 *     summary="Find available cars within a date range",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="from",
 *         in="query",
 *         required=true,
 *         description="Start date (YYYY-MM-DD)",
 *         @OA\Schema(type="string", format="date", example="2025-09-20")
 *     ),
 *     @OA\Parameter(
 *         name="to",
 *         in="query",
 *         required=true,
 *         description="End date (YYYY-MM-DD)",
 *         @OA\Schema(type="string", format="date", example="2025-09-25")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of available cars"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing or invalid parameters"
 *     )
 * )
 */
Flight::route('GET /cars/available', function () {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    try {
        $from = Flight::request()->query['from'] ?? '';
        $to   = Flight::request()->query['to'] ?? '';

        if (!$from || !$to) {
            throw new Exception("Missing 'from' or 'to' parameter");
        }

        Flight::json(Flight::carService()->findAvailable($from, $to));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Get all cars",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of ALL cars"
 *     )
 * )
 */
Flight::route('GET /cars', function () {
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    try {
        Flight::json(Flight::carService()->getAll());
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

// create,update,delete

/**
 * @OA\Post(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Create a new car",
 *     security={{"Authentication": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"brand","model","price_per_day"},
 *             @OA\Property(property="model", type="string", example="Toyota Corolla"),
 *             @OA\Property(property="year", type="integer", example=2022),
 *             @OA\Property(property="price_per_day", type="number", format="float", example=45.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Car successfully created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */
Flight::route('POST /cars', function () {
    // only `admin` can create a new car
    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::carService()->createCar($data), 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Update an existing car",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="model", type="string", example="Honda Civic"),
 *             @OA\Property(property="year", type="integer", example=2023),
 *             @OA\Property(property="price_per_day", type="number", format="float", example=55.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car successfully updated"
 *     )
 * )
 */
Flight::route('PUT /cars/@id:[0-9]+', function ($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::carService()->updateCar((int)$id, $data));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Delete a car by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car successfully deleted"
 *     )
 * )
 */
Flight::route('DELETE /cars/@id:[0-9]+', function ($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::carService()->delete((int)$id));
        Flight::json(['message' => "Car deleted successfully."]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

// activate,deactivate

/**
 * @OA\Patch(
 *     path="/cars/{id}/activate",
 *     tags={"cars"},
 *     summary="Activate a car",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car activated"
 *     )
 * )
 */
Flight::route('PATCH /cars/@id:[0-9]+/activate', function ($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::carService()->activate((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Patch(
 *     path="/cars/{id}/deactivate",
 *     tags={"cars"},
 *     summary="Deactivate a car",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car deactivated"
 *     )
 * )
 */
Flight::route('PATCH /cars/@id:[0-9]+/deactivate', function ($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::carService()->deactivate((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Get car by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Car ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Car details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Car not found"
 *     )
 * )
 */
Flight::route('GET /cars/@id:[0-9]+', function ($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::carService()->getById((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

?>
