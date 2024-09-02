<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You need to log in to submit a report."]);
    exit();
}

include 'db.php'; // Database connection

$response = ["success" => false, "message" => "An unknown error occurred."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $anonymous = $_POST['anonymous'];

    // Handle non-anonymous submissions
    $name = $anonymous == '0' ? $_POST['name'] : '';
    $email = $anonymous == '0' ? $_POST['email'] : '';
    $department = $anonymous == '0' ? $_POST['department'] : '';
    $phone = $anonymous == '0' ? $_POST['phone'] : '';

    // Prepare the upload directory
    $upload_dir = 'uploads/';
    $evidence = [null, null, null, null, null]; // Initialize an array for evidence with null values

    // Handle file uploads
    $fileCount = 0; // Track the number of uploaded files
    if (!empty($_FILES['evidence'])) {
        foreach ($_FILES['evidence']['name'] as $index => $fileName) {
            if ($_FILES['evidence']['error'][$index] === UPLOAD_ERR_OK) {
                $file_path = $upload_dir . basename($fileName);
                if (move_uploaded_file($_FILES['evidence']['tmp_name'][$index], $file_path)) {
                    if ($fileCount < 5) { // Store only up to 5 files
                        $evidence[$fileCount] = $file_path; // Store the file path
                        $fileCount++;
                    }
                } else {
                    $response = ["success" => false, "message" => "Error moving file: " . $_FILES['evidence']['error'][$index]];
                    echo json_encode($response);
                    exit();
                }
            } else {
                $response = ["success" => false, "message" => "Error uploading file: " . $_FILES['evidence']['error'][$index]];
                echo json_encode($response);
                exit();
            }
        }
    }

    // Log the evidence paths for debugging
    error_log("Evidence paths: " . implode(", ", $evidence));

    // Set initial status
    $status = 'submitted';

    // Insert the report into the database
    try {
        $stmt = $conn->prepare("INSERT INTO Reports (user_id, type, description, status, anonymous, name, email, department, phone, evidence1, evidence2, evidence3, evidence4, evidence5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "isssssssssssss", 
            $user_id, 
            $type, 
            $description, 
            $status, 
            $anonymous, 
            $name, 
            $email, 
            $department, 
            $phone, 
            $evidence[0], 
            $evidence[1], 
            $evidence[2], 
            $evidence[3], 
            $evidence[4]
        );

        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

        $response = ["success" => true, "message" => "Report submitted successfully!"];
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $response = ["success" => false, "message" => "Error: " . $e->getMessage()];
        error_log("Error submitting report: " . $e->getMessage());
    }
}

echo json_encode($response);
?>
