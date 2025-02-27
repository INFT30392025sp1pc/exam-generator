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

$sql = "SELECT role, username FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Assign role variable
$role = $user['role'] ?? 'User';
$user_name = $user['username'];

// Restrict access to only Subject Coordinators
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can upload question files.";
    header("Location: dashboard.php");
    exit();
}

// Ensure an exam_uuid exists in session
if (!isset($_SESSION['exam_uuid'])) {
    $_SESSION['error'] = "Exam session not found. Please start again.";
    header("Location: create_exam_questions.php");
    exit();
}

$exam_uuid = $_SESSION['exam_uuid']; // Retrieve exam UUID

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["question_file"])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["question_file"]["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_types = ["pdf", "docx", "txt"];

    // Validate file type
    if (!in_array($file_ext, $allowed_types)) {
        $error = "Invalid file type. Only PDF, DOCX, and TXT files are allowed.";
    } else {
        // Generate unique file name
        $unique_file_name = $exam_uuid . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $unique_file_name;

        if (move_uploaded_file($_FILES["question_file"]["tmp_name"], $target_file)) {
            // Insert file record into database
            $file_uuid = bin2hex(random_bytes(16));
            $insert_sql = "INSERT INTO question_files (uuid, exam_uuid, file_path, uploaded_by) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $file_uuid, $exam_uuid, $target_file, $user_name);

            if ($insert_stmt->execute()) {
                $_SESSION['success'] = "File uploaded successfully.";
                header("Location: create_exam_step3.php"); // Redirect to next step
                exit();
            } else {
                $error = "Error saving file details to the database.";
            }
        } else {
            $error = "Error uploading file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Question File</title>
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
                <p>Upload a question file</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="file" name="question_file" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-light w-100 mb-2">Upload</button>
                </form>

                <p>Or drag and drop file here</p>

                <!-- Drag and Drop Upload Box -->
                <div class="border p-3 mb-3 text-center" id="drop-area">
                    <input type="file" id="fileInput" name="question_file" class="d-none">
                    <label for="fileInput" class="btn btn-outline-light w-100">Add file</label>
                </div>

                <a href="create_exam_step3.php" class="btn btn-light w-100 mb-2">Next</a>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-outline-light w-100">Return to login screen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Drag and Drop File Upload Handling
        let dropArea = document.getElementById("drop-area");

        dropArea.addEventListener("dragover", function(event) {
            event.preventDefault();
            dropArea.classList.add("border-primary");
        });

        dropArea.addEventListener("dragleave", function(event) {
            dropArea.classList.remove("border-primary");
        });

        dropArea.addEventListener("drop", function(event) {
            event.preventDefault();
            dropArea.classList.remove("border-primary");
            let files = event.dataTransfer.files;
            document.getElementById("fileInput").files = files;
        });
    </script>
</body>
</html>
