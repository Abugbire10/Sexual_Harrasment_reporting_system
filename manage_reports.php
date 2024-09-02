<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data
    $report_id = $_POST['report_id'] ?? '';
    $admin_feedback = $_POST['feedback'] ?? '';
    $new_status = $_POST['status'] ?? '';

    // Define allowed statuses
    $allowed_statuses = ['under review', 'resolved'];

    // Debugging: Log incoming data
    error_log("DEBUG: Received Report ID: $report_id, New Status: $new_status, Feedback: $admin_feedback");

    // Check if required fields are present and status is valid
    if (empty($report_id) || empty($new_status) || !in_array($new_status, $allowed_statuses)) {
        error_log("ERROR: Missing report ID, status is missing, or invalid status provided.");
        echo "<script>alert('Report ID is missing or invalid status provided.'); window.location.href = 'dashboard_admin.php';</script>";
        exit;
    }

    // Start the transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL statement for updating the status and feedback
        $stmt = $conn->prepare("UPDATE reports SET status = ?, Admin_Feedback = ? WHERE report_id = ?");
        if (!$stmt) {
            error_log("ERROR: SQL statement preparation failed: " . $conn->error);
            throw new Exception("SQL statement preparation failed.");
        }

        // Bind parameters to the SQL statement
        if (!$stmt->bind_param("ssi", $new_status, $admin_feedback, $report_id)) {
            error_log("ERROR: Binding parameters failed: " . $stmt->error);
            throw new Exception("Binding parameters failed.");
        }

        // Execute the SQL statement
        if (!$stmt->execute()) {
            error_log("ERROR: SQL statement execution failed: " . $stmt->error);
            throw new Exception("SQL statement execution failed.");
        }

        // Debugging: Log affected rows
        error_log("DEBUG: Affected rows after status update: " . $stmt->affected_rows);

        if ($stmt->affected_rows === 0) {
            error_log("ERROR: No rows were updated. Possible invalid report ID or the status was already set to the desired value.");
            throw new Exception("No rows were updated.");
        }

        // Handle evidence file upload if any
        if (!empty($_FILES['evidence']['name'][0])) {
            foreach ($_FILES['evidence']['tmp_name'] as $index => $tmp_name) {
                if (is_uploaded_file($tmp_name)) {
                    $file_name = uniqid() . '_' . basename($_FILES['evidence']['name'][$index]);
                    $upload_dir = 'uploads/';
                    $target_file = $upload_dir . $file_name;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $stmt = $conn->prepare("UPDATE reports SET admin_evidence = CONCAT(IFNULL(admin_evidence, ''), ?, ',') WHERE report_id = ?");
                        if (!$stmt) {
                            error_log("ERROR: Evidence SQL statement preparation failed: " . $conn->error);
                            throw new Exception("Evidence SQL statement preparation failed.");
                        }
                        $stmt->bind_param("si", $target_file, $report_id);
                        if (!$stmt->execute()) {
                            error_log("ERROR: Evidence SQL statement execution failed: " . $stmt->error);
                            throw new Exception("Evidence SQL statement execution failed.");
                        }
                    } else {
                        error_log("ERROR: Error uploading evidence file: " . $file_name);
                        throw new Exception("Error uploading evidence file.");
                    }
                }
            }
        }

        // Commit the transaction if everything is successful
        if (!$conn->commit()) {
            error_log("ERROR: Transaction commit failed: " . $conn->error);
            throw new Exception("Transaction commit failed.");
        }

        // Log success message
        error_log("DEBUG: Transaction committed successfully.");
        echo "<script>alert('Feedback, status, and evidence updated successfully.'); window.location.href = 'dashboard_admin.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction in case of any errors
        $conn->rollback();
        // Log the exception message
        error_log("ERROR: Transaction failed with message: " . $e->getMessage());
        echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href = 'dashboard_admin.php';</script>";
    }

    // Close the statement
    $stmt->close();
} else {
    // Redirect if the request method is not POST
    header('Location: dashboard_admin.php');
    exit();
}

// Close the database connection
$conn->close();
?>