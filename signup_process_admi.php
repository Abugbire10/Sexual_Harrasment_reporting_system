<?php
include 'db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// Retrieve form data
$username = sanitizeInput($_POST['username']);
$email = sanitizeInput($_POST['email']);
$password = sanitizeInput($_POST['password']);
$confirm_password = sanitizeInput($_POST['confirm_password']);
$staff_id = sanitizeInput($_POST['staff_id']); // Retrieve the staff ID

// Initialize message
$message = "";

// Array of valid staff IDs
$valid_staff_ids = [10000, 10001, 10002, 10003, 10004];

// Check if staff ID is valid
if (!in_array($staff_id, $valid_staff_ids)) {
    $message = "Invalid staff ID!";
} else {
    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT admin_id FROM administrator WHERE email = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email already exists!";
        } else {
            // Check if staff ID already exists
            $stmt = $conn->prepare("SELECT admin_id FROM administrator WHERE staff_id = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $staff_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "Staff ID already registered!";
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // Insert new administrator into the database
                $stmt = $conn->prepare("INSERT INTO administrator (username, email, password_hash, staff_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("sssi", $username, $email, $password_hash, $staff_id);
                
                if ($stmt->execute()) {
                    echo "<script>
                            alert('Congratulations, you have successfully signed up as an administrator!');
                            window.location.href = 'login.html';
                          </script>";
                    exit();
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }
        }

        $stmt->close();
    }
}

$conn->close();

// Display the message if there's an error
if ($message) {
    echo "<script>
            alert('$message');
            window.location.href = 'signup_admi.html';
          </script>";
    exit();
}
?>
