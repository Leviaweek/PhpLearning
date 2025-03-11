<?php 
class Database {
    private SQLite3 $db;
    private string $migrationsPath;

    public function __construct(string $dbFile = "../data/database.sqlite", string $migrationsPath = ".") {
        $this->db = new SQLite3($dbFile);
        $this->migrationsPath = $migrationsPath;
        $this->applyMigrations();
    }

    private function applyMigrations() {
        if (!is_dir($this->migrationsPath)) {
            echo "Миграции не найдены. Создайте папку '$this->migrationsPath' и добавьте SQL-файлы.\n";
            return;
        }

        $files = glob($this->migrationsPath . "/*.sql");
        sort($files);

        foreach ($files as $file) {
            $sql = file_get_contents($file);
            $this->db->exec($sql);
            echo "Применена миграция: " . basename($file) . "\n";
        }
    }

    public function getConnection(): SQLite3 {
        return $this->db;
    }

    public function close() {
        $this->db->close();
    }
}

$db = new Database();
$db->close();
