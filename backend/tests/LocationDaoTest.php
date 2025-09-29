<?php
require_once __DIR__ . '/../rest/dao/LocationDao.php';
function pretty($d){ echo "<pre>".json_encode($d, JSON_PRETTY_PRINT)."</pre>"; }


    $dao = new LocationDao();

    // Find by city
    echo "Find by city (Sarajevo): \n";
    pretty($dao->find_by_city("Sarajevo"));

    // Find by country
    echo "Find by country (BiH): \n";
    pretty($dao->find_by_country("BiH"));


?>