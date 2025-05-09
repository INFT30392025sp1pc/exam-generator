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

// Get logged-in user's role
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

// Restrict access to only Subject Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can proceed.";
    header("Location: dashboard.php");
    exit();
}

// Ensure that an exam template was selected or uploaded
if (!isset($_SESSION['exam_template'])) {
    $_SESSION['error'] = "No exam template found. Please complete Step 4 first.";
    header("Location: generate_exam_step4.php");
    exit();
}

// Handle Exam Generation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_exam'])) {
    // Simulating exam generation process
    $_SESSION['generated_exam'] = "exam_files/generated_exam.docx"; // Example path
    $_SESSION['success'] = "Exam successfully generated!";
    header("Location: generate_exam_step5.php");
    exit();
}

// Handle Exporting of Exam
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['export_exam'])) {
    $format = $_POST['export_format'] ?? '';

    if (!in_array($format, ["pdf", "docx", "xlsx"])) {
        $_SESSION['error'] = "Invalid export format selected.";
        header("Location: generate_exam_step5.php");
        exit();
    }

    // Simulate exporting (real logic for generating the file needs to be implemented)
    $_SESSION['success'] = "Exam successfully exported as " . strtoupper($format) . ".";
    header("Location: generate_exam_step5.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exams Step 5</title>
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


                <p>Generate Exam Papers</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <button type="submit" name="generate_exam" class="btn btn-primary w-100 mb-2">Generate</button>
                </form>

                <p>Export exam papers. Select format:</p>
                <form method="POST" action="">
                    <div class="mb-3">
                        <select class="form-control" name="export_format" required>
                            <option value="" disabled selected>Format of export</option>
                            <option value="pdf">PDF</option>
                            <option value="docx">Word (DOCX)</option>
                            <option value="xlsx">Excel (XLSX)</option>
                        </select>
                    </div>
                    <button type="submit" name="export_exam" class="btn btn-primary w-100 mb-2">Export</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>