<?php
class UserPreferance {
    private $conn;
    private $table_name = "userpreferance";

    public $PageSize = 10;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function LoadPrefs($link, $id) {

    }
}
?>