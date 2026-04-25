<?php

namespace App\Models;

use Core\Model;

class TableModel extends Model {
    public function getAll() {
        return $this->fetchAll("SELECT * FROM tables");
    }

    public function getById($id) {
        return $this->fetch("SELECT * FROM tables WHERE id = ?", [$id]);
    }

    public function getByTableNumber($number) {
        return $this->fetch("SELECT * FROM tables WHERE table_number = ?", [$number]);
    }

    public function updateStatus($id, $status) {
        return $this->query("UPDATE tables SET status = ? WHERE id = ?", [$status, $id]);
    }
}
