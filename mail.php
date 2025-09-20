<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Debug: check if API key is loaded
if (empty($_ENV['MAILERSEND_API_KEY'])) {
    die("MailerSend API key not found. Check your .env file.");
}

$mailersend = new MailerSend([
    'api_key' => $_ENV['MAILERSEND_API_KEY']
]);

$recipients = [
    new Recipient('onestoppanel2023@gmail.com', 'one stop panel')
];

$emailParams = (new EmailParams())
    ->setFrom('no-reply@gmail.com')
    ->setFromName('One Stop Panel')
    ->setRecipients($recipients)
    ->setSubject('Appointment Confirmation')
    ->setHtml('<p>Hello from MailerSend!</p>')
    ->setText('Hello from MailerSend!');

$response = $mailersend->email->send($emailParams);

echo "<pre>";
print_r($response);
echo "</pre>";
?>