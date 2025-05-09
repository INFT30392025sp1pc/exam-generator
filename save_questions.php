<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Error: User not logged in.";
    exit();
}

// Get the logged-in username and role
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

// Assign role variable
$role = $user['role'] ?? 'User';

// Restrict access to only Subject Coordinators
if (!in_array('Coordinator', $roles)) {
    echo "Error: Access Denied. Only Subject Coordinators can save question lists.";
    exit();
}

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Error: Invalid request method.";
    exit();
}

// Ensure exam_uuid is provided
if (!isset($_POST['exam_uuid']) || empty($_POST['exam_uuid'])) {
    echo "Error: Exam UUID is missing.";
    exit();
}

$exam_uuid = $_POST['exam_uuid'];
$updated_content = $_POST['questions'] ?? '';

if (empty($updated_content)) {
    echo "Error: No content provided.";
    exit();
}

// Fetch the associated question file
$query = "SELECT file_path FROM question_files WHERE exam_uuid = ? ORDER BY uuid DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $exam_uuid);
$stmt->execute();
$result = $stmt->get_result();
$file_data = $result->fetch_assoc();

if (!$file_data) {
    echo "Error: No associated question file found.";
    exit();
}

$file_path = $file_data['file_path'];

// Ensure the file exists before saving
if (!file_exists($file_path)) {
    echo "Error: File not found.";
    exit();
}

// Attempt to save the updated content
if (file_put_contents($file_path, $updated_content) !== false) {
    echo "Success: Question list saved successfully.";
} else {
    echo "Error: Unable to save the file.";
}
?>
