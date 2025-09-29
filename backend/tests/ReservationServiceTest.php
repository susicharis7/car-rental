<?php 
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once __DIR__ . '/../rest/services/ReservationService.php';
require_once __DIR__ . '/../rest/dao/ReservationDao.php'; // just to input from here one reservation - so we can test the service

function pretty($label, $data) {
    echo "\n<< $label >>\n";
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>\n";
}

// getDetailedById TEST 
try {
    $service = new ReservationService();
    $dao = new ReservationDao();

    $firstDaoReservation = $dao->create([
        'user_id' => 1,
        'car_id' => 1,
        'pickup_location_id' => 1,
        'return_location_id' => 1,
        'pickup_dt' => '2025-09-03 00:00:00',
        'return_dt' => '2025-09-04 15:00:00',
        'status' => 'CONFIRMED',
        'total_price' => 330.00
    ]);


    pretty("Created a setup (first DAO reservation): ", $firstDaoReservation);


    $id = 1;

    $detailed = $service->getDetailedById($id);
    pretty("getDetailedById (id = $id)", $detailed);



} catch (Throwable $e) {
    echo e.getMessage();
}


// getForUser TEST

try {

    $secondService = new ReservationService();

    // all reservations for first user
    $all = $secondService->getForUser(1);
    pretty("Every reservation for first user: ", $all);

    // only PENDING reservations
    $pending = $secondService->getForUser(1, 'PENDING');
    pretty("Every reservation that are `PENDING` : ", $pending);

    // only COMPLETED reservations
    $completed = $secondService->getForUser(1,'COMPLETED');
    pretty("Completed reservations for user: " , $completed);


} catch(Throwable $e) {
    echo e.getMessage();
}



// getForCar TEST


try {

    $thirdService = new ReservationService();

    $getForFirstCar = $thirdService->getForCar(1);
    pretty("Everything for the first car (car_id = 1): ", $getForFirstCar);

} catch(Throwable $e) {
    echo e.getMessage();
}


// ============ CREATE =============== TEST

try {

    $fourthService = new ReservationService();

    // let us create a reservation
    $newRes = $fourthService->createReservation([
        'user_id' => 1,
        'car_id' => 3,
        'pickup_location_id' => 1,
        'return_location_id' => 2,
        'pickup_dt' => '2025-11-25 09:00:00',
        'return_dt' => '2025-11-26 09:00:00',
        'total_price' => 599.99
    ]);

    pretty("Created a new reservation: ", $newRes);


    // let us now make another reservation where overlap will happen

    $newOverlapRes = $fourthService->createReservation([
        'user_id' => 2,
        'car_id' => 3,
        'pickup_location_id' => 2,
        'return_location_id' => 1,
        'pickup_dt' => '2025-11-25 12:00:00',
        'return_dt' => '2025-11-26 15:00:00',
        'total_price' => 589.99
    ]);

    pretty("Overlap reservation: ", $newOverlapRes);

} catch (Exception $e) {
    pretty("Expected overlap error", $e->getMessage());
}



?>