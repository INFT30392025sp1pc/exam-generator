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

// Get the logged-in username
$username = $_SESSION['username'];

// Fetch user role from the database
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

// Allow only Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can generate exam files.";
    header("Location: dashboard.php");
    exit();
}

// Fetch questions grouped by exam
$query = "
SELECT 
    MIN(q.question_ID) AS question_ID,
    e.exam_ID,
    e.exam_year,
    e.exam_sp,
    e.subject_code,
    e.exam_name
FROM question q
JOIN exam e ON q.exam_ID = e.exam_ID
JOIN subject s ON e.subject_code = s.subject_code
WHERE e.exam_archive = 0 AND s.subject_archive = 0
GROUP BY e.exam_ID
ORDER BY e.exam_year DESC, e.exam_sp DESC, e.subject_code ASC
";
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


                <p>Please enter details for the exam to be generated</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="generate_exam_step2.php">
                    <div class="mb-3">
                        <select class="form-control" name="question_ID" required>
                            <option value="" disabled selected>Select Question Set</option>
                            <?php foreach ($question_files as $file) { ?>
                                <option value="<?php echo $file['question_ID']; ?>">
                                    <?php echo htmlspecialchars("{$file['subject_code']} - {$file['exam_name']} - SP{$file['exam_sp']} {$file['exam_year']}"); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Next</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>