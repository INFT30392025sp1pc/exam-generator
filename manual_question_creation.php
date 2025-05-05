<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Get the logged-in username and role
$username = $_SESSION['username'];

$sql = "SELECT user_role FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['user_role'] ?? 'User';

// Restrict access to only Administrators
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Administrators can add subjects.";
    header("Location: subjects.php");
    exit();
}

$exam_ID = $_SESSION['exam_ID'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contents = trim($_POST['contents']);
    $question_ID = bin2hex(random_bytes(16)); // Generate a question_ID


    // Check if the question already exists
    $check_sql = "SELECT * FROM question WHERE contents = ? AND exam_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $contents, $exam_ID);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "This question already exists!";
    } else {
        // Insert the new subject
        $insert_sql = "INSERT INTO question (contents, exam_ID, time_created) VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ss", $contents, $exam_ID);

        if ($insert_stmt->execute()) {
            $_SESSION['success'] = "Question added successfully.";
            header("Location: manual_question_creation.php");
            exit();
        } else {
            $error = "Error adding subject.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Subject</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css"> 
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="subjects.php">
                <u>Back</u>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="contents" placeholder="Add Question Here" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100 mb-2">Add Question</button>
                    <a href="create_exam_step3.php" class="btn btn-light w-100 mb-2">Finish</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>



