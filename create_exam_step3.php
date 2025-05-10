<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
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
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can review question lists.";
    header("Location: dashboard.php");
    exit();
}

// Ensure an exam_ID exists in session
if (!isset($_SESSION['exam_ID'])) {
    $_SESSION['error'] = "Exam session not found. Please start again.";
    header("Location: create_exam.php");
    exit();
}

// Gain the current exam_ID so that only the correct questions are selected
$exam_ID = $_SESSION['exam_ID'];

// Handle the question selection
$select_stmt = "SELECT contents FROM question WHERE exam_ID = ?";
$select_stmt = $conn->prepare($select_stmt);
$select_stmt->bind_param("s", $exam_ID);
$select_stmt->execute();
$select_result = $select_stmt->get_result();

// Handle the for submission 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $delete_sql = "DELETE FROM question WHERE exam_ID = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $exam_ID);
    $delete_stmt->execute();

    $textarea = $_POST['questionlist'];

    $number_of_questions = count(explode("\n", rtrim($textarea)));

    foreach (explode("\n", rtrim($textarea)) as $modified_question) {
        $insert_sql = "INSERT INTO question (time_created, contents, exam_ID) VALUES (NOW(), ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ss", $modified_question, $exam_ID);

        if (++$i === $number_of_questions) {
            if ($insert_stmt->execute()) {
                $_SESSION['success'] = "Question(s) added successfully.";
                header("Location: upload_truss.php");
                exit();
            } else {
                $_SESSION['error'] = "Error adding question.";
                header("Location: create_exam_step2.php");
            }
        } else
            $insert_stmt->execute();
    }

    if ($insert_stmt->execute()) {
        $_SESSION['success'] = "Question(s) added successfully.";
        header("Location: upload_truss.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding question.";
        header("Location: create_exam_step2.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Question List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="create_exam_step2.php">
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


                <p>Review question list</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <p>📄 List of questions here</p>
                <form method="POST" action="">
                    <!-- The below php uses the result from the earlier sql query and loops through the data collecting only the "contents" or actual question -->
                    <div class=mb-3>
                        <textarea class="form-control" name="questionlist" rows="8" wrap="off"><?php
                        if ($select_result->num_rows > 0) {
                            while ($row = $select_result->fetch_assoc()) {
                                echo $row["contents"] . "\n";
                            }
                        }
                        ?>
                        </textarea>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>