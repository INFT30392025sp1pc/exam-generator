<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

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

//Update student records
// Check if the students array is not empty
foreach ($_POST['students'] as $student) {
    $first = trim($student['first_name']);
    $last = trim($student['last_name']);
    $email = trim($student['student_email']);

    // 1. Check if student already exists
    $stmt = $conn->prepare("SELECT student_ID FROM student WHERE student_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $student_ID = $row['student_ID'];

        // Optional: update name
        $update = $conn->prepare("UPDATE student SET first_name = ?, last_name = ? WHERE student_ID = ?");
        $update->bind_param("ssi", $first, $last, $student_ID);
        $update->execute();
    } else {
        // 2. Insert new student
        $insert = $conn->prepare("INSERT INTO student (first_name, last_name, student_email) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $first, $last, $email);
        $insert->execute();
        $student_ID = $insert->insert_id;
    }

    // 3. Link student to exam
    $exam_ID = (int) $_POST['exam_ID'];
    $check_link = $conn->prepare("SELECT * FROM exam_user WHERE exam_ID = ? AND user_ID = ?");
    $check_link->bind_param("ii", $exam_ID, $student_ID);
    $check_link->execute();
    $check_result = $check_link->get_result();

    if ($check_result->num_rows === 0) {
        $link = $conn->prepare("INSERT INTO exam_user (exam_ID, user_ID) VALUES (?, ?)");
        $link->bind_param("ii", $exam_ID, $student_ID);
        $link->execute();
    }
}


// Redirect to step 4
$_SESSION['success'] = "Student records updated successfully.";
header("Location: generate_exam_step4.php?exam_ID=$exam_ID&question_ID=$question_ID");
exit();
?>
