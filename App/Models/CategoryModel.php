<?php

namespace App\Models;

use Core\Model;

class CategoryModel extends Model {
    public function getAll() {
        return $this->fetchAll("SELECT * FROM categories");
    }
}
