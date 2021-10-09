<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."Exception.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."PHPMailer.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."SMTP.php";

class Mailer {
    private $conn;

    public $errorMessage;

    public function __construct($db) {
        $this->conn = $db;
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

            $mail->setFrom('', '');
            $mail->addAddress($emailaddress);

            $mail->isHTML(true);
            $mail->Subject = 'Invite to Emerybeans';
            $mail->Body = '<html>'
                . '<head>'
                . '<title>Emerybeans Invite</title>'
                . '</head>'
                . '<body>'
                . '<h2>You are invited to join Emerybeans.</h2>'
                . '<p>Emerybeans is like that site with a similar name, but it\'s built by me, and free for me to use. It\'s still a work in '
                . 'progress, but it\'s working well enough to start inviting family to join.</p>'
                . '<p>You can log into the website using your email address and the provided password:</p>'
                . '<p><a href="http://emerybeans.epizy.com/">http://emerybeans.epizy.com</a></p>'
                . '<p>Password: ' . $password . '</p>'
                . '</body>'
                . '</html>';
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->errorMessage = $mail->ErrorInfo;
            return false;
        }
        return false;
    }
}
?>