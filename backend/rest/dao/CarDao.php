<?php
require_once __DIR__ . '/BaseDao.php';

class CarDao extends BaseDao {
    protected string $table_name;

    public function __construct() {
        $this->table_name = "cars";
        parent::__construct($this->table_name);
    }

    /* ALL Active Cars */
    public function get_active(): array {
        $sql = "SELECT * FROM `{$this->table_name}` WHERE is_active = 1 ORDER BY id DESC";
        return $this->query($sql, []);
    }

    /*
     * Available Cars in THAT specific period (from - to)
     * NEMA PREKLAPANJA Sa Reserved Cars & Pending?
     */
    public function find_available(string $from, string $to): array {
        $sql = "
        SELECT c.*
        FROM `cars` c
        WHERE c.is_active = 1
          AND NOT EXISTS (
            SELECT 1
            FROM `reservations` r
            WHERE r.car_id = c.id
              AND r.status IN ('PENDING','CONFIRMED')
              AND NOT (r.return_dt <= :from OR r.pickup_dt >= :to)
          )
        ORDER BY c.created_at DESC";
        return $this->query($sql, [':from' => $from, ':to' => $to]);
    }

    /** Activate/Deactivate AVAILABLE Car */
    public function set_active(int $carId, bool $active): array {
        return $this->update(['is_active' => $active ? 1 : 0], $carId);
    }


    /** Create car with >> is_active = 1 */
    public function create(array $car): array {
        if (!isset($car['is_active'])) $car['is_active'] = 1;
        return $this->add($car);
    }
}
