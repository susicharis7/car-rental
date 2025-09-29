<?php
require_once __DIR__ . '/BaseDao.php';

    class LocationDao extends BaseDao {
        protected string $table_name;

        public function __construct() {
            $this->table_name = 'locations';
            parent::__construct($this->table_name);
        }


        // Find by city
        public function find_by_city($city) {
            $stmt = $this->connection->prepare("SELECT * FROM locations WHERE city = :city");
            $stmt->execute(["city" => $city]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find by country
        public function find_by_country($country) {
            $stmt = $this->connection->prepare("SELECT * FROM locations WHERE country = :country");
            $stmt->execute(["country" => $country]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
    }


?>