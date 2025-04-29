<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's role
$username = $_SESSION['username'];
$sql = "SELECT user_role FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$role = $user['user_role'] ?? 'User';
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can proceed.";
    header("Location: dashboard.php");
    exit();
}

// Validate required POST data
$exam_ID = $_POST['exam_ID'] ?? null;
$question_ID = $_POST['question_ID'] ?? null;
$students = $_POST['students'] ?? [];

if (!$exam_ID || !$question_ID || empty($students)) {
    $_SESSION['error'] = "Invalid data submitted.";
    header("Location: generate_exam_files.php");
    exit();
}

// Update student records
foreach ($students as $student) {
    $student_ID = $student['student_ID'];
    $first = $student['first_name'];
    $last = $student['last_name'];
    $email = $student['email'];

    $update = $conn->prepare("
        UPDATE student 
        SET first_name = ?, last_name = ?, student_email = ?
        WHERE student_ID = ?
    ");
    $update->bind_param("sssi", $first, $last, $email, $student_ID);
    $update->execute();
}

// Redirect to step 4
$_SESSION['success'] = "Student records updated successfully.";
header("Location: generate_exam_step4.php?exam_ID=$exam_ID&question_ID=$question_ID");
exit();
?>
