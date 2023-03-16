<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."Exception.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."PHPMailer.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."SMTP.php";

class Mailer {
    private $conn;
    private $site;

    public $errorMessage;

    public function __construct($db, $site) {
        $this->conn = $db;
        $this->site = $site;
    }

    public function sendInvite($emailaddress, $password) {
        // Send invite email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = '';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($this->site->adminEmailAddress, $this->site->siteName . ' Admin');
            $mail->addAddress($emailaddress);

            $mail->isHTML(true);
            $mail->Subject = 'Invite to ' . $this->site->siteName;
            $mail->Body = '<html>'
                . '<head>'
                . '<title>' . $this->site->siteName . ' Invite</title>'
                . '</head>'
                . '<body>'
                . '<h2>You are invited to join ' . $this->site->siteName . '.</h2>'
                . '<p>' . $this->site->siteName . ' is like that site with a similar name, but it\'s built by me, and free for me to use. It\'s still a work in '
                . 'progress, but it\'s working well enough to start inviting family to join.</p>'
                . '<p>You can log into the website using your email address and the provided password:</p>'
                . '<p><a href="' . $this->site->siteUrl . '/">' . $this->site->siteUrl . '</a></p>'
                . '<p>Password: ' . $password . '</p>'
                . '</body>'
                . '</html>';
            var_dump($mail->Body);
            //$mail->send();
            return true;
        } catch (Exception $e) {
            $this->errorMessage = $mail->ErrorInfo;
            return false;
        }
        return false;
    }

    public function sendRecovery($emailaddress, $recoveryHash) {
        // Send invite email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = '';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($this->site->adminEmailAddress, $this->site->siteName . ' Admin');
            $mail->addAddress($emailaddress);

            $mail->isHTML(true);
            $mail->Subject = $this->site->siteName . ' - Recover Password';
            $mail->Body = '<html>'
                . '<head>'
                . '<title>' . $this->site->siteName . ' Recover Password</title>'
                . '</head>'
                . '<body>'
                . '<h2>You have requested to reset your password.</h2>'
                . '<p>Please use this link to reset your password:</p>'
                . '<p><a href="' . $this->site->siteUrl . '/recoverpassword.php?recoverHash=' . $recoveryHash . '">Click Here</a></p>'
                . '</body>'
                . '</html>';
            var_dump($mail->Body);
            //$mail->send();
            return true;
        } catch (Exception $e) {
            $this->errorMessage = $mail->ErrorInfo;
            return false;
        }
        return false;
    }
}
?>