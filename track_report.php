<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    error_log('User ID not set in session');
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT report_id, type, description, report_date, status, Admin_Feedback, admin_evidence FROM reports WHERE user_id = ? ORDER BY report_date DESC";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log('Statement preparation failed: ' . $conn->error);
        throw new Exception('Failed to prepare SQL statement');
    }

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $reports = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($reports)) {
        error_log('No reports found for user ID: ' . $user_id);
    }

    // Process admin_evidence
    foreach ($reports as &$report) {
        if (!empty($report['admin_evidence'])) {
            $report['admin_evidence'] = '<a href="download_evidence.php?file=' . urlencode($report['admin_evidence']) . '" target="_blank">View Document</a>';
        } else {
            $report['admin_evidence'] = 'No document';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($reports);

    $stmt->close();
} catch (Exception $e) {
    error_log('Error in track_report.php: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    $conn->close();
}