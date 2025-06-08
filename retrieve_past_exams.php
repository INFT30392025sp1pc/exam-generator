<?php
require_once 'functions.php';
enableDebug(false); // Set to false in production

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
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

// Restrict access to only Administrators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Administrators can modify users.";
    header("Location: users.php");
    exit();
}

$baseDir = 'generated_pdfs/';
$examBatches = [];

foreach (glob("{$baseDir}batch_*", GLOB_ONLYDIR) as $batchPath) {
    $csvFile = "{$batchPath}/exam_summary.csv";
    if (!file_exists($csvFile))
        continue;

    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Adjust CSV layout mapping based on actual CSV structure
    // Example: If the CSV is:
    // 0: "Exam Name", "Some Exam"
    // 1: "Subject Code", "ABC123"
    // 2: "Study Period", "SP1"
    // 3: "Date Generated", "2024-06-01"
    // Then:
    $examName = isset($lines[0]) ? (str_getcsv($lines[0])[1] ?? 'N/A') : 'N/A';
    $subjectCode = isset($lines[1]) ? (str_getcsv($lines[1])[1] ?? 'N/A') : 'N/A';
    $studyPeriod = isset($lines[2]) ? (str_getcsv($lines[2])[1] ?? 'N/A') : 'N/A';
    // Format the date if it matches the pattern YYYYMMDD_HHMMSS
    if (isset($lines[3])) {
        $dateRaw = str_getcsv($lines[3])[1] ?? basename($batchPath);
        if (preg_match('/^(\d{8})_(\d{6})$/', $dateRaw, $matches)) {
            // Extract date part
            $datePart = $matches[1]; // e.g., 20250605
            $dateObj = DateTime::createFromFormat('Ymd', $datePart);
            $generatedAt = $dateObj ? $dateObj->format('d/m/Y') : $dateRaw;
        } else {
            $generatedAt = $dateRaw;
        }
    } else {
        $generatedAt = basename($batchPath);
    }

    // From line 4 onward: count students (only if both name and email present)
    $studentCount = 0;
    for ($i = 4; $i < count($lines); $i++) {
        $row = str_getcsv($lines[$i]);
        if (!empty($row[0]) && !empty($row[1])) {
            $studentCount++;
        }
    }

    $examBatches[] = [
        'sp' => $studyPeriod,
        'subject' => $subjectCode,
        'name' => $examName,
        'date' => $generatedAt,
        'students' => $studentCount,
        'csv' => $csvFile,
        'path' => $batchPath
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieve Past Exams</title>
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
                    <u>Back</u></a>
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



                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <table class="table table-bordered table-hover table-sm bg-white text-dark">
                    <thead class="table-light">
                        <tr>
                            <th>Study Period</th>
                            <th>Subject Code</th>
                            <th>Exam Name</th>
                            <th>Number of Students</th>
                            <th>Date Generated</th>
                            <th>Download CSV</th>
                            <th>Download PDFs></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examBatches as $exam): ?>
                            <tr>
                                <td><?= htmlspecialchars($exam['sp']) ?></td>
                                <td><?= htmlspecialchars($exam['subject']) ?></td>
                                <td><?= htmlspecialchars($exam['name']) ?></td>
                                <td><?= $exam['students'] ?></td>
                                <td><?= htmlspecialchars($exam['date']) ?></td>
                                <td>
                                    <a href="<?= $exam['csv'] ?>" class="btn btn-primary btn-sm" download>CSV</a>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <?php
                                        $pdfLinks = glob("{$exam['path']}/*.pdf");
                                        foreach ($pdfLinks as $pdf) {
                                            $fileUrl = htmlspecialchars($pdf);
                                            echo "<a href='$fileUrl' class='download-btn d-none'></a>";
                                        }
                                        ?>
                                        <button onclick="downloadAll(this)" class="btn btn-success btn-sm">Download PDFs</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script>
    function downloadAll(button) {
        const row = button.closest('tr');
        const links = row.querySelectorAll('.download-btn');
        links.forEach(link => {
            const a = document.createElement('a');
            a.href = link.href;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        button.classList.remove('btn-success');
        button.classList.add('btn-secondary');
        button.textContent = 'Downloaded';
    }
</script>

</html>