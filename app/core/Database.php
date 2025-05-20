<?php

require_once __DIR__ . '/../config/database/db.config.php';

class Database {
    private $db;

    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }

    public function query($sql) {
        return $this->db->query($sql);
    }

    public function prepare($sql) {
        return $this->db->prepare($sql);
    }

    public function escape($value) {
        return $this->db->real_escape_string($value);
    }
}
?>
