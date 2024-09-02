<?php
include 'db.php';

if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];

    $sql = "SELECT evidence1, evidence2, evidence3, evidence4, evidence5 FROM reports WHERE report_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $hasEvidence = !empty($row['evidence1']) || !empty($row['evidence2']) || !empty($row['evidence3']) || !empty($row['evidence4']) || !empty($row['evidence5']);

    echo json_encode(['hasEvidence' => $hasEvidence]);
} else {
    echo json_encode(['hasEvidence' => false]);
}

$conn->close();
?>
