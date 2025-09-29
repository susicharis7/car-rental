<?php 

/**
 * @OA\Get(
 *     path="/locations",
 *     tags={"locations"},
 *     summary="Get all locations",
 *     security={{"Authentication": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all locations"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error while fetching locations"
 *     )
 * )
 */
Flight::route('GET /locations', function() {
    // Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    try {
        Flight::json(Flight::locationService()->getAll());
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/locations/{id}",
 *     tags={"locations"},
 *     summary="Get location by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Location ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Location details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Location not found"
 *     )
 * )
 */
Flight::route('GET /locations/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        Flight::json(Flight::locationService()->getById((int)$id));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Post(
 *     path="/locations",
 *     tags={"locations"},
 *     summary="Create a new location",
 *     security={{"Authentication": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","city","country"},
 *             @OA\Property(property="name", type="string", example="Kraljevski naziv ulice"),
 *             @OA\Property(property="address", type="string", example="dr. Irfana Ljubijankica 27"),
 *             @OA\Property(property="city", type="string", example="Bihac"),
 *             @OA\Property(property="country", type="string", example="Bosnia")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Location successfully created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */
Flight::route('POST /locations', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::locationService()->createLocation($data), 201);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/locations/{id}",
 *     tags={"locations"},
 *     summary="Delete a location by ID",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Location ID",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Location successfully deleted"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error deleting location"
 *     )
 * )
 */ 
Flight::route('DELETE /locations/@id:[0-9]+', function($id) {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));
    Flight::authMiddleware()->authorizeRole('admin');

    try {   
        Flight::json(Flight::locationService()->delete((int)$id));
        Flight::json(['message' => "Location deleted successfully."]);
    } catch(Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/locations/search",
 *     tags={"locations"},
 *     summary="Search locations by city or country",
 *     security={{"Authentication": {}}},
 *     @OA\Parameter(
 *         name="city",
 *         in="query",
 *         required=false,
 *         description="City to search for",
 *         @OA\Schema(type="string", example="Sarajevo")
 *     ),
 *     @OA\Parameter(
 *         name="country",
 *         in="query",
 *         required=false,
 *         description="Country to search for",
 *         @OA\Schema(type="string", example="Bosnia and Herzegovina")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of matching locations"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing or invalid query parameter"
 *     )
 * )
 */
Flight::route('GET /locations/search', function() {

    Flight::authMiddleware()->verifyToken(Flight::request()->getHeader("Authentication"));

    try {
        $query = Flight::request()->query;

        if (isset($query['city'])) {
            $city = $query['city'];
            Flight::json(Flight::locationService()->findByCity($city));
        } elseif (isset($query['country'])) {
            $country = $query['country'];
            Flight::json(Flight::locationService()->findByCountry($country));
        } else {
            throw new Exception("Please provide either 'city' or 'country' as query parameter.");
        }

    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});



?>