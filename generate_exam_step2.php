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

// Validate question_ID from step 1
if (!isset($_POST['question_ID'])) {
    $_SESSION['error'] = "No question set selected";
    header("Location: generate_exam_files.php");
    exit();
}

$question_ID = $_POST['question_ID'];

// Get the exam_ID linked to this question set
$sql = 'SELECT exam_ID FROM question WHERE question_ID = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $question_ID);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

$exam_ID = $exam['exam_ID'] ?? null;

if (!$exam_ID) {
    $_SESSION['error'] = "Exam not found for the selected question set.";
    header("Location: generate_exam_files.php");
    exit();
}

// Fetch students linked via exam_user
$sql = "
SELECT s.student_ID, s.first_name, s.last_name, s.student_email
FROM student s
JOIN exam_user eu ON s.student_ID = eu.user_ID
WHERE eu.exam_ID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_ID);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
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
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">

</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white w-100" style="max-width: 500px;">
            <div class="text-center mb-3">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
                <h4>
                    Welcome, you are logged in as
                    <strong>
                        <?php echo htmlspecialchars(implode(' & ', $roles)); ?>
                    </strong>
                </h4>


                <p>Student List - Update list as required</p>
            </div>

            <?php include('partials/alerts.php'); ?>

            <?php if (count($students) > 0): ?>
                <form method="POST" action="generate_exam_step3.php">
                    <input type="hidden" name="exam_ID" value="<?php echo htmlspecialchars($exam_ID); ?>">
                    <input type="hidden" name="question_ID" value="<?php echo htmlspecialchars($question_ID); ?>">

                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light text-dark">
                                <tr>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $i => $student): ?>
                                    <tr>
                                        <input type="hidden" name="students[<?php echo $i; ?>][student_ID]"
                                            value="<?php echo $student['student_ID']; ?>">
                                        <td><input type="text" class="form-control form-control-sm text-dark"
                                                name="students[<?php echo $i; ?>][first_name]"
                                                value="<?php echo htmlspecialchars($student['first_name']); ?>"></td>
                                        <td><input type="text" class="form-control form-control-sm text-dark"
                                                name="students[<?php echo $i; ?>][last_name]"
                                                value="<?php echo htmlspecialchars($student['last_name']); ?>"></td>
                                        <td><input type="email" class="form-control form-control-sm text-dark"
                                                name="students[<?php echo $i; ?>][email]"
                                                value="<?php echo htmlspecialchars($student['student_email']); ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <br>
                    <div class="d-flex justify-content-between">
                        <a href="generate_exam_files.php" class="btn btn-secondary">Previous Step</a>
                        <button type="submit" class="btn btn-primary">Next Step</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning text-start">No students found for this exam.</div>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-danger w-100">Return to Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>