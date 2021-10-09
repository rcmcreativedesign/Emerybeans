<?php
class Database {
    private $host = "";
    private $db_name = "";
    private $username = "";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        } catch (Exception $e) {
            die("ERROR: Could not connect.");
        }

        return $this->conn;
    }
}
?>