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
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Please enter details for the exam to be generated</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="generate_exam_step2.php">
                    <div class="mb-3">
                        <select class="form-control" name="question_ID" required>
                            <option value="" disabled selected>Select Question Set</option>
                            <?php foreach ($question_files as $file) { ?>
                                <option value="<?php echo $file['question_ID']; ?>">
                                    <?php echo htmlspecialchars("{$file['subject_code']} - SP{$file['exam_sp']} {$file['exam_year']}"); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Placeholder for Student File -->
                    <div class="mb-3">
                        <select class="form-control" name="student_file_uuid" required>
                            <option value="" disabled selected>Select Student File</option>
                            <!-- Options to be populated with PHP later -->
                        </select>
                    </div>

                    <!-- Placeholder for Template File -->
                    <div class="mb-3">
                        <select class="form-control" name="template_file_uuid" required>
                            <option value="" disabled selected>Select Template File</option>
                            <!-- Options to be populated with PHP later -->
                        </select>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Next</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
