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

// If the user is not an Administrator, redirect with an error
if (!in_array('Administrator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Administrators can add users.";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'];
    $email = $_POST['email'];

    // Check if user already exists
    $check_sql = "SELECT user_email FROM user WHERE user_email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "User email already exists!";
    } else {
        // Insert new user
        $insert_sql = "INSERT INTO user (user_password, first_name, last_name, user_email, user_role) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", md5($password), $first_name, $last_name, $email, $role);

        if ($insert_stmt->execute()) {
            $_SESSION['success'] = "User added successfully.";
            header("Location: users.php");
            exit();
        } else {
            $_SESSION['error'] = "Error adding user.";
            header("Location: users.php");
        }

        // if ($insert_stmt->execute()) {
        //     // Send an email to the new user
        //     $to = $email;
        //     $subject = "Set Your Password";
        //     $message = "Hello $new_username,\n\nAn account has been created for you.\n\nUsername: $new_username\nTemporary Password: $initial_password\n\nPlease log in and change your password immediately.\n\nBest regards,\nAdmin Team";
        //     $headers = "From: UniSA Exam Generator";
        //     //mail($to, $subject, $message, $headers); Uncomment this when we are able to send email

        //     $_SESSION['success'] = "User created successfully. An email has been sent.";
        //     header("Location: users.php");
        //     exit();
        // } else {
        //     $error = "Error creating user.";
        // }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
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
                <h4>
                    Welcome, you are logged in as
                    <strong>
                        <?php echo htmlspecialchars(implode(' & ', $roles)); ?>
                    </strong>
                </h4>

                <p>Please complete the fields below to add a new user:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <input class="form-control" name="email" placeholder="Enter email" type="email" required>
                    </div>
                    <div class="mb-3">
                        <input class="form-control" name="first_name" placeholder="Enter First Name" required>
                    </div>
                    <div class="mb-3">
                        <input class="form-control" name="last_name" placeholder="Enter Last Name" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="role" required>
                            <option value="" disabled selected>Select Role (Coordinator, Administrator)</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Administrator">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input class="form-control" name="password" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>