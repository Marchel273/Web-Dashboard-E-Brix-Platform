<?php
// php_web_dashboard/config/database.php

class Database {
    private $host = "localhost";
    private $port = "5432";
    private $db_name = "ebrix_db";
    private $username = "postgres";
    private $password = "postgres";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // Silently allow fallback if PostgreSQL instance is not yet online locally
            $this->conn = null;
        }
        return $this->conn;
    }
}
?>
