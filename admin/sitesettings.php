<?php
require_once '../_authorized.php';

require_once "../classes/Database.php";
require_once '../classes/Site.php';

$database = new Database();
$db = $database->getConnection();
$site = new Site($db);

$siteName = $siteUrl = $adminEmailAddress = "";
$siteName_err = $siteUrl_err = $adminEmailAddress_err = "";
$posted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $posted = true;
    $siteName = getPostOrEmpty("siteName");
    if (empty($siteName))
        $siteName_err = "Please enter a value";
    $siteUrl = getPostOrEmpty("siteUrl");
    if (empty($siteUrl))
        $siteUrl_err = "Please enter a value";
    $adminEmailAddress = getPostOrEmpty("adminEmailAddress");
    if (empty($adminEmailAddress))
        $adminEmailAddress_err = "Please enter a value";

    if (empty($siteName_err) && empty($siteUrl_err) && empty($adminEmailAddress_err)) {
        $site->siteName = $siteName;
        $site->siteUrl = $siteUrl;
        $site->adminEmailAddress = $adminEmailAddress;
        $site->saveSite();
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

        <link href="../site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
    </head>
    <body>
        <div class="wrapper container">
            <?php include '../navbar.php'; ?>
            <h2>Welcome to <?php echo $site->siteName?>!</h2>
            <p>Manage Site</p>

            <form id="inviteform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row form-group">
                    <label class="col-sm-2" for="siteName">Site Name:</label>
                    <div class="col-sm-4">
                        <input type="text" id="siteName" name="siteName" value="<?php echo $posted ? $siteName : $site->siteName;?>" class="form-control <?php echo (!empty($siteName_err)) ? 'is-invalid' : ''; ?>" />
                        <span class="invalid-feedback"><?php echo $siteName_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2" for="siteUrl">Site URL:</label>
                    <div class="col-sm-4">
                        <input tpe="text" name="siteUrl" value="<?php echo $posted ? $siteUrl : $site->siteUrl;?>" class="form-control <?php echo (!empty($siteUrl_err)) ? 'is-invalid' : ''; ?>" />
                        <span class="invalid-feedback"><?php echo $siteUrl_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-2" for="adminEmailAddress">Admin Email Address:</label>
                    <div class="col-sm-4">
                        <input type="text" name="adminEmailAddress" value="<?php echo $posted ? $adminEmailAddress : $site->adminEmailAddress;?>" class="form-control <?php echo (!empty($adminEmailAddress_err)) ? 'is-invalid' : ''; ?>" />
                        <span class="invalid-feedback"><?php echo $adminEmailAddress_err; ?></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-12"><input class="btn btn-primary" type="submit" name="submit" value="Submit" /></div>
                </div>
            </form>

        </div>
    </body>
</html>