<?php
require 'vendor/autoload.php';

// Services
require_once __DIR__ . '/rest/services/AuthService.php'; // Auth
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/CarService.php';
require_once __DIR__ . '/rest/services/LocationService.php';
require_once __DIR__ . '/rest/services/ReservationService.php';

// Middleware
require_once __DIR__ .'/middleware/AuthMiddleware.php';

// Register Services
Flight::register('authService', 'AuthService'); // Auth
Flight::register('authMiddleware', 'AuthMiddleware');
Flight::register('userService', 'UserService');
Flight::register('carService', 'CarService');
Flight::register('locationService', 'LocationService');
Flight::register('reservationService', 'ReservationService');

// Every route except login~register :: must have VALID token
Flight::route('/*', function() {
    // $url - path that client calls :: `localhost/car-rental/backend/auth/login` 
    $url = Flight::request()->url;

    // login~register~docs without Token (provjerava da li traze token ~ ako ne ~ odmah returna TRUE, javno su dostupne)
    if(strpos($url, '/auth/login') === 0 || 
    strpos($url, '/auth/register') === 0 || 
    strpos($url, '/docs') || 
    strpos($url, 'cars/active' || 
    strpos($url, 'locations')
    
    )) return true;

    // enters `try-catch` , takes the header out of `Authentication` => ono sto posaljemo u swaggeru u `Authorize` 
    try {
        // uzima `token` koji unesemo nakon logina od korisnika ~ sprema se u `$token`
        $token = Flight::request()->getHeader("Authentication");
        // poziva se class `authMiddleware` - i metoda verifyToken, gdje mu proslijedjujemo nas token od korisnika kako bi provjerili da li je validan
        if (Flight::authMiddleware()->verifyToken($token)) return true;
    } catch(Exception $e) {
        // in case token is not valid ~ return error message
        Flight::halt(401, $e->getMessage());
    }
    
    


});





// Routes 
require_once __DIR__ . '/rest/routes/AuthRoutes.php'; // Auth
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/CarRoutes.php';
require_once __DIR__ . '/rest/routes/LocationRoutes.php';
require_once __DIR__ . '/rest/routes/ReservationRoutes.php';

// Ping 
Flight::route('/', function(){
    // echo 'Hello world!';
});

Flight::start();
