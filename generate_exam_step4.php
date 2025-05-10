<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php');
require_once('fpdf.php'); // FPDF Library
require_once('src/autoload.php'); // FPDI Library

use setasign\Fpdi\Fpdi;

$exam_ID = $_GET['exam_ID'] ?? null;

if (!$exam_ID) {
    $_SESSION['error'] = "No exam selected.";
    header("Location: generate_exam_files.php");
    exit();
}

// Ensure only Coordinators can access
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT user_role FROM user WHERE user_email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['user_role'] ?? 'User';

if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can access this step.";
    header("Location: dashboard.php");
    exit();
}

// Count total pending exams based on number of students
$count_stmt = $conn->prepare("SELECT COUNT(DISTINCT user_ID) FROM exam_user WHERE exam_ID = ?");
$count_stmt->bind_param("i", $exam_ID);
$count_stmt->execute();
$count_result = $count_stmt->get_result()->fetch_row();
$pending_count = $count_result[0] ?? 0;


// Ensure PDF folder exists
$pdf_dir = 'generated_pdfs/';
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

$generated_files = [];

// Handle PDF generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    // Clear previous files
    array_map('unlink', glob($pdf_dir . '*.pdf'));

    // Fetch students linked to exams (include exam_ID!)
    $stmt3 = $conn->prepare("
    SELECT s.student_ID, s.first_name, s.last_name, s.student_email, eu.exam_ID
    FROM student s
    JOIN exam_user eu ON s.student_ID = eu.user_ID
    WHERE eu.exam_ID = ?
");
    $stmt3->bind_param("i", $exam_ID);
    $stmt3->execute();
    $result = $stmt3->get_result();

    while ($row = $result->fetch_assoc()) {
        $name = "{$row['first_name']} {$row['last_name']}";
        $sanitizedFirst = preg_replace('/[^a-zA-Z0-9]/', '', $row['first_name']);
        $sanitizedLast = preg_replace('/[^a-zA-Z0-9]/', '', $row['last_name']);
        $filename = $pdf_dir . "Exam_{$sanitizedFirst}_{$sanitizedLast}_{$row['student_ID']}.pdf";


        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile('templates/truss_template_clean.pdf');

        // Import all cover pages
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $pdf->addPage();
            $pdf->useTemplate($tplIdx);
        }

        // Create new page for exam content
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Student Name: {$row['first_name']} {$row['last_name']}", 0, 1);
        $pdf->Cell(0, 10, "Student Email: {$row['student_email']}", 0, 1);
        $pdf->Ln(5);

        // Fetch questions for this student's exam
        $stmt2 = $conn->prepare("SELECT contents FROM question WHERE exam_ID = ?");
        $stmt2->bind_param("i", $row['exam_ID']);
        $stmt2->execute();
        $questionResult = $stmt2->get_result();

        $questionNumber = 1;

        while ($question = $questionResult->fetch_assoc()) {
            // Write Question Header
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, "QUESTION {$questionNumber}:", 0, 1);

            // Write Question Text
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 8, $question['contents']);
            $pdf->Ln(5); // Space between questions

            $questionNumber++;
        }

        // Fetch parameters for the exam
        $param_stmt = $conn->prepare("SELECT parameter_name, parameter_lower, parameter_upper FROM parameter WHERE exam_ID = ?");
        $param_stmt->bind_param("i", $row['exam_ID']);
        $param_stmt->execute();
        $param_result = $param_stmt->get_result();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Parameter Table", 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 10, 'Parameter', 1);
        $pdf->Cell(60, 10, 'Value', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        while ($param = $param_result->fetch_assoc()) {
            $value = rand($param['parameter_lower'], $param['parameter_upper']);
            $pdf->Cell(60, 10, $param['parameter_name'], 1);
            $pdf->Cell(60, 10, $value, 1);
            $pdf->Ln();
        }
        $pdf->Ln(10);

        // Add the truss image
        $img_stmt = $conn->prepare("SELECT truss_url FROM trussimage WHERE exam_ID = ? LIMIT 1");
        $img_stmt->bind_param("i", $row['exam_ID']);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        $img_row = $img_result->fetch_assoc();

        if ($img_row && file_exists($img_row['truss_url'])) {
            $pdf->Image($img_row['truss_url'], null, null, 150); // width 150mm
            $pdf->Ln(10);
        }

        $pdf->Output('F', $filename);
        $_SESSION['success'] = "Exam papers generated successfully";
        $generated_files[] = $filename;
    }

    $_SESSION['generated_files'] = $generated_files;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Generate Exams Step 4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white text-center" style="max-width: 600px;">
            <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            <h5>Generate Exam Papers</h5>

            <?php include('partials/alerts.php'); ?>
            <?php unset($_SESSION['success']); ?>

            <?php if (empty($_SESSION['generated_files'])): ?>
                <p class="text-white-50">(<?php echo $pending_count; ?> Pending Exams)</p>
                <form method="POST" action="">
                    <button type="submit" name="generate" class="btn btn-light my-3">Generate (Create PDFs)</button>
                </form>
            <?php endif; ?>


            <?php if (!empty($generated_files)): ?>
                <h6 class="mt-3">Generated Files</h6>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-bordered table-sm bg-white text-dark">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($generated_files as $file): ?>
                                <tr>
                                    <td><?php echo basename($file); ?></td>
                                    <td>
                                        <a href="<?php echo $file; ?>" class="btn btn-sm btn-success download-btn" download>
                                            Download
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-warning w-100 mt-3" onclick="downloadAll()">Download All PDFs</button>
            <?php else: ?>
            <?php endif; ?>

            <a href="dashboard.php" class="btn btn-danger w-100 mt-4">Return to Dashboard</a>
        </div>
    </div>
    <script>
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
                btn.textContent = 'Downloaded';
            });
        });

        function downloadAll() {
            const links = document.querySelectorAll('.download-btn');
            links.forEach(link => {
                const a = document.createElement('a');
                a.href = link.href;
                a.download = '';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        }
    </script>
    <?php unset($_SESSION['generated_files']); ?>

</body>

</html>