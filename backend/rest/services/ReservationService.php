<?php 
    require_once __DIR__ . '/BaseService.php';
    require_once __DIR__ . '/../dao/ReservationDao.php';
    require_once __DIR__ . '/../dao/CarDao.php'; // for price/validation of cars

    class ReservationService extends BaseService {
        protected $dao;
        private CarDao $carDao;

        public function __construct() {
            parent::__construct(new ReservationDao());
            $this->carDao = new CarDao();

        }


        // find by id (uses query_unique, with nice joins as I made them in DAO)
        public function getDetailedById(int $id) : ?array {
            $row = $this->dao->find_by_id($id);
            if (!$row) throw new Exception("Reservation ID not found.");
            return $row;
        }


        // all reservations for specific user
        public function getForUser(int $userId, ?string $status = null): array {
            return $this->dao->find_by_user($userId, $status);
        }

        // all reservations for specific car
        public function getForCar(int $carId, ?string $status = null): array {
            return $this->dao->find_by_car($carId, $status);
        }

        // --------------------- CREATE ---------------------- // 

        public function createReservation(array $data): array {
            // required fields !
            $this->requiredFields($data, [
                'user_id', 'car_id', 'pickup_location_id', 'return_location_id', 'pickup_dt', 'return_dt'
            ]);

            // date validations !
            $fromTs = strtotime($data['pickup_dt']);
            $toTs = strtotime($data['return_dt']);

            if ($fromTs === false || $toTs === false) {
                throw new Exception("Invalid datetime format.");
            }

            if ($toTs <= $fromTs) {
                throw new Exception("Return date/time must be AFTER pickup date/time.");
            }

            // basic car checkings - if car doesn't exist -> throw error 
            $car = $this->carDao->getById((int)$data['car_id']);
            if (!$car) throw new Exception("Car was not found.");

            // if it says that car is active but it is not, throw exception
            if (!isset($car['is_active']) || (int)$car['is_active'] !== 1) {
                throw new Exception("Car is NOT ACTIVE.");
            }

            // preklapanje auta
            if ($this->carHasOverlap(
                (int)$data['car_id'],
                $data['pickup_dt'],
                $data['return_dt']
            )) throw new Exception("Car is not available in the selected period!");


            // obracun cijene
            $start = new DateTime($data['pickup_dt']);
            $end = new DateTime($data['return_dt']);
            $days = $start->diff($end)->days;

            if ($days <= 0) throw new Exception("Pickup and return dates must be atleast one day apart.");

            $data['total_price'] = $days * $car['price_per_day'];
            $data['status'] = "PENDING"; // default

            

            return $this->dao->create($data);
        
        }


        // Change Reservation Status
        public function updateStatus(int $id, string $status) : array {
            $allowedStatuses = ['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED'];

            if (!in_array($status, $allowedStatuses)) throw new Exception("Invalid reservation status.");

            $reservation = $this->dao->getById($id);
            if(!$reservation) throw new Exception("Reservation not found");

            return $this->dao->update(['status' => $status], $id);
        }

    }

?>