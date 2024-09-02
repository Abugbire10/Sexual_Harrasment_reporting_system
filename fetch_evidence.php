<?php
include 'db.php'; // Include your database connection file

if (isset($_GET['report_id'])) {
    $report_id = intval($_GET['report_id']);
    
    $sql = "SELECT evidence1, evidence2, evidence3, evidence4, evidence5 FROM reports WHERE report_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $evidenceFiles = [];
    if ($row = $result->fetch_assoc()) {
        foreach (['evidence1', 'evidence2', 'evidence3', 'evidence4', 'evidence5'] as $evidenceField) {
            if (!empty($row[$evidenceField])) {
                $evidenceFiles[] = basename($row[$evidenceField]); // Get the file name only
            }
        }
    }

    // Return the evidence files as JSON
    echo json_encode(['evidence' => $evidenceFiles]);
}

$conn->close();
?>
