<?php
error_reporting(0); // Suppress all error messages
// ini_set('display_errors', 0); // Optionally, you can uncomment this line as well.

include 'db.php'; // Include your database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $emails = $_POST['emails']; // Get the emails input
    $message = $_POST['message'];

    // Prepare the email subject and body
    $subject = "Forwarded Report: $report_id";
    $body = "Report ID: $report_id\nMessage: $message\n";

    $evidenceFiles = [];
    // Fetch report details from the database
    $report_details_sql = "SELECT evidence1, evidence2, evidence3, evidence4, evidence5, name, phone, email AS reporter_email, department, anonymous FROM reports WHERE report_id = ?";
    $stmt = $conn->prepare($report_details_sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        foreach (['evidence1', 'evidence2', 'evidence3', 'evidence4', 'evidence5'] as $evidenceField) {
            if (!empty($row[$evidenceField])) {
                $evidenceFiles[] = $row[$evidenceField];
            }
        }

        if (!$row['anonymous']) {
            $body .= "Name: " . htmlspecialchars($row['name']) . "\n";
            $body .= "Phone: " . htmlspecialchars($row['phone']) . "\n";
            $body .= "Email: " . htmlspecialchars($row['reporter_email']) . "\n";
            $body .= "Department: " . htmlspecialchars($row['department']) . "\n";
        }
    }

    // Set up PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'aamustedfinalyearproject@gmail.com'; // Your SMTP username
        $mail->Password = 'gfgj fwif zrnm fwvx'; // Your SMTP password (app password)
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->Timeout = 30; // Set timeout

        // Enable debugging output
        // $mail->SMTPDebug = 2; // You can comment this out if you don't want debugging info

        // Recipients: Split the emails by comma and add each as a recipient
        $emailList = explode(',', $emails);
        foreach ($emailList as $recipient) {
            $mail->addAddress(trim($recipient)); // Trim whitespace from each email
        }

        // Attach files
        foreach ($evidenceFiles as $filePath) {
            $mail->addAttachment('uploads/' . htmlspecialchars(basename($filePath))); // Attach the file
        }

        // Content
        $mail->isHTML(false); // Set email format to plain text
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "<script>
                alert('Report forwarded successfully!');
                setTimeout(function() {
                    window.location.href = 'dashboard_admin.php'; // Change to your forward page URL
                }, 100); // Redirect 
              </script>";
    } catch (Exception $e) {
        echo "<script>
                alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
                setTimeout(function() {
                    window.location.href = 'dashboard_admin.php'; // Change to your forward page URL
                }, 100); // Redirect 
              </script>";
    }
}

$conn->close();
?>
