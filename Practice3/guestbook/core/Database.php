<?php 
namespace Guestbook\Core;
use mysqli;
use Exception;
class Database {
    private static ?Database $instance = null;
    private ?mysqli $db = null;

    private function __construct() {
        $config = require __DIR__ . '/../config/db.php';
        $this->db = new mysqli(
            hostname: $config['host'],
            username: $config['username'],
            password: $config['password'],
            database: $config['database'],
            port: $config['port']
        );
        
        if ($this->db->connect_error) {
            throw new Exception("Database connection error");
        }

        $this->db->set_charset($config['charset']);
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli {
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
        self::$instance = null;
        $this->db = null;
    }
}