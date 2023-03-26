#Emerybeans is a PHP website that lets you upload pictures and share them with friends and family. The site features an invite-only system that allows you to control who can view the pictures.

I'm building this site in part to learn PHP.

To configure your site, you will need to update two files in the 'classes' folder: Database.php and Mailer.php. 

Database.php contains the connection information to your MySQL database. You can find the 'CREATE' statements used to set up the tables in your database in the setup.php file. My intention is for setup.php to allow the initial install and configuration, but currently does nothing other than hold the 'CREATE' statements. 

Mailer.php contains the SMTP connection information needed to send emails. Emails are a core component of the application, so you will need to set this up properly.

Next you will need to add some records to your MySQL database: Initial admin user and Site settings.

You'll need to use something like PhpMyAdmin to insert an initial user in the 'user' table with both the enabled and the inviteAuthorized flags set to 1. To set the password, use the recover password feature on the login page.

For the Site settings, use something like PhpMyAdmin to insert records in the 'site' table. You'll need to add a system email address, the site name, and the site URL. The site name is used throughout the application. The site URL and system email address are used for the invite and recovery emails.