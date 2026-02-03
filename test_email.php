<?php
require 'php/config.php';
$result = sendEmail('raviranjankashyap7@gmail.com', 'Test Email', '<h2>Test</h2><p>This is a test email from InnovateHub.</p>');
echo $result ? "Email sent successfully!" : "Failed to send email.";
?>