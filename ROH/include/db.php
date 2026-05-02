<?php
// include/db.php - Secure Database Connection

class ROHDatabase {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/../RohData.sql3';   // Adjust path if needed
        // $dbPath = __DIR__ . '/../bin/debug/RohData.sql3'; // alternative

        $this->db = new SQLite3($dbPath);
        $this->db->enableExceptions(true);
        $this->db->exec('PRAGMA foreign_keys = ON;');
        $this->db->exec('PRAGMA journal_mode = WAL;'); // Better concurrency
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

    // Secure query helper (prepared statement)
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->lastErrorMsg());
        }

        foreach ($params as $key => $value) {
            $type = is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
            $stmt->bindValue($key, $value, $type);
        }

        $result = $stmt->execute();
        $stmt->close(); // Good practice
        return $result;
    }

    // Fetch single row as assoc array
    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();
        return $row ?: null;
    }

    // Fetch all rows
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
        $this->query($sql, $params); // For INSERT/UPDATE/DELETE
        return $this->db->changes();
    }
}

// Global helper (optional - for backward compatibility)
function db() {
    return ROHDatabase::getInstance();
}
?>
