<?php

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Get the logged-in username and role
$username = $_SESSION['username'];

$sql = "SELECT user_role FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['user_role'] ?? 'User';

// Restrict access to only Coordinators
if ($role !== 'Coordinator') {
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
if(isset($_POST['upload'])) {   // If the upload button is set (i.e. has been clicked)

    $filename = $_FILES["file"]["tmp_name"];  // Create filename variable (i.e. file uploaded)
 
    if($_FILES["file"]["size"] > 0) {   // If the file (named "file" in form) exists

        $file = fopen($filename, "r");  // Create opened file variable

        while (($row = fgetcsv($file)) !== FALSE) {  // Basically a loop through the csv

            $insert_sql = 'INSERT INTO question (exam_ID, contents, time_created) VALUES (?, ?, NOW())'; // SQL Statement
            $contents = $row[0];
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('ss', $exam_ID, $contents);
            
            if ($insert_stmt->execute()) {
                $_SESSION['success'] = "Questions added successfully.";
            } else {
                $error = "Error adding questions.";
            }
        }

        fclose($file);
    }

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
                <a href="create_exam_step2.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Upload a question file</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="file" name="file" class="form-control mb-3" required>
                    <button type="submit" name="upload" class="btn btn-light w-100 mb-2">Upload</button>
                </form>

                <body>
                <div class="upload-container">
                    <div id="drop-area">
                        <div class="upload-icon">

<!--                            the upload svg icon is copied from Boostrap library-->
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="70" fill="currentColor" class="bi bi-cloud-upload-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H7.5V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11h4.188C14.502 11 16 9.57 16 7.773c0-1.636-1.242-2.969-2.834-3.194C12.923 1.999 10.69 0 8 0m-.5 14.5V11h1v3.5a.5.5 0 0 1-1 0"/>
                            </svg>
                        </div>
                        <p class="drop-instructions">Or drag & drop files here</p>
                        <div class="preview" id="preview"></div>
                        <button type="button" class="btn btn-light w-100 mb-2" id="upload-btn">Upload Files</button>
                        <div id="status"></div>
                    </div>
                </div>

                <a href="create_exam_step3.php" class="btn btn-light w-100 mb-2">Next</a>
            </div>
        </div>
    </div>

<!--call external js file -->
    <script src="drag_to_upload.js">
    </script>
</body>

</html>
