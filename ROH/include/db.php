<?php
// include/db.php - Improved with better locking handling

class ROHDatabase {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/../RohData.sql3';
        
        $this->db = new SQLite3($dbPath);
        $this->db->enableExceptions(true);
        
        // Important settings for concurrency
        $this->db->exec('PRAGMA journal_mode = WAL;');
        $this->db->exec('PRAGMA busy_timeout = 5000;');   // Wait up to 5 seconds
        $this->db->exec('PRAGMA foreign_keys = ON;');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ROHDatabase();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->lastErrorMsg());
        }

        foreach ($params as $key => $value) {
            $type = is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
            $stmt->bindValue($key, $value, $type);
        }

        return $stmt->execute();
    }

    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();
        return $row ?: null;
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        $result->finalize();
        return $rows;
    }

    public function execute($sql, $params = []) {
        $result = $this->query($sql, $params);
        $result->finalize();
        return $this->db->changes();
    }
}

function db() {
    return ROHDatabase::getInstance();
}
?>
