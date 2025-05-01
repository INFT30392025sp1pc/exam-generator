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

// Assign role variable
$role = $user['user_role'] ?? 'User';

// Restrict access to only Subject Coordinators
if ($role !== 'Coordinator') {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can review question lists.";
    header("Location: dashboard.php");
    exit();
}

// Ensure an exam_uuid exists in session
if (!isset($_SESSION['exam_ID'])) {
    $_SESSION['error'] = "Exam session not found. Please start again.";
    header("Location: create_exam.php");
    exit();
}

// Gain the current exam_ID so that only the correct questions are selected
$exam_ID = $_SESSION['exam_ID'];

// Handle the question selection
$select_stmt = "SELECT contents FROM question WHERE exam_ID = ?";
$select_stmt = $conn->prepare($select_stmt);
$select_stmt -> bind_param("s", $exam_ID);
$select_stmt -> execute();
$select_result = $select_stmt->get_result(); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Question List</title>
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
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Review question list</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <p>📄 List of questions here</p>
                <!-- The below php uses the result from the earlier sql query and loops through the data collecting only the "contents" or actual question -->
                <textarea class="form-control mb-3" id="questionList" rows="8"><?php 
                        if ($select_result->num_rows >0) {
                            while($row = $select_result->fetch_assoc()) {
                                echo $row["contents"]."\r\n";
                            }
                        }
                    ?>
                </textarea>

                <button type="button" class="btn btn-light w-100 mb-2" id="saveBtn">Save</button>
            </div>
        </div>
    </div>
                        
    <script>
        document.getElementById("saveBtn").addEventListener("click", function() {
            let questionList = document.getElementById("questionList").value;
            fetch("save_questions.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "exam_ID=<?php echo $exam_ID; ?>&questions=" + encodeURIComponent(questionList)
                })
                .then(response => response.text())
                .then(data => alert(data));
        });
    </script>
</body>

</html>
