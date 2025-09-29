<?php 

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once __DIR__ . '/../rest/services/LocationService.php';

    function pretty($label, $data) {
        echo "\n=== $label ===\n";
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>\n";
    }

    try {
        $service = new LocationService();

        pretty('All locations', $service->getAll());
        pretty('By city: Sarajevo', $service->findByCity('Sarajevo'));
        pretty('By country: Bosnia and Herzegovina', $service->findByCountry('BiH'));



    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage();
    }



?>