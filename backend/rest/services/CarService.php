<?php 
    require_once __DIR__ . '/BaseService.php';
    require_once __DIR__ . '/../dao/CarDao.php';

    class CarService extends BaseService {
        protected $dao;

        public function __construct() {
            parent::__construct(new CarDao());
        }

        // For createCar()

        private function requireFields(array $data, array $required): void {
            foreach ($required as $k) {
                if (!array_key_exists($k, $data) || $data[$k] === null || $data[$k] === '') {
                    throw new Exception("Field '$k' is required.");
                }
            }
        }


        public function getActive(): array {
            return $this->dao->get_active();
        }

        // Find available `from` -> `to` 
        public function findAvailable(string $from, string $to): array {
            if (strtotime($to) <= strtotime($from)) {
                throw new Exception("Invalid period: 'to' must be after 'from' ");
            }

            return $this->dao->find_available($from, $to);
        }
        

        // `ACTIVATE` car
        public function activate(int $carId): array {
            return $this->dao->set_active($carId, true);
        }

        // `DEACTIVATE` car
        public function deactivate(int $carId): array {
            return $this->dao->set_active($carId, false);
        }

        // Create car with default `active = true(1)`
        public function createCar(array $data): array {

            if (!isset($data['is_active'])) $data['is_active'] = 1;

            // light validation
            $this->requireFields($data, ['model', 'year', 'price_per_day']);
            return $this->dao->create($data);
        }

        // Update car
        public function updateCar(int $id, array $data): array {
            return $this->dao->update($data, $id);
        }
    }



?>