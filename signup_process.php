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

// Initialize message
$message = "";

// Check if passwords match
if ($password !== $confirm_password) {
    $message = "Passwords do not match!";
} else {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Email already exists!";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Congratulations, you have successfully signed up!');
                    window.location.href = 'login.html';
                  </script>";
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();

// Display the message if there's an error
if ($message) {
    echo "<script>
            alert('$message');
            window.location.href = 'signup.html';
          </script>";
    exit();
}
?>