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

// Restrict access to only Administrators
if ($role !== 'Administrator') {
    $_SESSION['error'] = "Access Denied. Only Administrators can modify users.";
    header("Location: users.php");
    exit();
}

// Fetch subjects for dropdown
$user_sql = "SELECT first_name, last_name, user_email, user_role FROM user ORDER BY last_name ASC";
$user_result = $conn->query($user_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_code = $_POST['subject_code'];
    $subject_archive = isset($_POST['subject_archive']) ? 1  : 0; // Checkbox toggle

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
    <title>Modify User</title>
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
                <a href="users.php">
                <u>Back</u>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <select class="form-control" name="users" id="user_Dropdown" required>
                            <option value="" disabled selected>Select user to modify</option>
                            <?php while ($row = $user_result->fetch_assoc()) { 
                                $user_text = $row['last_name'] . " (" . $row['user_role'] . ")";
                            ?>
                                <option value="<?php echo $row['first_name']; ?>" data-status="<?php echo $row['first_name']; ?>">
                                    <?php echo htmlspecialchars($row['first_name']) . " $user_text"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <p>What would you like to do?</p>
                    

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#subjectDropdown").change(function() {
                var selectedOption = $(this).find("option:selected");
                var status = selectedOption.data("status");
                
                // Set toggle switch based on subject status
                $("#toggleSwitch").prop("checked", status == 1);
            });
        });
    </script>
</body>
</html>

