<?php
// include/db.php - Secure Database Connection (Fixed)

class ROHDatabase {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/../RohData.sql3';
        $this->db = new SQLite3($dbPath);
        $this->db->enableExceptions(true);
        $this->db->exec('PRAGMA foreign_keys = ON;');
        $this->db->exec('PRAGMA journal_mode = WAL;');
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

    // Execute query and return result object
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->lastErrorMsg());
        }

        foreach ($params as $key => $value) {
            $type = is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
            $stmt->bindValue($key, $value, $type);
        }

        return $stmt->execute();   // Returns SQLite3Result
    }

    // Fetch single row
    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();   // Safe here for single row
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

    // Execute non-select (INSERT/UPDATE/DELETE)
    public function execute($sql, $params = []) {
        $result = $this->query($sql, $params);
        $result->finalize();
        return $this->db->changes();
    }
}

// Helper
function db() {
    return ROHDatabase::getInstance();
}
?>
