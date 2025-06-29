<?php

if (file_exists(__DIR__ . '/../../config/db.config.local.php')) {
    require_once __DIR__ . '/../../config/db.config.local.php';
} else {
    require_once __DIR__ . '/../../config/db.config.php';
}

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

class DatabaseConfig {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $configPath = file_exists(__DIR__ . '/../../config/db.config.local.php')
            ? __DIR__ . '/../../config/db.config.local.php'
            : __DIR__ . '/../../config/db.config.php';
        $dbConfig = include $configPath;
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $dbname = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        $charset = isset($dbConfig['charset']) ? $dbConfig['charset'] : 'utf8mb4';
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->connection = new PDO($dsn, $username, $password, $options);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>
