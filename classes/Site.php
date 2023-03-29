<?php
class Site {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->loadSite();
    }

    public $siteName;
    public $siteUrl;
    public $adminEmailAddress;

    public function saveSite() {
        $sql = "UPDATE site SET siteName = ?, siteUrl = ?, adminEmailAddress = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sss", $this->siteName, $this->siteUrl, $this->adminEmailAddress);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $stmt->close();
        } else {
            return false;
        }
        return true;
    }

    public function createSite() {
        $sql = "INSERT INTO site (siteName, siteUrl, adminEmailAddress) VALUES (?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sss", $this->siteName, $this->siteUrl, $this->adminEmailAddress);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $stmt->close();
        } else {
            return false;
        }
        return true;
    }

    private function loadSite() {
        $sql = "SELECT siteName, siteUrl, adminEmailAddress FROM site";
        if ($stmt = $this->conn->prepare($sql)) {
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->siteName, $this->siteUrl, $this->adminEmailAddress);
                $stmt->fetch();
                $stmt->close();
            }else {
                $stmt->close();
            }
        }
    }
}
?>