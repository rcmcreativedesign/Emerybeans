<?php
require_once '_authorized.php';

require_once "classes/Database.php";
require_once 'classes/Site.php';
require_once "classes/Entry.php";

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);
$entry = new Entry($db);

$caption = "";
$upload_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["upload"])) {
        $caption = $_POST["caption"];

        $type = $_FILES['uploadImage']['type'];
        $imageFileType = strtolower(pathinfo($_FILES["uploadImage"]["tmp_name"], PATHINFO_EXTENSION));
        if (file_exists($_FILES["uploadImage"]["tmp_name"]) && $file_contents = file_get_contents($_FILES["uploadImage"]["tmp_name"])) {
            $image_base64 = base64_encode($file_contents);
            if (strlen($image_base64) > 0) {
                $image = "data:" . $_FILES["uploadImage"]["type"] . ";base64," . $image_base64;
                if ($entry->createEntry($caption, $image, $imageFileType, 0, $type)) {
                    header('location: index.php');
                    exit;            
                } else {
                    $upload_err = "File upload failed. Error message: ({$db->errno}) " . $db->error;
                }
            } else {
                $upload_err = "File upload failed. Please submit it again.";
            }
        } else {
            $upload_err = "File upload failed. Unable to get contents.";
        }
    }
}
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $site->siteName?></title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <style>
            #imagePreview { max-width: 300px; }
            .caption { min-width: 300px; }
        </style>
        <script type="text/javascript">
            function preview_image(e) {
                var reader = new FileReader();
                reader.onload = function() {
                    var preview = document.getElementById('imagePreview');
                    preview.src = reader.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
            </script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>

            <?php
            if(!empty($upload_err)) {
                echo '<div class="alert alert-danger">' . $upload_err . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <img id="imagePreview"/>
                <div class="form-group">
                    <label>Select picture or video</label><br/>
                    <input type="file" name="uploadImage" onchange="preview_image(event)" />
                </div>
                <div class="form-group">
                    <label>Caption</label><br/>
                    <textarea name="caption" rows="3" class="caption"><?php echo $caption?></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" name="upload" value="Upload Image"/>
                </div>
            </form>
        </div>
    </body>
</html>