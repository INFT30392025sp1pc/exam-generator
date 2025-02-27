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

$sql = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Assign role variable
$role = $user['role'] ?? 'User';

// Restrict access to only Subject Coordinators
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can review question lists.";
    header("Location: dashboard.php");
    exit();
}

// Ensure an exam_uuid exists in session
if (!isset($_SESSION['exam_uuid'])) {
    $_SESSION['error'] = "Exam session not found. Please start again.";
    header("Location: create_exam.php");
    exit();
}

$exam_uuid = $_SESSION['exam_uuid']; // Retrieve exam UUID

// Fetch the uploaded question file details
$query = "SELECT file_path FROM question_files WHERE exam_uuid = ? ORDER BY uuid DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $exam_uuid);
$stmt->execute();
$result = $stmt->get_result();
$file_data = $result->fetch_assoc();

// Check if file exists
$file_path = $file_data['file_path'] ?? null;
$file_content = "";

if ($file_path && file_exists($file_path)) {
    $file_content = file_get_contents($file_path);
} else {
    $error = "No uploaded question file found.";
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
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" width="150">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Review question list</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <p>📄 List of questions here</p>
                <textarea class="form-control mb-3" id="questionList" rows="8"><?php echo htmlspecialchars($file_content); ?></textarea>

                <button type="button" class="btn btn-light w-100 mb-2" id="saveBtn">Save</button>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-outline-light w-100">Return to login screen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("saveBtn").addEventListener("click", function() {
            let questionList = document.getElementById("questionList").value;
            fetch("save_questions.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "exam_uuid=<?php echo $exam_uuid; ?>&questions=" + encodeURIComponent(questionList)
                })
                .then(response => response.text())
                .then(data => alert(data));
        });
    </script>
</body>

</html>