<?php

require_once 'functions.php';
enableDebug(false); // Set to false in production

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
$exam_ID = $_SESSION['exam_data']['exam_ID'];
$question_ID = $_SESSION['exam_data']['question_ID'];
$students = $_SESSION['exam_data']['students'];

if (!$exam_ID || !$question_ID || empty($students)) {
    $_SESSION['error'] = "Invalid data submitted.";
    header("Location: generate_exam_files.php");
    exit();
}

// Track submitted student IDs and skipped students
$submittedStudentIDs = [];
$skippedStudents = [];

foreach ($students as $student) {
    $susername = trim($student['username']);
    $first = trim($student['first_name']);
    $last = trim($student['last_name']);
    $email = trim($student['student_email']);

    // 1. Check if username already exists for another student
    $checkUsernameStmt = $conn->prepare("SELECT student_ID, student_email FROM student WHERE username = ?");
    $checkUsernameStmt->bind_param("s", $susername);
    $checkUsernameStmt->execute();
    $usernameResult = $checkUsernameStmt->get_result();

    if ($usernameResult->num_rows > 0) {
        $existingStudent = $usernameResult->fetch_assoc();
        if ($existingStudent['student_email'] !== $email) {
            // Username exists for a different student (different email)
            $skippedStudents[] = [
                'email' => $email,
                'username' => $susername,
                'reason' => "Username '$susername' already exists for another student"
            ];
            continue; // Skip this student
        }
        // If we get here, it's the same student (same email)
    }

    // 2. Check if student exists by email
    $stmt = $conn->prepare("SELECT student_ID FROM student WHERE student_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $student_ID = $row['student_ID'];

        // Update name and username
        $update = $conn->prepare("UPDATE student SET first_name = ?, last_name = ?, username = ? WHERE student_ID = ?");
        $update->bind_param("sssi", $first, $last, $susername, $student_ID);
        $update->execute();
    } else {
        // Insert new student
        $insert = $conn->prepare("INSERT INTO student (username, first_name, last_name, student_email) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $susername, $first, $last, $email);
        $insert->execute();
        $student_ID = $insert->insert_id;
    }

    // Track this student
    $submittedStudentIDs[] = $student_ID;

    // 3. Link student to exam if not already linked
    $check_link = $conn->prepare("SELECT 1 FROM exam_user WHERE exam_ID = ? AND user_ID = ?");
    $check_link->bind_param("ii", $exam_ID, $student_ID);
    $check_link->execute();
    $check_result = $check_link->get_result();

    if ($check_result->num_rows === 0) {
        $link = $conn->prepare("INSERT INTO exam_user (exam_ID, user_ID) VALUES (?, ?)");
        $link->bind_param("ii", $exam_ID, $student_ID);
        $link->execute();
    }
}

// Remove students no longer in the form
$currentStmt = $conn->prepare("SELECT user_ID FROM exam_user WHERE exam_ID = ?");
$currentStmt->bind_param("i", $exam_ID);
$currentStmt->execute();
$currentResult = $currentStmt->get_result();

$currentStudentIDs = [];
while ($row = $currentResult->fetch_assoc()) {
    $currentStudentIDs[] = (int)$row['user_ID'];
}

$studentsToRemove = array_diff($currentStudentIDs, $submittedStudentIDs);

if (!empty($studentsToRemove)) {
    $placeholders = implode(',', array_fill(0, count($studentsToRemove), '?'));
    $types = str_repeat('i', count($studentsToRemove));

    $sql = "DELETE FROM exam_user WHERE exam_ID = ? AND user_ID IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i' . $types, $exam_ID, ...$studentsToRemove);
    $stmt->execute();
}
// Store skipped students in session for display
if (!empty($skippedStudents)) {
    $_SESSION['skipped_students'] = $skippedStudents;
}

// Redirect to step 4
header("Location: generate_exam_step4.php?exam_ID=$exam_ID&question_ID=$question_ID");
exit();
?>
