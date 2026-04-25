<?php

namespace App\Models;

use Core\Model;

class ItemModel extends Model {
    public function getAvailable() {
        return $this->fetchAll("SELECT items.*, categories.name as category_name 
                               FROM items 
                               JOIN categories ON items.category_id = categories.id 
                               WHERE items.status = 'available'");
    }

    public function getByCategory($categoryId) {
        return $this->fetchAll("SELECT * FROM items WHERE category_id = ? AND status = 'available'", [$categoryId]);
    }
}
