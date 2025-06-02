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

// Get the logged-in username and role
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

// Assign role variable
$role = $user['user_role'] ?? 'User';

// Restrict access to only Subject Coordinators
if (!in_array('Coordinator', $roles)) {

    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can create exams.";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_year = $_POST['exam_year'];
    $exam_sp = $_POST['study_period'];
    $exam_name = $_POST['exam_name'];
    $subject_code = $_POST['subject_code'];
    $is_supplementary = isset($_POST['supplementary']) ? 1 : 0;

    // Insert exam details into database
    $insert_sql = "INSERT INTO exam (exam_year, exam_sp, exam_name, subject_code, is_supplementary, time_created) VALUES (?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssss", $exam_year, $exam_sp, $exam_name, $subject_code, $is_supplementary);

    if ($insert_stmt->execute()) {
        $_SESSION['exam_name'] = $exam_name; // Store exam name for next step
        header("Location: create_exam_step2.php");
        exit();
    } else {
        $error = "Error creating exam.";
    }
}

// Fetch subject codes from the subject table - exam.subject_code relies on subject.subject_code
$subject_stmt = $conn->prepare("SELECT subject_code FROM subject ORDER BY subject_code ASC");
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();

$subjects = [];
while ($row = $subject_result->fetch_assoc()) {
    $subjects[] = $row['subject_code'];
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
            <div class="text-left">
                <a href="dashboard.php">
                    <u>Back</u>
            </div>
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


                <p> Please enter details for the exam to be created:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="number" class="form-control" name="exam_year" min="2020" max="2100" step="1"
                            value="<?= date('Y'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="study_period" required>
                            <option value="" disabled selected>Select Study Period</option>
                            <option value="SP1">SP1</option>
                            <option value="SP2">SP2</option>
                            <option value="SP3">SP3</option>
                            <option value="SP4">SP4</option>
                            <option value="SP5">SP5</option>
                            <option value="SP6">SP6</option>
                            <option value="SP7">SP7</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="exam_name" placeholder="Exam Name" required>
                    </div>
                    <div class="mb-3">
                        <select name="subject_code" class="form-select form-control" required>
                            <option value="" disabled selected>Select Subject Code</option>
                            <?php foreach ($subjects as $code): ?>
                                <option value="<?= htmlspecialchars($code); ?>"><?= htmlspecialchars($code); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="supplementary" id="supplementaryCheckbox">
                        <label class="form-check-label" for="supplementaryCheckbox">Is this a supplementary
                            exam?</label>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Next Step</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>