<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

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
if (!in_array('Administrator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Administrators can modify subjects.";
    header("Location: subjects.php");
    exit();
}

// Fetch subjects for dropdown
$subjects_sql = "SELECT subject_name, subject_archive FROM subject ORDER BY subject_name ASC";
$subjects_result = $conn->query($subjects_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_code = $_POST['subject_code'];
    $subject_archive = isset($_POST['subject_archive']) ? 1 : 0; // Checkbox toggle

    $update_sql = "UPDATE subject SET subject_archive = ? WHERE subject_code = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("is", $subject_archive, $subject_code);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Subject updated successfully.";
        header("Location: subjects.php");
        exit();
    } else {
        $error = "Error updating subject.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Subject</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for handling status update -->
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="subjects.php">
                    <u>Back</u>
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

                <form method="POST" action="">
                    <div class="mb-3">
                        <select class="form-control" name="subject_code" id="subjectDropdown" required>
                            <option value="" disabled selected>Select subject to modify</option>
                            <?php while ($row = $subjects_result->fetch_assoc()) {
                                $status_text = $row['subject_archive'];
                                ?>
                                <option value="<?php echo $row['subject_archive']; ?>"
                                    data-status="<?php echo $row['subject_archive']; ?>">
                                    <?php echo htmlspecialchars($row['subject_name']) . " ($status_text)"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <p>What would you like to do?</p>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="subject_code" id="toggleSwitch">
                        <label class="form-check-label" for="toggleSwitch">Toggle on to enable subject, toggle off to
                            archive</label>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#subjectDropdown").change(function () {
                var selectedOption = $(this).find("option:selected");
                var status = selectedOption.data("status");

                // Set toggle switch based on subject status
                $("#toggleSwitch").prop("checked", status == 1);
            });
        });
    </script>
</body>

</html>