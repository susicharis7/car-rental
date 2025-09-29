<?php 
require_once __DIR__ . '/BaseDao.php';

class ReservationDao extends BaseDao {
    protected string $table_name;

    public function __construct() {
        $this->table_name = 'reservations';
        parent::__construct($this->table_name);
    }

    // find by id - joins for cleaner output
    public function find_by_id(int $id): ?array {
        return $this->query_unique("
            SELECT r.*, 
                   c.model, c.year, c.price_per_day,
                   u.first_name, u.last_name, u.email,
                   lp.name AS pickup_location, lr.name AS return_location
            FROM `reservations` r
            JOIN `cars` c ON c.id = r.car_id
            JOIN `users` u ON u.id = r.user_id
            JOIN `locations` lp ON lp.id = r.pickup_location_id
            JOIN `locations` lr ON lr.id = r.return_location_id
            WHERE r.id = :id
        ", [':id' => $id]) ?: null;
    }

    // ALL Reservations by specific car
    public function find_by_car(int $carId, ?string $status = null): array {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.email, c.model, c.year, c.price_per_day 
                FROM `reservations` r
                JOIN `users` u ON u.id = r.user_id
                JOIN `cars` c ON c.id = r.car_id
                WHERE r.car_id = :car_id";
        $params = [':car_id' => $carId];
        if ($status) { 
            $sql .= " AND r.status = :status"; 
            $params[':status'] = $status; 
        }
        $sql .= " ORDER BY r.pickup_dt DESC";
        return $this->query($sql, $params);
    }

    // ALL Reservations for specific user
    public function find_by_user(int $userId, ?string $status = null): array {
        $sql = "SELECT r.*, c.model, c.year, lp.name AS pickup_location, lr.name AS return_location
                FROM `reservations` r 
                JOIN `cars` c ON c.id = r.car_id
                JOIN `locations` lp ON lp.id = r.pickup_location_id
                JOIN `locations` lr ON lr.id = r.return_location_id
                WHERE r.user_id = :uid";
        $params = [':uid' => $userId];
        if ($status) { 
            $sql .= " AND r.status = :status"; 
            $params[':status'] = $status; 
        }
        $sql .= " ORDER BY r.pickup_dt DESC";
        return $this->query($sql, $params);
    }

    // uses BaseDao, default `pending` if not put
    public function create(array $res): array {
        if (!isset($res['status'])) $res['status'] = 'PENDING';
        return $this->add($res);
    }
}
