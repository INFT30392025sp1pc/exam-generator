<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's roles
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

// Restrict to Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Coordinators can upload trusses.";
    header("Location: dashboard.php");
    exit();
}

// Ensure exam_ID is available
if (!isset($_SESSION['exam_ID'])) {
    $_SESSION['error'] = "Exam ID not found. Please start again.";
    header("Location: create_exam_questions.php");
    exit();
}
$exam_ID = $_SESSION['exam_ID'];

// Handle image upload
if (isset($_POST['upload']) && isset($_FILES['truss_image'])) {
    $image = $_FILES['truss_image'];
    $truss_name = $_POST['truss_name'];

    // Validate file type
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
    if (!in_array($image['type'], $allowed_types)) {
        $_SESSION['error'] = "Only PNG, JPG, JPEG, or SVG images are allowed.";
        header("Location: upload_truss.php");
        exit();
    }

    $target_dir = "uploads/truss/";
    if (!is_dir($target_dir))
        mkdir($target_dir, 0755, true);
    $filename = uniqid("truss_") . "_" . basename($image["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $insert = $conn->prepare("INSERT INTO trussimage (truss_name, truss_url, exam_ID) VALUES (?, ?, ?)");
        $insert->bind_param("ssi", $truss_name, $target_file, $exam_ID);
        if ($insert->execute()) {
            $_SESSION['success'] = "Truss image uploaded successfully.";
        } else {
            $_SESSION['error'] = "Failed to save truss image to database.";
        }
    } else {
        $_SESSION['error'] = "Failed to move uploaded file.";
    }

    header("Location: upload_truss.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Truss Image</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="create_exam_step3.php">
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


                <p>Upload a truss file</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="truss_name" placeholder="Enter truss name"
                            required>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="truss_image" accept="image/*" class="form-control" required>
                    </div>
                    <button type="submit" name="upload" class="btn btn-light w-100 mb-2">Upload Truss</button>
                </form>
                <a href="create_exam_step4.php" class="btn btn-light w-100">Next</a>


                <body>
                    <div class="upload-container">
                        <div id="drop-area">
                            <div class="upload-icon">

                                <!--the upload svg icon is copied from Boostrap library-->
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="70" fill="currentColor"
                                    class="bi bi-cloud-upload-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H7.5V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11h4.188C14.502 11 16 9.57 16 7.773c0-1.636-1.242-2.969-2.834-3.194C12.923 1.999 10.69 0 8 0m-.5 14.5V11h1v3.5a.5.5 0 0 1-1 0" />
                                </svg>
                            </div>
                            <p class="drop-instructions">Or drag & drop files here</p>
                            <div class="preview" id="preview"></div>
                            <button type="button" class="btn btn-light w-100 mb-2" id="upload-btn">Upload Files</button>
                            <div id="status"></div>
                        </div>
                    </div>

                    <a href="create_exam_step4.php" class="btn btn-light w-100 mb-2">Next</a>
            </div>
        </div>
    </div>

    <!--call external js file -->
    <script src="drag_to_upload.js">
    </script>
</body>

</html>