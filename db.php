<?php
/**
 * Database Connection Class - DigitalKasur.com
 * Enhanced with error handling and logging
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            die("Database Connection Failed. Please try again later.");
        }
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

    private function __clone() {}

    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

class DB {
    private static $pdo;

    public static function init() {
        if (!self::$pdo) {
            self::$pdo = Database::getInstance()->getConnection();
        }
        return self::$pdo;
    }

    public static function selectOne($query, $params = []) {
        try {
            $stmt = self::init()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("DB selectOne Error: " . $e->getMessage());
            return false;
        }
    }

    public static function select($query, $params = []) {
        try {
            $stmt = self::init()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("DB select Error: " . $e->getMessage());
            return [];
        }
    }

    public static function insert($table, $data) {
        $keys = array_keys($data);
        $placeholders = array_fill(0, count($keys), '?');

        $query = "INSERT INTO `{$table}` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $placeholders) . ")";

        try {
            self::init()->prepare($query)->execute(array_values($data));
            return self::init()->lastInsertId();
        } catch (PDOException $e) {
            error_log("DB insert Error: " . $e->getMessage());
            return false;
        }
    }

    public static function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "`{$key}` = ?";
        }
        $setClause = implode(', ', $setClause);

        $query = "UPDATE `{$table}` SET {$setClause} WHERE {$where}";

        try {
            $stmt = self::init()->prepare($query);
            $stmt->execute(array_merge(array_values($data), $whereParams));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("DB update Error: " . $e->getMessage());
            return false;
        }
    }

    public static function delete($table, $where, $params = []) {
        $query = "DELETE FROM `{$table}` WHERE {$where}";

        try {
            $stmt = self::init()->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("DB delete Error: " . $e->getMessage());
            return false;
        }
    }

    public static function count($table, $where = "1=1", $params = []) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
            $result = self::selectOne($query, $params);
            return $result ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("DB count Error: " . $e->getMessage());
            return 0;
        }
    }

    public static function sum($table, $column, $where = "1=1", $params = []) {
        try {
            $query = "SELECT SUM({$column}) as total FROM {$table} WHERE {$where}";
            $result = self::selectOne($query, $params);
            return $result ? (float)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("DB sum Error: " . $e->getMessage());
            return 0;
        }
    }

    public static function query($query, $params = []) {
        try {
            $stmt = self::init()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("DB query Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
