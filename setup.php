<?php
    require_once '_globals.php';
    require_once "classes/Database.php";
    require_once 'classes/Site.php';
    require_once 'classes/User.php';

    $database = new Database();
    $db = $database->getConnection();

    $siteName = $siteUrl = $adminEmailAddress = $userDisplayName = $userEmailAddress = $userPassword = "";
    $siteName_err = $siteUrl_err = $adminEmailAddress_err = $userDisplayName_err = $userEmailAddress_err = $userPassword_err = "";

    // Check all the tables
    $tables = Array("entry", "user", "entryview", "entrylike", "site");
    $foundCount = 0;
    for ($i = 0; $i < count($tables); $i++) {
//echo 'tableExists('.$tables[$i].'): '.tableExists($tables[$i], $db).'<br/>';
        if (tableExists($tables[$i], $db))
            $foundCount++;
    }
//echo 'foundCount: '.$foundCount.'<br/>';
//echo 'count(tables): '.count($tables).'<br/>';
    if ($foundCount == count($tables)) {
        $db->close();
        header("location: index.php");
        exit;
    }
    
    // Check if data was posted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $siteName = getPostOrEmpty("siteName");
        if (empty($siteName))
            $siteName_err = "Please enter a value";
        $siteUrl = getPostOrEmpty("siteUrl");
        if (empty($siteUrl))
            $siteUrl_err = "Please enter a value";
        $adminEmailAddress = getPostOrEmpty("adminEmailAddress");
        if (empty($adminEmailAddress))
            $adminEmailAddress_err = "Please enter a value";
        $userDisplayName = getPostOrEmpty("userDisplayName");
        if (empty($userDisplayName))
            $userDisplayName_err = "Please enter a value";
        $userEmailAddress = getPostOrEmpty("userEmailAddress");
        if (empty($userEmailAddress))
            $userEmailAddress_err = "Please enter a value";
        $userPassword = getPostOrEmpty("userPassword");
        if (empty($userPassword))
            $userPassword_err = "Please enter a value";

        if (empty($siteName_err) && empty($siteUrl_err) && empty($adminEmailAddress_err) && empty($userDisplayName_err) && empty($userEmailAddress_err) && empty($userPassword_err)) {
            for ($i = 0; $i < count($tables); $i++) {
                if (!tableExists($tables[$i], $db))
                    createTable($tables[$i], $db);
            }

            $newSite = new Site($db);
            $newUser = new User($db);

            $newSite->siteName = $siteName;
            $newSite->siteUrl = $siteUrl;
            $newSite->adminEmailAddress = $adminEmailAddress;
            $newSite->saveSite();

            $newUser->createUser($userEmailAddress, $userPassword, $userDisplayName);
            $newUser->enabled = true;
            $newUser->inviteAuthorized = true;
            $newUser->saveUser();
        }
    }

    $site = new Site($db);

    $db->close();

    function tableExists($tableName, $db) {
        $checkQuery = "";
        switch ($tableName) {
            case "entry":
                $checkQuery = "DESCRIBE `entry`";
                break;
            case "user":
                $checkQuery = "DESCRIBE `user`";
                break;
            case "entryview":
                $checkQuery = "DESCRIBE `entryview`";
                break;
            case "entrylike":
                $checkQuery = "DESCRIBE `entrylike`";
                break;
            case "site":
                $checkQuery = "DESCRIBE `site`";
                break;
            default:
                return false;
        }

        $tableExists = false;
        if ($stmt = $db->prepare($checkQuery)) {
            $tableExists = $stmt->execute();
            $stmt->close();
        }
        return $tableExists;

    }

    function createTable($tableName, $db) {
        $checkQuery = "";
        switch ($tableName) {
            case "entry":
                $createQuery = "CREATE TABLE `entry` (`id` int(11) NOT NULL AUTO_INCREMENT,`uploadTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`comments` text,`imageBinary` longblob NOT NULL,`fileExtension` varchar(50) NOT NULL,`imageType` int(11) NOT NULL,`type` varchar(128) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;";
                break;
            case "user":
                $createQuery =  "CREATE TABLE `user` (`id` int(11) NOT NULL AUTO_INCREMENT, `emailAddress` text NOT NULL, `displayName` varchar(50) DEFAULT NULL, `pwHash` text NOT NULL, `hashSeed` text NOT NULL, `lastLoginTimestamp` timestamp NULL DEFAULT NULL, `createdTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `enabled` bit(1) NOT NULL, `inviteAuthorized` bit(1) NOT NULL DEFAULT b'0', `recoveryHash` text DEFAULT NULL, `recoverySentDate` timestamp NULL DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;";
                break;
            case "entryview":
                $createQuery = "CREATE TABLE `entryview` (`id` int(11) NOT NULL AUTO_INCREMENT,`entryId` int(11) NOT NULL,`userId` int(11) NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
                break;
            case "entrylike":
                $createQuery = "CREATE TABLE `entrylike` (`id` int(11) NOT NULL AUTO_INCREMENT,`entryId` int(11) NOT NULL,`userId` int(11) NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;";
                break;
            case "site":
                $createQuery = "CREATE TABLE `site` (`siteUrl` text NOT NULL, `siteName` text NOT NULL, `adminEmailAddress` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1";
                break;
        }

        $tableExists = tableExists($tableName, $db);

        if (!$tableExists) {
            $dbCreated = false;
            if ($stmt = $db->prepare($createQuery)) {
                $dbCreated = $stmt->execute();
                $stmt->close();
            }
            return $dbCreated;
        }else {
            return $tableExists;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Site Setup</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to <?php echo empty($site->siteName) ? "your new site" : $site->siteName?>!</h2>

            <?php if(!empty($site->siteName)) { ?>
            <div class="alert alert-success">Site created successfully</div>
            <?php } else { ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row form-group">
                    <div class="col-sm-12"><h5>Site Settings</h5></div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-11">
                        <div class="row form-group">
                            <label class="col-sm-2" for="siteName">Site Name:</label>
                            <div class="col-sm-4">
                                <input type="text" id="siteName" name="siteName" value="<?php echo $siteName;?>" class="form-control <?php echo (!empty($siteName_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $siteName_err; ?></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-2" for="siteUrl">Site URL:</label>
                            <div class="col-sm-4">
                                <input tpe="text" name="siteUrl" value="<?php echo $siteUrl;?>" class="form-control <?php echo (!empty($siteUrl_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $siteUrl_err; ?></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-2" for="adminEmailAddress">Admin Email Address:</label>
                            <div class="col-sm-2">
                                <input type="text" name="adminEmailAddress" value="<?php echo $adminEmailAddress;?>" class="form-control <?php echo (!empty($adminEmailAddress_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $adminEmailAddress_err; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-12"><h5>Initial User</h5></div>
                </div>
                <div class="row from-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-11">
                        <div class="row form-group">
                            <label class="col-sm-2" for="userDisplayName">Display Name:</label>
                            <div class="col-sm-2">
                                <input type="text" name="userDisplayName" value="<?php echo $userDisplayName;?>" class="form-control <?php echo (!empty($userDisplayName_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $userDisplayName_err; ?></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-2" for="userEmail">Email Address:</label>
                            <div class="col-sm-2">
                                <input type="text" name="userEmailAddress" value="<?php echo $userEmailAddress;?>" class="form-control <?php echo (!empty($userEmailAddress_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $userEmailAddress_err; ?></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-2" for="userPassword">Password:</label>
                            <div class="col-sm-2">
                                <input type="password" name="userPassword" value="<?php echo $userPassword;?>" class="form-control <?php echo (!empty($userPassword_err)) ? 'is-invalid' : ''; ?>" />
                                <span class="invalid-feedback"><?php echo $userPassword_err; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-12"><input type="submit" name="submit" value="Submit" /></div>
                </div>
            </form>
            <?php } ?>
        </div>
    </body>
</html>