<?php 

    error_reporting(E_ALL);
    ini_set('display_errors',1);

    require_once __DIR__ . '/../rest/services/CarService.php';

    function pretty($label, $data) {
        echo "\n=== $label ===\n";
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>\n";
    }

    try {
        $service = new CarService();

        // getActive
        $active = $service->getActive();

        // find Available
        $from = '2025-09-20 10:00:00';
        $to = '2025-09-22 10:00:00';
        $available = $service->findAvailable($from,$to);
        pretty("Available cars: [$from -> $to]", $available);


        // create car
        $created = $service->createCar([
            'model' => 'VW Golf 8',
            'year' => 2024,
            'price_per_day' => 99.00
        ]);

        pretty('Created car: ', $created);


        // update car (let's change the price)
        $updated = $service->updateCar((int)$created['id'], ['price_per_day' => 69.69]);
        pretty('Updated Car: ', $updated);

            echo "\nAll tests done.\n";



    } catch(Throwable $e) {
         echo "\n[TEST ERROR] " . $e->getMessage() . "\n";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

?>