<?php
require_once __DIR__ . '/../rest/dao/CarDao.php';

function pretty($data) {
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
}

$carDao = new CarDao();

$firstCar = [
    "model" => "BMW M3",
    "year" => 2021,
    "price_per_day" => 150.00
];

$secondCar = [
    "model" => "BMW M4 CS",
    "year" => 2017,
    "price_per_day" => 120.00
];

// Create first Car (default is_active = 1)
$firstTest = $carDao->create($firstCar);
echo "Created car:";
pretty($firstTest);

// ALL Active Cars
echo "Active cars before deactivation:";
$secondTest = $carDao->get_active();
pretty($secondTest);

// Deactivate Car
echo "Deactivating car ID={$firstTest['id']}...";
$thirdTest = $carDao->set_active($firstTest['id'], false);
pretty($thirdTest);

// 4. Again list of ALL Active Cars
echo "Active cars after deactivation:";
$listAgain = $carDao->get_active();
pretty($listAgain);
