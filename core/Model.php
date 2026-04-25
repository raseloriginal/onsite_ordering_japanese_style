<?php

namespace Core;

use PDO;

class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
