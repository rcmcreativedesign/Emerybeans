<?php
class User {
    private $conn;
    private $table_name = "user";

    public $id;
    public $emailAddress;
    public $displayName;
    public $pwHash;
    public $hashSeed;
    public $lastLoginTimestamp;
    public $createdTimestamp;
    public $enabled;
    public $inviteAuthorized;
    public $recoveryHash;
    public $recoverySendDate;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser($emailaddress, $password, $displayname) {
        $this->emailAddress = $emailaddress;
        $this->displayName = $displayname;
        $this->hashSeed = $this->getGUID();
        $this->pwHash = $this->createPasswordHash($this->emailAddress, $password, $this->hashSeed);

        if ($this->saveUser()) {
            return $this->setUserById($this->id);
        } else {
            return false;
        }
    }

    public function setUserById($id) {
        $sql = "SELECT id, emailAddress, displayName, pwHash, hashSeed, lastLoginTimestamp, createdTimestamp, enabled, inviteAuthorized, recoveryHash, recoverySentDate FROM user WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->id, $this->emailAddress, $this->displayName, $this->pwHash, $this->hashSeed, $this->lastLoginTimestamp, $this->createdTimestamp, $this->enabled, $this->inviteAuthorized, $this->recoveryHash, $this->recoverySendDate);
                $stmt->fetch();
                $stmt->close();
                if (!$this->id) {
                    return false;
                } else {
                    return true;
                }
            }else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    public function setUserByEmailAddress($emailaddress) {
        $sql = "SELECT id, emailAddress, displayName, pwHash, hashSeed, lastLoginTimestamp, createdTimestamp, enabled, inviteAuthorized, recoveryHash, recoverySentDate FROM user WHERE emailAddress = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $emailaddress);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->id, $this->emailAddress, $this->displayName, $this->pwHash, $this->hashSeed, $this->lastLoginTimestamp, $this->createdTimestamp, $this->enabled, $this->inviteAuthorized, $this->recoveryHash, $this->recoverySendDate);
                $stmt->fetch();
                $stmt->close();
                if (!$this->id) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    public function setUserByRecoveryHash($recoveryHash) {
        $sql = "SELECT id, emailAddress, displayName, pwHash, hashSeed, lastLoginTimestamp, createdTimestamp, enabled, inviteAuthorized, recoveryHash, recoverySentDate FROM user WHERE recoveryHash = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $recoveryHash);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->id, $this->emailAddress, $this->displayName, $this->pwHash, $this->hashSeed, $this->lastLoginTimestamp, $this->createdTimestamp, $this->enabled, $this->inviteAuthorized, $this->recoveryHash, $this->recoverySendDate);
                $stmt->fetch();
                $stmt->close();
                if (!$this->id) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    public function saveUser() {
        if ($this->id === null) {
            $sql = "INSERT INTO user (emailAddress, displayName, pwHash, hashSeed, enabled, inviteAuthorized) VALUES (?, ?, ?, ?, 1, 0)";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("ssss", $this->emailAddress, $this->displayName, $this->pwHash, $this->hashSeed);
                if ($stmt->execute()) {
                    $this->id = $this->conn->insert_id;
                } else {
                    $stmt->close();
                    return false;
                }
                $stmt->close();
            } else {
                return false;
            }
        } else {
            $sql = "UPDATE user SET emailAddress = ?, displayName = ?, pwHash = ?, hashSeed = ?, lastLoginTimestamp = ?, createdTimestamp = ?, enabled = ?, inviteAuthorized = ?, recoveryHash = ?, recoverySentDate = ? WHERE id = ?";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("ssssssiissi", $this->emailAddress, $this->displayName, $this->pwHash, $this->hashSeed, $this->lastLoginTimestamp, $this->createdTimestamp, $this->enabled, $this->inviteAuthorized, $this->recoveryHash, $this->recoverySendDate, $this->id);
                if (!$stmt->execute()) {
                    $stmt->close();
                    return false;
                }
                $stmt->close();
            } else {
                return false;
            }

        }
        return true;
    }

    public function deleteUser() {
        $sqlDelete = "DELETE u, el, ev FROM user u LEFT JOIN entrylike el ON u.id = el.userId LEFT JOIN entryview ev on u.id = ev.userId where u.id = ?";
        if ($stmt = $this->conn->prepare($sqlDelete)) {
            $bind = $stmt->bind_param("i", $this->id);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            } else {
                $stmt->close();
                return true;
            }
        }
        return false;
    }

    public function validatePassword($password) {
        $pwString = $this->emailAddress . $password . $this->hashSeed;
        if(sha1($pwString) == $this->pwHash)
            return true;
        return false;
    }

    public function logAccess() {
        $this->lastLoginTimestamp = date("Y-m-d H:i:s");
        $this->saveUser();
    }


    public function generatePassword() {
        $password = "Emerybeans" . rand(100000, 999999);
        return $password;
    }

    public function createPasswordHash($emailaddress, $password, $hashseed) {
        $passwordhash = sha1($emailaddress . $password . $hashseed);
        return $passwordhash;
    }

    public function createRecoveryHash() {
        $recoverHash = sha1($this->getGUID());
        return $recoverHash;
    }

    public function createHashSeed() {
        return $this->getGUID();
    }

    private function getGUID(){
        $charid = strtolower(md5(uniqid(rand(), true)));
        return $charid;
    }
}
?>