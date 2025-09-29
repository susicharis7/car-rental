<?php
require_once __DIR__ . '/../dao/BaseDao.php';

class BaseService {
    protected $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }


    public function getAll() {
        return $this->dao->getAll();
    }

    public function getById($id) {
        return $this->dao->getById($id);
    }

    public function create($data) {
        return $this->dao->add($data);
    }

    public function update($data, $id) {
        return $this->dao->update($data,$id);
    }

    public function delete($id) {
        return $this->dao->delete($id);
    }


    // ~~~~~~~~~~~~~~~~~ HELPER ~~~~~~~~~~~~~~~~~
    protected function requiredFields(array $data, array $required): void {
        foreach ($required as $r) {
            if (!array_key_exists($r, $data) || $data[$r] === null || $data[$r] === '') throw new Exception("Field '$r' is required. ");
        }
    }


    protected function carHasOverlap(int $carId, string $from, string $to, ?int $ignoreReservationId = null): bool {
        $existing = array_merge(
            $this->dao->find_by_car($carId, 'PENDING'),
            $this->dao->find_by_car($carId, 'CONFIRMED')
        );

        $fromTs = strtotime($from);
        $toTs = strtotime($to);

        foreach($existing as $e) {
            if($ignoreReservationId && (int)$e['id'] === $ignoreReservationId) continue;

            $eFrom = strtotime($e['pickup_dt']);
            $eTo = strtotime($e['return_dt']);

            if($eFrom < $toTs && $eTo > $fromTs) return true;

        } return false;

    }
}

?>