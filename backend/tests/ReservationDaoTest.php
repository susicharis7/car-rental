<?php
require_once __DIR__ . '/../rest/dao/ReservationDao.php';

function pretty($d){ echo "<pre>".json_encode($d, JSON_PRETTY_PRINT)."</pre>"; }

$dao = new ReservationDao();

// First Reservation
$firstRes = [
    "user_id" => 1,
    "car_id" => 1,
    "pickup_location_id" => 1, 
    "return_location_id" => 1,
    "pickup_dt" => '2025-09-10 10:00:00',
    "return_dt" => '2025-09-11 10:00:00',
    "status" => "PENDING",
    "total_price" => 999.00
];

$firstResCreated = $dao->create($firstRes);
// print_r($firstResCreated);


// Second Reservation
$secondRes = [
    "user_id" => 2,
    "car_id" => 2,
    "pickup_location_id" => 1,
    "return_location_id" => 1,
    "pickup_dt" => '2025-08-30 10:00:00',
    "return_dt" => '2025-08-31 10:00:00',
    "status" => "COMPLETED",
    "total_price" => 999.00
];

$secondResCreated = $dao->create($secondRes);
// print_r($secondResCreated);


echo "Created Reservations: \n";
pretty([$firstResCreated, $secondResCreated]);

// Find by ID
echo "Find by ID (id = 1):  \n";
pretty($dao->find_by_id((int)$firstResCreated['id']));

// Find by CAR
echo "Find by car (id = 1): \n";
pretty($dao->find_by_car(1));

// Find by CAR + Status Filter
echo "Find by car (with filter): \n";
pretty($dao->find_by_car(2, 'COMPLETED'));

// Find by USER
echo "Find by user (id = 1): \n";
pretty($dao->find_by_user(1));

// Find by USER + Status Filter
echo "Find by user(with filter): \n";
pretty($dao->find_by_user(1, 'PENDING'));



?>