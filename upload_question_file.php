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

// Restrict access to only Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Coordinators can add questions.";
    header("Location: subjects.php");
    exit();
}

// Ensure an exam_ID exists in session
if (!isset($_SESSION['exam_ID'])) {
    $_SESSION['error'] = "Exam ID not found. Please start again.";
    header("Location: create_exam_questions.php");
    exit();
}

// Create exam_ID variable from the session exam_ID
$exam_ID = $_SESSION['exam_ID'];

// Handle the csv upload
if (isset($_POST['upload'])) {
    $filename = $_FILES["file"]["tmp_name"];
    $successCount = 0;
    $errors = [];
    $response = ['success' => false, 'message' => ''];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");

        while (($row = fgetcsv($file)) !== FALSE) {
            $insert_sql = 'INSERT INTO question (exam_ID, contents, time_created) VALUES (?, ?, NOW())';
            $contents = $row[0];
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('ss', $exam_ID, $contents);

            if ($insert_stmt->execute()) {
                $successCount++;
            } else {
                $errors[] = "Error adding question: " . $conn->error;
            }
        }

        fclose($file);

        if ($successCount > 0) {
            $message = "Successfully added $successCount questions.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            $_SESSION['success'] = $message; // Still set session for page reload
            $response = [
                'success' => true,
                'message' => $message
            ];
        } else {
            $message = !empty($errors) ? implode("\n", $errors) : "No questions were added.";
            $_SESSION['error'] = $message;
            $response = [
                'success' => false,
                'message' => $message
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => "The file is empty."
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(); // Important to prevent HTML output
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Question File</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="create_exam_step2.php">
                    <u>Back</u>
            </div>
            <div class="text-center">
                <a href="create_exam_step2.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>
                    Welcome, you are logged in as
                    <strong>
                        <?php echo htmlspecialchars(implode(' & ', $roles)); ?>
                    </strong>
                </h4>


                <p>Upload a question file</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="" enctype="multipart/form-data" id="upload-form">
                    <!-- Hidden file input that will be triggered by the drop area -->
                    <input type="file" name="file" id="file-input" class="d-none" accept=".csv" required>
                    <label for="file-input" class="btn btn-light mb-3">Select CSV File</label>

                    <!-- Drag and drop area -->
                    <div class="upload-container mt-4">
                        <div id="drop-area">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="70" fill="currentColor"
                                     class="bi bi-cloud-upload-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H7.5V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11h4.188C14.502 11 16 9.57 16 7.773c0-1.636-1.242-2.969-2.834-3.194C12.923 1.999 10.69 0 8 0m-.5 14.5V11h1v3.5a.5.5 0 0 1-1 0" />
                                </svg>
                            </div>
                            <p class="drop-instructions">Or Drag & drop your CSV file here</p>
                            <div class="preview mt-3" id="preview"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" name="upload" class="btn btn-light" id="upload-btn">Upload Questions</button>
                        <a href="create_exam_step3.php" class="btn btn-light">Next</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--call external js file -->
    <script src="drag_to_upload_csv_questions.js">
    </script>
</body>

</html>