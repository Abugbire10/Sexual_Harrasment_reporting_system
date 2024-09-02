<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['admin_id']; // Assuming user is logged in and user_id is stored in session

    // Fetch the current password hash from the database
    $stmt = $conn->prepare("SELECT password_hash FROM administrator WHERE admin_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Output MySQL error
    }
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (password_verify($current_password, $hashed_password)) {
        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE administrator SET password_hash = ? WHERE admin_id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error); // Output MySQL error
            }
            $stmt->bind_param('si', $new_hashed_password, $admin_id);
            if ($stmt->execute()) {
                // Success message and redirect
                echo "<script>alert('Password reset successful!'); window.location.href='dashboard.php';</script>";
                exit; // Ensure no further code is executed
            } else {
                // Error message
                echo "<script>alert('Error resetting password. Please try again.');</script>";
            }
            $stmt->close();
        } else {
            // Error message if passwords do not match
            echo "<script>alert('New password and confirm password do not match.');</script>";
        }
    } else {
        // Error message if current password is incorrect
        echo "<script>alert('Current password is incorrect.');</script>";
    }
}
?>
