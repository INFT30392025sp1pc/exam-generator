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
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can generate exam files.";
    header("Location: dashboard.php");
    exit();
}

// Fetch available question files
$query = "SELECT qf.uuid, qf.file_path, ed.exam_name 
          FROM question_files qf 
          JOIN exam_details ed ON qf.exam_uuid = ed.uuid 
          ORDER BY ed.exam_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$question_files = [];
while ($row = $result->fetch_assoc()) {
    $question_files[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exams Step 1</title>
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
                <p>Please enter details for the exam to be generated</p>

                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php } ?>

                <form method="POST" action="generate_exam_step2.php">
                    <div class="mb-3">
                        <select class="form-control" name="question_file_uuid" required>
                            <option value="" disabled selected>Select question file</option>
                            <?php foreach ($question_files as $file) { ?>
                                <option value="<?php echo $file['uuid']; ?>">
                                    <?php echo htmlspecialchars($file['exam_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Next</button>
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
