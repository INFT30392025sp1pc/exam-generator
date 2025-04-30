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
 
    if($_FILES["file"]["size"] > 0) {   // If the file (named "file" in form) exists, n

        $file = fopen($filename, "r");  // Create opened file variable

        while (($row = fgetcsv($file)) !== FALSE) {  // Basically a loop through the csv, the 100 specifies the maximum length of the csv

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

                <p>Or drag and drop file here</p>

                <!-- Drag and Drop Upload Box -->
                <div class="border p-3 mb-3 text-center" id="drop-area">
                    <input type="file" id="fileInput" name="file" class="d-none">
                    <label for="fileInput" class="btn btn-outline-light w-100">Add file</label>
                </div>

                <a href="create_exam_step3.php" class="btn btn-light w-100 mb-2">Next</a>
            </div>
        </div>
    </div>

    <script>
        // Drag and Drop File Upload Handling
        let dropArea = document.getElementById("drop-area");

        dropArea.addEventListener("dragover", function(event) {
            event.preventDefault();
            dropArea.classList.add("border-primary");
        });

        dropArea.addEventListener("dragleave", function(event) {
            dropArea.classList.remove("border-primary");
        });

        dropArea.addEventListener("drop", function(event) {
            event.preventDefault();
            dropArea.classList.remove("border-primary");
            let files = event.dataTransfer.files;
            document.getElementById("fileInput").files = files;
        });
    </script>
</body>

</html>
