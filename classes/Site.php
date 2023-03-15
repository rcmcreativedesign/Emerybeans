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