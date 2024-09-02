<?php
// Start the session
session_start();

// Database connection details
include 'db.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $new_password = $_POST["new_password"];
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email exists, update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        if ($update_stmt->execute()) {
            // Password reset successfully, set message
            $_SESSION['success_message'] = "Congratulations! Your password has been reset successfully.";
        } else {
            $message = "Error resetting password. Please try again.";
        }
        $update_stmt->close();
    } else {
        $message = "Email not found. Please check your email address.";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-reset {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .form-reset h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .form-floating input {
            border-radius: 5px;
        }
        .message {
            margin-top: 1rem;
        }
    </style>
</head>
<body class="text-center">
    <main class="form-reset">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1 class="h3 mb-3 fw-normal">Password Reset</h1>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="new_password" placeholder="New Password" required>
                <label for="floatingPassword">New Password</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Reset Password</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger message" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    // Check for success message in session and display it
    if (isset($_SESSION['success_message'])) {
        echo "<div id='congratsModal' class='modal fade' tabindex='-1'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title'>Congratulations!</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <p>" . $_SESSION['success_message'] . "</p>
                        </div>
                    </div>
                </div>
              </div>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('congratsModal'));
                    myModal.show();
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 3000); // Redirect after 3 seconds
                });
              </script>";
        // Unset the message after displaying
        unset($_SESSION['success_message']);
    }
    ?>
</body>
</html>
