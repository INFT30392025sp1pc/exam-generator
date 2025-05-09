<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
$role = $user['user_role'] ?? 'User';

// Restrict access to only Subject Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can create exams.";
    header("Location: dashboard.php");
    exit();
}

// Ensure an exam_name exists in session
if (!isset($_SESSION['exam_name'])) {
    $_SESSION['error'] = "Exam session not found. Please start again.";
    header("Location: create_exam_questions.php");
    exit();
}

//retrieve exam_name
$exam_name = $_SESSION['exam_name']; // Retrieve exam ID

//retrieve auto-generated exam_ID from db
$sql = "SELECT exam_ID FROM exam WHERE exam_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $exam_name);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();
$exam_ID = $exam['exam_ID'];
//Store exam_ID for next page
$_SESSION['exam_ID'] = $exam_ID;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam Step 2a</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="create_exam_questions.php">
                    <u>Back</u>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>
                    Welcome, you are logged in as
                    <strong>
                        <?php echo htmlspecialchars(implode(' & ', $roles)); ?>
                    </strong>
                </h4>


                <p>Would you like to upload a question file, modify an existing question list, or manually create a new
                    question list?</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <a href="upload_question_file.php" class="btn btn-light w-100 mb-2">Upload</a>
                <a href="modify_question_list.php" class="btn btn-light w-100 mb-2">Modify</a>

                <!-- Carries the exam_ID variable over to the next page as it is a secondary key for each question -->
                <a href="manual_question_creation.php?$exam_ID=<?php echo $exam_ID ?>"
                    class="btn btn-light w-100 mb-2">Manual</a>
            </div>
        </div>
    </div>
</body>

</html>