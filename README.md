
## Step To Setup
(1) Run "composer install" <br />
(2) Change the Database Credentials in .env File <br />
(3) Run "php artisan passport:install" </br>
(4) To migrate run "php artisan migrate" <br />
(5) You need to configure the .env file with your smtp credentials for email feature else you can give <b>log<b> driver at MAIL_MAILER variable. <br />
(6) Run in New Terminal "php artisan serve" <br />
(7) import the postman route collection which available in root directory which name "Auth.postman_collection.json"<br>
