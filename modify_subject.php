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

$sql = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role'] ?? 'User';

// Restrict access to only Administrators
if ($role !== 'Administrator') {
    $_SESSION['error'] = "Access Denied. Only Administrators can modify subjects.";
    header("Location: subjects.php");
    exit();
}

// Fetch subjects for dropdown
$subjects_sql = "SELECT uuid, subject_name, is_active FROM subjects ORDER BY subject_name ASC";
$subjects_result = $conn->query($subjects_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_uuid = $_POST['subject_uuid'];
    $is_active = isset($_POST['is_active']) ? 1 : 0; // Checkbox toggle

    $update_sql = "UPDATE subjects SET is_active = ? WHERE uuid = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("is", $is_active, $subject_uuid);

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for handling status update -->
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" width="150">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>

                <!-- Display Error Message -->
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <select class="form-control" name="subject_uuid" id="subjectDropdown" required>
                            <option value="" disabled selected>Select subject to modify</option>
                            <?php while ($row = $subjects_result->fetch_assoc()) { 
                                $status_text = $row['is_active'] ? "Active" : "Archived";
                            ?>
                                <option value="<?php echo $row['uuid']; ?>" data-status="<?php echo $row['is_active']; ?>">
                                    <?php echo htmlspecialchars($row['subject_name']) . " ($status_text)"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <p>What would you like to do?</p>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="toggleSwitch">
                        <label class="form-check-label" for="toggleSwitch">Toggle on to enable subject, toggle off to archive</label>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>

                <!-- Logout Button -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-outline-light w-100">Return to login screen</button>
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
