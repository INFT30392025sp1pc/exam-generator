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

// Ensure student file was uploaded in Step 3
if (!isset($_SESSION['uploaded_file']) || !file_exists($_SESSION['uploaded_file'])) {
    $_SESSION['error'] = "No student file found. Please complete Step 3 first.";
    header("Location: generate_exam_step3.php");
    exit();
}

// Fetch available templates from the database
$template_files = [];
$query = "SELECT uuid, template_name, file_path FROM exam_templates WHERE status = 'active' ORDER BY template_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $template_files[] = $row;
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["exam_template"])) {
    $upload_dir = "uploads/templates/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Ensure the upload directory exists
    }

    $file = $_FILES["exam_template"];
    $file_name = basename($file["name"]);
    $file_tmp = $file["tmp_name"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_extensions = ["docx", "pdf"];

    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['error'] = "Invalid file type. Only DOCX and PDF files are allowed.";
        header("Location: generate_exam_step4.php");
        exit();
    }

    // Move uploaded file
    $unique_id = uniqid();
    $target_file = $upload_dir . $unique_id . "_" . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        // Insert into database
        $insert_sql = "INSERT INTO exam_templates (uuid, template_name, file_path, uploaded_by, status) VALUES (?, ?, ?, ?, 'active')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssss", $unique_id, $file_name, $target_file, $username);
        $stmt->execute();

        $_SESSION['exam_template'] = $target_file;
        header("Location: generate_exam_step5.php"); // Proceed to step 5
        exit();
    } else {
        $_SESSION['error'] = "File upload failed. Please try again.";
        header("Location: generate_exam_step4.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exams Step 4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" width="150">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Select exam template</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <!-- Select existing template -->
                <form method="POST" action="generate_exam_step5.php">
                    <div class="mb-3">
                        <select class="form-control" name="selected_template" required>
                            <option value="" disabled selected>Select template file</option>
                            <?php foreach ($template_files as $template) { ?>
                                <option value="<?php echo $template['uuid']; ?>">
                                    <?php echo htmlspecialchars($template['template_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Upload</button>
                </form>

                <p>Or drag and drop a file here</p>

                <!-- Upload new template -->
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="exam_template" accept=".docx,.pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">Next</button>
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
