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

    // Takes the user_email in the dropdown to define where to update in the db
    $user_email = $_POST['users'];
    $new_role = $_POST['new_role'];

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

    // Remove all existing roles
    $deleteRoles = $conn->prepare("DELETE FROM user_role_map WHERE user_ID = ?");
    $deleteRoles->bind_param("i", $user_ID);
    $deleteRoles->execute();

    if ($new_role === "Disabled") {
        // Explicitly assign role_id = 3 (Disabled)
        $disabledRoleID = 3;
        $assignDisabledRole = $conn->prepare("INSERT INTO user_role_map (user_ID, role_id) VALUES (?, ?)");
        $assignDisabledRole->bind_param("ii", $user_ID, $disabledRoleID);
        $assignDisabledRole->execute();
    } else {
        // Get role ID from role name
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

        // Assign new role
        $assignRole = $conn->prepare("INSERT INTO user_role_map (user_ID, role_id) VALUES (?, ?)");
        $assignRole->bind_param("ii", $user_ID, $role_id);
        $assignRole->execute();
    }


    $_SESSION['success'] = "User updated successfully.";
    header("Location: modify_user.php");
    exit();


    if ($update_stmt->execute()) {
        $_SESSION['success'] = "User updated successfully";
        header("Location: modify_user.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating user.";
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
                            <option value="" disabled selected>Select user to modify</option>
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
                    <p>Select new role</p>
                    <div class="mb-3">
                        <select class="form-control" name="new_role" required>
                            <option value="" disabled selected>Select New Role (Coordinator, Administrator) or action (Deactivate)</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Disabled">Deactivate User</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>