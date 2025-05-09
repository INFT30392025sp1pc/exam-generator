<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$username = $_SESSION['username'];

$sql = "
SELECT r.role_name 
FROM user u
JOIN user_role_map urm ON u.user_ID = urm.user_ID
JOIN role r ON urm.role_id = r.role_id
WHERE u.user_email = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role_name'];
}
if (empty($roles)) {
    $roles[] = 'User';
}

$role = $user['role'] ?? 'User';

if (!in_array('Coordinator', $roles)) {
    echo json_encode(["status" => "error", "message" => "Access Denied."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}

if (!isset($_POST['student_file_uuid']) || empty($_POST['student_file_uuid'])) {
    echo json_encode(["status" => "error", "message" => "Missing file reference."]);
    exit();
}

$student_file_uuid = $_POST['student_file_uuid'];
$updated_content = $_POST['students'] ?? '';

if (empty($updated_content)) {
    echo json_encode(["status" => "error", "message" => "No content provided."]);
    exit();
}

// Retrieve student file path
$query = "SELECT file_path FROM student_files WHERE uuid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_file_uuid);
$stmt->execute();
$result = $stmt->get_result();
$file_data = $result->fetch_assoc();

if (!$file_data) {
    echo json_encode(["status" => "error", "message" => "No associated student file found."]);
    exit();
}

$file_path = $file_data['file_path'];

if (!file_exists($file_path)) {
    echo json_encode(["status" => "error", "message" => "File not found."]);
    exit();
}

// Save the updated content
if (file_put_contents($file_path, $updated_content) !== false) {
    $_SESSION['output_format'] = 'PDF'; // Set output format to PDF
    echo json_encode(["status" => "success", "redirect" => "generate_exam_step4.php"]);
} else {
    echo json_encode(["status" => "error", "message" => "Unable to save the file."]);
}
?>
