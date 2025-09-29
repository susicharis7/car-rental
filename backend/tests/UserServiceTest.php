<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../rest/services/UserService.php';

function pretty($label, $data) {
    echo "\n$label\n";
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>\n";
}

try {

    $service = new UserService();

    $newUser = $service->createUser([
        'first_name' => 'Lajla',
        'last_name' => 'Karac',
        'email' => 'lajla.karac@ibu.edu.ba',
        'password' => 'karac123'
    ]);


    // search by email
    $foundByEmail = $service->searchByEmail('lajla.karac@ibu.edu.ba');
    pretty("Found Lajla!: " , $foundByEmail);

    

} catch (Exception $e) {
    echo e.getMessage();
}


?>