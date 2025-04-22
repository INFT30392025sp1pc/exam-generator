<?php
session_start();
include('db.php');

if (!isset($_SESSION['username']) || !isset($_SESSION['exam_uuid'])) {
    $_SESSION['error'] = "Invalid session.";
    header("Location: generate_exam_step4.php");
    exit();
}

$exam_uuid = $_SESSION['exam_uuid'];
$output_format = $_GET['format'] ?? 'PDF';

// Retrieve exam data
$query = "SELECT * FROM exam_papers WHERE uuid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $exam_uuid);
$stmt->execute();
$result = $stmt->get_result();
$exam_data = $result->fetch_assoc();

if (!$exam_data) {
    $_SESSION['error'] = "Exam data not found.";
    header("Location: generate_exam_step4.php");
    exit();
}

// Simulating export process
$export_file = "exports/exam_" . $exam_uuid . "." . strtolower($output_format);
file_put_contents($export_file, "Generated exam content here...");

header("Content-Disposition: attachment; filename=" . basename($export_file));
header("Content-Type: application/octet-stream");
header("Content-Length: " . filesize($export_file));
readfile($export_file);
exit();
?>
