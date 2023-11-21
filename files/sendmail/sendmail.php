<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Create a PDO instance
$host = 'dedi59.jnb1.host-h.net';
$dbname = 'choicygasd_db1';
$tableName = 'form';
$user = 'choicygasd_1';
$password = 'zJ1Pxq7gH2KLp71JU3H8';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect. " . $e->getMessage());
}

// Initialize PHPMailer
$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('en', 'phpmailer/language/');
$mail->IsHTML(true);

// SMTP
$mail->isSMTP();
$mail->Host       = 'smtp.choicepack.co.za';
$mail->SMTPAuth   = true;
$mail->Username   = 'info@choicepack.co.za';
$mail->Password   = 'LetsTryThis2!';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 465;

// Set the sender and recipient
$mail->setFrom('info@choicepack.co.za', 'Choice Pack');
$mail->addAddress('info@choicepack.co.za');
$mail->Subject = 'Hello!';

// Process and sanitize POST data
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Build the HTML body
$body = '<h1>Form submission from Choicepack website</h1>';
$body .= '<table border="1">
    <tr>
        <th style="padding: 5px 10px">name</th>
        <th style="padding: 5px 10px">email</th>
        <th style="padding: 5px 10px">message</th>
    </tr>
    <tr>
        <td style="padding: 5px 10px">' . $name . '</td>
        <td style="padding: 5px 10px">' . $email . '</td>
        <td style="padding: 5px 10px">' . $message . '</td>
    </tr>
</table>';

$mail->Body = $body;

// Attempt to insert data into the database
try {
    $stmt = $pdo->prepare("INSERT INTO $tableName (name, email, message) VALUES (:name, :email, :message)");
    $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
    $message = 'The data is sent and saved to the database!';
} catch (PDOException $e) {
    $message = 'Error: ' . $e->getMessage();
}

// Send the email
if (!$mail->send()) {
    $message = 'Error';
} else {
    $message = 'The data is sent!';
}

$response = ['message' => $message];

header('Content-type: application/json');
echo json_encode($response);
?>
