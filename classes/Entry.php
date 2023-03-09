<?php
class Entry {
    private $conn;
    private $table_name = "entry";

    public $id;
    public $uploadTimestamp;
    public $comments;
    public $imageBinary;
    public $fileExtension;
    public $imageType;
    public $type;
    public function uploadTimestampDisplay() {
        return ($this->uploadTimestamp) ? date_create($this->uploadTimestamp)->modify("-2 hours") : null;
    }

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createEntry($comments, $imagebinary, $fileextension, $imagetype, $type) {
        $this->comments = $comments;
        $this->imageBinary = $imagebinary;
        $this->fileExtension = $fileextension;
        $this->imageType = $imagetype;
        $this->type = $type;

        if ($this->saveEntry()) {
            return $this->setEntryById($this->id);
        } else {
            return false;
        }
    }

    public function setEntryById($id) {
        $sql = "SELECT id, uploadTimestamp, comments, imageBinary, fileExtension, imageType, type FROM entry WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->id, $this->uploadTimestamp, $this->comments, $this->imageBinary, $this->fileExtension, $this->imageType, $this->type);
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

    public function saveEntry() {
        if ($this->id === null) {
            $sql = "INSERT INTO entry (comments, imageBinary, fileExtension, imageType, type) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("sssis", $this->comments, $this->imageBinary, $this->fileExtension, $this->imageType, $this->type);
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
            $sql = "UPDATE entry SET uploadTimestamp = ?, comments = ?, imageBinary = ?, fileExtension = ?, imageType = ?, type = ? WHERE id = ?";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("ssbsisi", $this->uploadTimestamp, $this->comment, $this->imageBinary, $this->fileExtension, $this->imageType, $this->type, $this->id);
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

    public function deleteEntry() {
        $sql = "DELETE entry WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $this->$id);
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

    public function setEntryLiked($userId) {
        return $this->setMeta("INSERT INTO entrylike (entryid, userid) VALUES (?, ?)", $userId);
    }
    
    public function setEntryViewed($userId)  {
        return $this->setMeta("INSERT INTO entryview (entryId, userId) VALUES (?, ?)", $userId);
    }

    private function setMeta($query, $userId) {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $userId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }    
    }

    public function hasLiked($userId) {
        return $this->getMeta("SELECT COUNT(timestamp) AS checkCount FROM entrylike WHERE entryId = ? AND userId = ?", $userId);
    }

    public function hasViewed($userId) {
        return $this->getMeta("SELECT COUNT(timestamp) AS checkCount FROM entryview WHERE entryId = ? AND userId = ?", $userId);
    }

    private function getMeta($query, $userId) {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $this->id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc();
        $meta = $count["checkCount"] > 0;
        $stmt->close();

        return $meta;
    }
 
    public function getLikedList() {
        $likedresults = array();
        $userlikes_query = "SELECT CONCAT(CASE WHEN u.displayName IS NULL OR u.displayName = '' THEN u.emailAddress ELSE u.displayName END, ' liked this') AS liked FROM `user` AS u LEFT JOIN `entrylike` AS el ON u.id = el.userId WHERE el.entryId = ?";
        if ($stmt = $this->conn->prepare($userlikes_query)) {
            $stmt->bind_param("i", $this->id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
    
                if ($result->num_rows > 0) {
                    while ($userlikes_val = $result->fetch_assoc()) {
                        $likedresults[] = $userlikes_val["liked"];
                    }
                }
            }
            $stmt->close();
        }
        return $likedresults;
    
    }

    public function getImage() {
        $image = "";
        $imageQuery = "SELECT imageBinary, type FROM entry WHERE id = ?";
        if ($stmt = $this->conn->prepare($imageQuery)) {
            $stmt->bind_param("i", $this->id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                $imageFetch = $result->fetch_assoc();
                $image = $imageFetch["imageBinary"];
            }
            $stmt->close();
        }
        return $image;
    }
}
?>