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

// Check if a file was uploaded in the previous step
if (!isset($_SESSION['uploaded_file']) || !file_exists($_SESSION['uploaded_file'])) {
    $_SESSION['error'] = "No file found. Please upload a student file.";
    header("Location: generate_exam_step2.php");
    exit();
}

$uploaded_file = $_SESSION['uploaded_file'];
$students = [];

// Read the file contents
$file_extension = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));

if ($file_extension === "csv") {
    // Read CSV file
    if (($handle = fopen($uploaded_file, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $students[] = $data; // Store each row in the array
        }
        fclose($handle);
    }
} elseif ($file_extension === "txt") {
    // Read TXT file (line by line)
    $students = file($uploaded_file, FILE_IGNORE_NEW_LINES);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exams Step 3</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <script>
        function enableEditing() {
            document.getElementById("studentList").contentEditable = true;
            document.getElementById("studentList").focus();
        }
    </script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" width="150">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Review student list</p>

                <div class="mb-3 border p-2 bg-light text-dark rounded" id="studentList">
                    <ul class="list-unstyled">
                        <?php foreach ($students as $student) { ?>
                            <li>📄 <?php echo is_array($student) ? implode(", ", $student) : htmlspecialchars($student); ?></li>
                        <?php } ?>
                    </ul>
                </div>

                <button onclick="enableEditing()" class="btn btn-primary w-100 mb-2">Edit</button>
                <form method="POST" action="generate_exam_step4.php">
                    <input type="hidden" name="student_data" id="studentData">
                    <button type="submit" class="btn btn-success w-100 mb-2">Save</button>
                </form>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-outline-light w-100">Return to login screen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function() {
            let studentData = document.getElementById("studentList").innerText;
            document.getElementById("studentData").value = studentData;
        });
    </script>
</body>
</html>
