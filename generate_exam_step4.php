<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('db.php');
require_once('fpdf.php'); // FPDF Library
require_once('src/autoload.php'); // FPDI Library

use setasign\Fpdi\Fpdi;

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
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM exam_user");
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
    $sql = "
        SELECT s.student_ID, s.first_name, s.last_name, s.student_email, eu.exam_ID
        FROM student s
        JOIN exam_user eu ON s.student_ID = eu.user_ID
    ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $name = "{$row['first_name']} {$row['last_name']}";
        $filename = $pdf_dir . "Exam_Student_{$row['student_ID']}.pdf";

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

        $pdf->Output('F', $filename);

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
            <img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220">
            <h5>Generate Exam Papers</h5>
            <p class="text-white-50">(<?php echo $pending_count; ?> Pending Exams)</p>

            <form method="POST" action="">
                <button type="submit" name="generate" class="btn btn-light my-3">Generate (Create PDFs)</button>
            </form>

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
                                        <a href="<?php echo $file; ?>" class="btn btn-sm btn-success" download>Download</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <button class="btn btn-primary mt-3" disabled>Export ZIP (Generate first)</button>
            <?php endif; ?>

            <a href="dashboard.php" class="btn btn-danger w-100 mt-4">Return to Dashboard</a>
        </div>
    </div>
</body>

</html>
