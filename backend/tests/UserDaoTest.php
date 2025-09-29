<?php
    require_once __DIR__ . '/../rest/dao/UserDao.php';

    function pretty($data) {
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    }

    $dao = new UserDao();

    // Find by email
    echo "Find by email: \n";
    pretty($dao->find_by_email('haris.susic@stu.ibu.edu.ba'));


    // Find by name/surname
    echo "Find by name/surname: \n";
    pretty($dao->find_by_name('hari'));

    // Active Users
    echo "active users:\n";
    pretty($dao->find_active());

    // Deactivate user then check inactive users
    $firstUser = $dao->find_by_email('haris.susic@stu.ibu.edu.ba');
    if ($firstUser) {
        $dao->set_active((int)$firstUser['id'], false);
    }

    echo "inactive users (after deactivation): \n";
    pretty($dao->find_inactive());

    // Get it back to `active`
    if ($firstUser) {
        $dao->set_active((int)$firstUser['id'], true);
    }



    // Add new User
    $newUser = [
        "first_name" => "Tarik",
        "last_name" => "Skender",
        "email" => "tarik.skender@stu.ibu.edu.ba",
        "password_hash" => "tarikskender123"
    ];

    echo "Added new User: \n";
    pretty($dao->create($newUser));

?>