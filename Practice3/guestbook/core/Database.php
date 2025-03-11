<?php 

class Database {
    private static ?Database $instance = null;
    private ?SQLite3 $db = null;

    private function __construct() {
        $config = require '../config/db.php';
        $this->db = new SQLite3($config['path']);
        foreach ($config['pragmas'] as $pragma => $value) {
            $this->db->exec("PRAGMA $pragma = $value");
        }
        $this->db->enableExceptions(true);
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): SQLite3 {
        if ($this->db === null) {
            throw new Exception("Database connection error");
        }
        return $this->db;
    }

    public function close(): void {
        if ($this->db === null) {
            return;
        }
        $this->db->close();
        $this->instance = null;
        $this->db = null;
    }
}

$db = Database::getInstance();