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

// Get the logged-in username
$username = $_SESSION['username'];

// Fetch user role from the database
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

// Check if user is marked as disabled
if (in_array("Disabled", $roles)) {
    session_unset();
    session_destroy();
    session_start(); // restart session to set error message
    $_SESSION['error'] = "Your account has been disabled. Please contact your administrator.";
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
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

                <p>Please select what you would like to do:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <?php if (in_array("Administrator", $roles)) { ?>
                    <p class="text-white fw-bold mt-4 mb-2 border-bottom border-light pb-1">Administrator Actions</p>
                    <a href="users.php" class="btn btn-light w-100 mb-2">Add/Modify Users</a>
                    <a href="subjects.php" class="btn btn-light w-100 mb-2">Add/Modify Subjects</a>
                <?php }
                if (in_array("Coordinator", $roles)) { ?>
                    <p class="text-white fw-bold mt-4 mb-2 border-bottom border-light pb-1">Coordinator Actions</p>
                    <a href="create_exam_questions.php" class="btn btn-light w-100 mb-2">Create Exam Questions</a>
                    <a href="generate_exam_files.php" class="btn btn-light w-100 mb-2">Generate Exam Files</a>
                    <a href="retrieve_past_exams.php" class="btn btn-light w-100 mb-2">Retrieve past exams</a>
                <?php } ?><br><br>

                <!-- Common Buttons -->
                <a href="change_password.php" class="btn btn-dark w-100 mb-2">Change/update password</a>
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-danger w-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>