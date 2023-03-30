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

    // Site Statistics
    public function getCountOfActiveUsersToday() {
        $query = "SELECT COUNT(id) AS ActiveTodayUsers FROM user WHERE lastLoginTimestamp > DATE_SUB(NOW(), INTERVAL 1 DAY);";
        return $this->getCountOf($query);
    }

    public function getCountOfTotalUsers() {
        $query = "SELECT COUNT(id) as TotalUsers FROM user;";
        return $this->getCountOf($query);
    }

    public function getCountOfEnabledUsers() {
        $query = "SELECT COUNT(id) as EnabledUsers FROM user WHERE enabled = 1;";
        return $this->getCountOf($query);
    }

    public function getCountOfEntriesCreatedToday() {
        $query = "SELECT COUNT(id) AS EntriesCreatedToday FROM entry WHERE uploadTimestamp > DATE_SUB(NOW(), INTERVAL 1 DAY);";
        return $this->getCountOf($query);
    }

    public function getCountOfTotalEntries() {
        $query = "SELECT COUNT(id) as TotalEntries FROM entry;";
        return $this->getCountOf($query);
    }

    public function getMostLikedEntry() {
        $query = "SELECT CONCAT(e.Comments, ' (', DATE_FORMAT(e.uploadTimestamp, '%Y-%m-%d'), ')') FROM (SELECT el.entryId, COUNT(el.entryId) entryCount FROM `entry` AS e JOIN `entrylike` AS el WHERE e.id = el.entryId GROUP BY el.entryId ORDER BY entryCount DESC LIMIT 1) as results JOIN `entry` e WHERE results.entryId = e.id;";
        $results = "";
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $fetch = $result->fetch_array();
                $results = $fetch[0];
            }
            $stmt->close();
        }
        return $results;
    }

    private function getCountOf($query) {
        $returnCount = -1;
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $fetch = $result->fetch_array();
                $returnCount = $fetch[0];
            }
            $stmt->close();
        }
        return $returnCount;
    }
}
?>