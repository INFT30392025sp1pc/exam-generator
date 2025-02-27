<?php
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
$sql = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Assign role variable
$role = $user['role'] ?? 'User'; // Default to 'User' if no role is found

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
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Please select what you would like to do:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <?php if ($role === "Administrator") { ?>
                    <a href="users.php" class="btn btn-light w-100 mb-2">Add/Modify Users</a>
                    <a href="subjects.php" class="btn btn-light w-100 mb-2">Add/Modify Subjects</a>
                    <a href="change_password.php" class="btn btn-dark w-100 mb-2">Change/update password</a>
                <?php } elseif ($role === "Coordinator") { ?>
                    <a href="create_exam_questions.php" class="btn btn-light w-100 mb-2">Create Exam Questions</a>
                    <a href="generate_exam_files.php" class="btn btn-light w-100 mb-2">Generate Exam Files</a>
                    <a href="retrieve_past_exams.php" class="btn btn-light w-100 mb-2">Retrieve past exams</a>
                    <a href="change_password.php" class="btn btn-dark w-100 mb-2">Change/update password</a>
                <?php } ?>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-danger w-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
