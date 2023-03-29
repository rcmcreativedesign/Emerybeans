<?php
class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOpenInvites() {
        $query = "SELECT id, emailAddress, displayName, createdTimestamp FROM user WHERE lastLoginTimestamp IS NULL ORDER BY createdTimestamp;";
        $results = array();
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $stmtresult = $stmt->get_result();

                if ($stmtresult->num_rows > 0) {
                    while ($stmtresult_val = $stmtresult->fetch_assoc()) {
                        $results[] = array($stmtresult_val["id"], $stmtresult_val["emailAddress"], $stmtresult_val["displayName"], $stmtresult_val["createdTimestamp"]);
                    }
                }
            }
            $stmt->close();
        }
        return $results;
    }

    public function getAllUsers() {
        $query = "SELECT id, emailAddress, displayName, lastLoginTimestamp, enabled, inviteAuthorized FROM user ORDER BY displayName;";
        $results = array();
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $stmtresult = $stmt->get_result();

                if ($stmtresult->num_rows > 0) {
                    while ($stmtresult_val = $stmtresult->fetch_assoc()) {
                        $results[] = array($stmtresult_val["id"], $stmtresult_val["emailAddress"], $stmtresult_val["displayName"], $stmtresult_val["lastLoginTimestamp"], $stmtresult_val["enabled"], $stmtresult_val["inviteAuthorized"]);
                    }
                }
            }
            $stmt->close();
        }
        return $results;
    }

    public function getCountOfAdmins() {
        $query = "SELECT COUNT(id) adminCount FROM user WHERE admin = 1";
        $adminCount = -1;
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                $fetch = $result->fetch_assoc();
                $adminCount = $fetch["adminCount"];
            }
            $stmt->close();
        }
        return $adminCount;
    }
}
?>