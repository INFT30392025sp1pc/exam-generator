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
    $_SESSION['error'] = "Access Denied. Only Administrators can modify users.";
    header("Location: users.php");
    exit();
}

// Fetch subjects for dropdown
$user_sql = "SELECT first_name, last_name, user_email, user_role FROM user ORDER BY last_name ASC";
$user_result = $conn->query($user_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = $_POST['users'];
    $new_role = $_POST['new_role'];

    // Handle password reset
    if ($new_role === "Password") {
        $defaultPassword = md5("ChangeMyPW01");
        $reset = $conn->prepare("UPDATE user SET user_password = ? WHERE user_email = ?");
        $reset->bind_param("ss", $defaultPassword, $user_email);
        if ($reset->execute()) {
            $_SESSION['success'] = "Password reset to default (ChangeMyPW01).";
        } else {
            $_SESSION['error'] = "Failed to reset password.";
        }
        header("Location: modify_user.php");
        exit();
    }

    // Get user ID
    $getUserID = $conn->prepare("SELECT user_ID FROM user WHERE user_email = ?");
    $getUserID->bind_param("s", $user_email);
    $getUserID->execute();
    $userResult = $getUserID->get_result();
    $userRow = $userResult->fetch_assoc();
    $user_ID = $userRow['user_ID'] ?? null;

    if (!$user_ID) {
        $_SESSION['error'] = "User not found.";
        header("Location: modify_user.php");
        exit();
    }

    if ($new_role === "Disabled") {
        // Remove all roles before deactivating
        $deleteRoles = $conn->prepare("DELETE FROM user_role_map WHERE user_ID = ?");
        $deleteRoles->bind_param("i", $user_ID);
        $deleteRoles->execute();

        // Assign Disabled role (assumed to be role_id 3)
        $disabledRoleID = 3;
        $assignDisabledRole = $conn->prepare("INSERT INTO user_role_map (user_ID, role_id) VALUES (?, ?)");
        $assignDisabledRole->bind_param("ii", $user_ID, $disabledRoleID);
        $assignDisabledRole->execute();
    } else {
        // Remove the Disabled role only if it exists
        $removeDisabled = $conn->prepare("DELETE FROM user_role_map WHERE user_ID = ? AND role_id = 3");
        $removeDisabled->bind_param("i", $user_ID);
        $removeDisabled->execute();

        // Get the role_id for the new role
        $getRoleID = $conn->prepare("SELECT role_id FROM role WHERE role_name = ?");
        $getRoleID->bind_param("s", $new_role);
        $getRoleID->execute();
        $roleResult = $getRoleID->get_result();
        $roleRow = $roleResult->fetch_assoc();
        $role_id = $roleRow['role_id'] ?? null;

        if (!$role_id) {
            $_SESSION['error'] = "Invalid role selected.";
            header("Location: modify_user.php");
            exit();
        }

        // Assign the new role
        // Check if the role already exists for this user
        $checkRole = $conn->prepare("SELECT 1 FROM user_role_map WHERE user_ID = ? AND role_id = ?");
        $checkRole->bind_param("ii", $user_ID, $role_id);
        $checkRole->execute();
        $checkResult = $checkRole->get_result();

        if ($checkResult->num_rows === 0) {
            $assignRole = $conn->prepare("INSERT INTO user_role_map (user_ID, role_id) VALUES (?, ?)");
            $assignRole->bind_param("ii", $user_ID, $role_id);
            $assignRole->execute();
        }

    }

    $_SESSION['success'] = "User updated successfully.";
    header("Location: modify_user.php");
    exit();
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
                        <select class="form-control" name="users" id="user_dropdown" required>
                            <option value="" disabled selected>Select User to Modify</option>
                            <?php while ($row = $user_result->fetch_assoc()) {
                                $user_text = $row['first_name'] . " " . $row['last_name'] . " (" . $row['user_role'] . ")";
                                ?>
                                <option value="<?php echo $row['user_email']; ?>"
                                    data-status="<?php echo $row['user_email']; ?>">
                                    <?php echo htmlspecialchars($row['user_email']) . " - $user_text"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="new_role" required>
                            <option value="" disabled selected>Select Action</option>
                            <option value="Coordinator" title="Grants ability to create and manage exams">Add Role:
                                Coordinator</option>
                            <option value="Administrator" title="Full access including user and subject management">Add
                                Role: Administrator</option>
                            <option value="Disabled" title="Removes all active roles and deactivates the user">
                                Deactivate User</option>
                            <option value="Password" title="Resets the password to 'ChangeMyPW01' using MD5">Set Default
                                Password (ChangeMyPW01)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>