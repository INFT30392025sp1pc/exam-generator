<?php

require_once 'functions.php';
enableDebug(false);

session_start();
include('db.php');

// Ensure login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get user roles
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

// Handle "Disabled" user
if (in_array("Disabled", $roles)) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['error'] = "Your account has been disabled.";
    header("Location: login.php");
    exit();
}

// Coordinator-only access
if (!in_array("Coordinator", $roles)) {
    $_SESSION['error'] = "Access Denied. Only Coordinators can access this page.";
    header("Location: dashboard.php");
    exit();
}

// Archive/unarchive actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['archive_subject_code']) || isset($_POST['unarchive_subject_code'])) {
        $subject_code = $_POST['archive_subject_code'] ?? $_POST['unarchive_subject_code'];
        $new_status = isset($_POST['archive_subject_code']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE subject SET subject_archive = ? WHERE subject_code = ?");
        $stmt->bind_param("is", $new_status, $subject_code);
        $stmt->execute();
        $_SESSION['success'] = ($stmt->affected_rows > 0)
            ? "Subject '$subject_code' " . ($new_status ? "archived." : "unarchived.")
            : "No changes made.";
    } elseif (isset($_POST['archive_exam']) || isset($_POST['unarchive_exam'])) {
        $exam_id = (int) ($_POST['archive_exam'] ?? $_POST['unarchive_exam']);
        $new_status = isset($_POST['archive_exam']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE exam SET exam_archive = ? WHERE exam_ID = ?");
        $stmt->bind_param("ii", $new_status, $exam_id);
        $stmt->execute();
        $_SESSION['success'] = ($stmt->affected_rows > 0)
            ? "Exam " . ($new_status ? "archived." : "unarchived.")
            : "No changes made.";
    }

    header("Location: manage_exam_data.php");
    exit();
}

// Fetch all subjects and exams
$subjects = $conn->query("SELECT * FROM subject ORDER BY subject_archive ASC, subject_code ASC");
$exams = $conn->query("SELECT * FROM exam ORDER BY exam_archive ASC, exam_name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Exam Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="dashboard.php">
                    <u>Back</u></a>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
            </div>
            <h5>Manage Exam Data</h5>

            <?php include('partials/alerts.php'); ?>

            <h6 class="mt-4">Subjects</h6>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-bordered table-sm bg-white text-dark">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($sub = $subjects->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($sub['subject_code']) ?></td>
                                <td><?= htmlspecialchars($sub['subject_name']) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <?php if ($sub['subject_archive']): ?>
                                            <button class="btn btn-sm btn-success" name="unarchive_subject_code" value="<?= htmlspecialchars($sub['subject_code']) ?>">Unarchive</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger" name="archive_subject_code" value="<?= htmlspecialchars($sub['subject_code']) ?>">Archive</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h6 class="mt-4">Exams</h6>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-bordered table-sm bg-white text-dark">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ex = $exams->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($ex['exam_name']) ?></td>
                                <td><?= htmlspecialchars($ex['exam_sp'] ?? '-') ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <?php if ($ex['exam_archive']): ?>
                                            <button class="btn btn-sm btn-success" name="unarchive_exam" value="<?= (int) $ex['exam_ID'] ?>">Unarchive</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger" name="archive_exam" value="<?= (int) $ex['exam_ID'] ?>">Archive</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <a href="dashboard.php" class="btn btn-danger w-100 mt-4">Return to Dashboard</a>
        </div>
    </div>
</body>

</html>
