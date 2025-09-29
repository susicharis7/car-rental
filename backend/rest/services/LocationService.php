<?php 
    require_once __DIR__ . '/BaseService.php';
    require_once __DIR__ . '/../dao/LocationDao.php';


    class LocationService extends BaseService {
        protected $dao;

        public function __construct() {
            parent::__construct(new LocationDao());
        }

        // find by city
        public function findByCity(string $city): array {
            $city = trim($city);
            if ($city == '')  throw new Exception('City is reqiured.');
            return $this->dao->find_by_city($city);
        }

        // find by country
        public function findByCountry(string $country): array {
            $country = trim($country);
            if ($country == '') throw new Exception('Country is required.');
            return $this->dao->find_by_country($country);
        }

        // create location
        public function createLocation(array $data): array {
            $required = ['name', 'city', 'country'];
            
            foreach($required as $field) {
                if (!isset($data[$field]) || trim($data[$field]) === '') throw new Exception("Field `$field` is required.");
            }

            if (!isset($data['address'])) $data['address'] = null;

            return $this->dao->add($data);
        }
    }

?>