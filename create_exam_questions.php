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
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can create exams.";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_uuid = bin2hex(random_bytes(16)); // Generate a UUID
    $year = $_POST['year'];
    $study_period = $_POST['study_period'];
    $exam_name = $_POST['exam_name'];
    $supplementary = isset($_POST['supplementary']) ? 1 : 0;

    // Insert exam details into database
    $insert_sql = "INSERT INTO exam_details (uuid, year, study_period, exam_name, supplementary) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sisss", $exam_uuid, $year, $study_period, $exam_name, $supplementary);

    if ($insert_stmt->execute()) {
        $_SESSION['exam_uuid'] = $exam_uuid; // Store exam ID for next step
        header("Location: create_exam_step2.php");
        exit();
    } else {
        $error = "Error creating exam.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam Step 1</title>
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
                <p>Please enter details for the exam to be created:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="number" class="form-control" name="year" placeholder="Year" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="study_period" placeholder="Study Period" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_name" placeholder="Exam Name" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="supplementary" id="supplementaryCheckbox">
                        <label class="form-check-label" for="supplementaryCheckbox">Is this a supplementary exam?</label>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Next Step</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>