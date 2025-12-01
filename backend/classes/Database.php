<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    public static function connect() {
        try {
            $conn = new PDO(
                "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME,
                Config::$DB_USER,
                Config::$DB_PASS
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
}
?>