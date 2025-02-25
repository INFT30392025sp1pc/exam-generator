<?php
session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's role
$username = $_SESSION['username'];

$sql = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$role = $user['role'] ?? 'User';

// Restrict access to only Subject Coordinators
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can proceed.";
    header("Location: dashboard.php");
    exit();
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["student_file"])) {
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Ensure the upload directory exists
    }

    $file = $_FILES["student_file"];
    $file_name = basename($file["name"]);
    $file_tmp = $file["tmp_name"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_extensions = ["csv", "txt"];

    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['error'] = "Invalid file type. Only CSV and TXT files are allowed.";
        header("Location: generate_exam_step2.php");
        exit();
    }

    // Move uploaded file
    $target_file = $upload_dir . uniqid() . "_" . $file_name;
    if (move_uploaded_file($file_tmp, $target_file)) {
        $_SESSION['uploaded_file'] = $target_file;
        header("Location: generate_exam_step3.php"); // Proceed to step 3
        exit();
    } else {
        $_SESSION['error'] = "File upload failed. Please try again.";
        header("Location: generate_exam_step2.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exams Step 2</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" width="150">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Upload a student file</p>

                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php } ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="student_file" accept=".csv,.txt" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Upload</button>
                </form>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-outline-light w-100">Return to login screen</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
