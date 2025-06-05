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
if (!isset($_POST['question_ID']) && !isset($_GET['question_ID'])) {
    $_SESSION['error'] = "No question set selected";
    header("Location: generate_exam_files.php");
    exit();
}

$question_ID = $_POST['question_ID'] ?? $_GET['question_ID'] ?? null;

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

// Initialize students array
$students = [];

// Fetch students linked via exam_user
if ($exam_ID) {
    $sql = "
    SELECT s.student_ID, s.username, s.first_name, s.last_name, s.student_email
    FROM student s
    JOIN exam_user eu ON s.student_ID = eu.user_ID
    WHERE eu.exam_ID = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle CSV file upload if submitted
// Handle CSV file upload if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['student_csv']) && $_FILES['student_csv']['error'] === UPLOAD_ERR_OK) {
    $csvFile = $_FILES['student_csv']['tmp_name'];
    $csvFileName = $_FILES['student_csv']['name']; // Get the original filename
    $csvStudents = [];

    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Skip header row if exists
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) >= 4) {
                $csvStudents[] = [
                    'username' => trim($data[0]),
                    'first_name' => trim($data[1]),
                    'last_name' => trim($data[2]),
                    'student_email' => trim($data[3])
                ];
            }
        }
        fclose($handle);

        // Store CSV students and filename in session
        $_SESSION['csv_students'] = $csvStudents;
        $_SESSION['csv_filename'] = $csvFileName; // Store the filename
        $_SESSION['success'] = "CSV file '" . htmlspecialchars($csvFileName) . "' uploaded successfully. Students will be merged with existing list";
        header("Location: generate_exam_step2.php?question_ID=" . $question_ID);
        exit();
    } else {
        $_SESSION['error'] = "Could not read the uploaded CSV file";
    }
}

// Merge with CSV students if available
if (isset($_SESSION['csv_students'])) {
    $existingIds = array_column($students, 'student_ID');
    foreach ($_SESSION['csv_students'] as $csvStudent) {
        if (!in_array($csvStudent['student_ID'], $existingIds)) {
            $students[] = $csvStudent;
            $existingIds[] = $csvStudent['student_ID'];
        }
    }
}

// Handle form submission to next step
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_students'])) {
    // Process the student data
    $submittedStudents = $_POST['students'] ?? [];
    $validStudents = [];

    foreach ($submittedStudents as $student) {
        if (
            !empty($student['username']) && !empty($student['first_name']) &&
            !empty($student['last_name']) && !empty($student['student_email'])
        ) {
            $validStudents[] = [
                'username' => $student['username'],
                'first_name' => $student['first_name'],
                'last_name' => $student['last_name'],
                'student_email' => $student['student_email']
            ];
        }
    }

    if (count($validStudents) > 0) {
        // Store all required data in session
        $_SESSION['exam_data'] = [
            'exam_ID' => $exam_ID,
            'question_ID' => $question_ID,
            'students' => $validStudents
        ];

        // Redirect to step 3 with data
        header("Location: generate_exam_step3.php");
        exit();
    } else {
        $_SESSION['error'] = "Please add at least one valid student before continuing";
    }
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
        <div class="card p-4 shadow-lg login-card text-white w-100" style="max-width: 600px;">
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

            <!-- CSV Upload Section -->
            <div class="card mb-3 text-dark">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upload Student List (CSV)</h5>
                    <?php if (isset($_SESSION['csv_filename'])): ?>
                        <span class="badge bg-info text-dark">
                            <?php echo htmlspecialchars($_SESSION['csv_filename']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="question_ID" value="<?php echo htmlspecialchars($question_ID); ?>">
                        <div class="mb-3">
                            <label for="student_csv" class="form-label">CSV Format:</label>
                            <div class="alert alert-info p-2 mb-2 small">
                                username,first_name,last_name,student_email
                            </div>
                            <input class="form-control form-control-sm" type="file" id="student_csv" name="student_csv"
                                accept=".csv">
                            <div class="form-text small">First row will be skipped (header). File must contain 4 columns
                                in order.</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Upload CSV</button>
                    </form>
                </div>
            </div>

            <!-- Student List Form -->
            <!-- Student List Form -->
            <form method="POST">
                <input type="hidden" name="exam_ID" value="<?php echo htmlspecialchars($exam_ID); ?>">
                <input type="hidden" name="question_ID" value="<?php echo htmlspecialchars($question_ID); ?>">

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" id="studentTable">
                        <thead class="table-light text-dark">
                            <tr>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $i => $student): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm text-dark"
                                            name="students[<?php echo $i; ?>][username]"
                                            value="<?php echo htmlspecialchars($student['username'] ?? ''); ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm text-dark"
                                            name="students[<?php echo $i; ?>][first_name]"
                                            value="<?php echo htmlspecialchars($student['first_name']); ?>" required></td>
                                    <td><input type="text" class="form-control form-control-sm text-dark"
                                            name="students[<?php echo $i; ?>][last_name]"
                                            value="<?php echo htmlspecialchars($student['last_name']); ?>" required></td>
                                    <td><input type="email" class="form-control form-control-sm text-dark"
                                            name="students[<?php echo $i; ?>][student_email]"
                                            value="<?php echo htmlspecialchars($student['student_email']); ?>" required>
                                    </td>
                                    <td><button type="button" class="btn btn-sm btn-danger"
                                            onclick="this.closest('tr').remove()">Remove</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                </br>
                <button type="button" class="btn btn-light w-100 mb-3" onclick="addStudentRow()">Add Student</button>

                <div class="d-flex justify-content-between">
                    <a href="generate_exam_files.php" class="btn btn-secondary">Previous Step</a>
                    <button type="submit" name="submit_students" class="btn btn-primary">Next Step</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-danger w-100">Return to Dashboard</a>
            </div>
        </div>
    </div>
    <script>
        let studentCount = <?php echo count($students); ?>;

        function addStudentRow() {
            const table = document.getElementById('studentTable').querySelector('tbody');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm text-dark" name="students[${studentCount}][student_ID]" required></td>
                <td><input type="text" class="form-control form-control-sm text-dark" name="students[${studentCount}][first_name]" required></td>
                <td><input type="text" class="form-control form-control-sm text-dark" name="students[${studentCount}][last_name]" required></td>
                <td><input type="email" class="form-control form-control-sm text-dark" name="students[${studentCount}][student_email]" required></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">Remove</button></td>
            `;
            table.appendChild(row);
            studentCount++;
        }

        // Validate at least one student exists before proceeding
        document.querySelector('form').addEventListener('submit', function (e) {
            if (e.submitter && e.submitter.name === 'submit_students') {
                const rows = document.querySelectorAll('#studentTable tbody tr');
                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one student (either manually or via CSV) before continuing.');
                }
            }
        });

        // CSV file validation
        document.getElementById('student_csv').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.name.split('.').pop().toLowerCase() !== 'csv') {
                alert('Please upload a CSV file only.');
                e.target.value = '';
            }
        });
    </script>
</body>

</html>