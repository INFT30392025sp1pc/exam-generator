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

// If the user is not an Administrator, redirect with an error
if ($role !== 'Administrator') {
    $_SESSION['error'] = "Access Denied. Only Administrators can add users.";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $hashed_password = md5('test'); // This will need to be randomized / changed later
    $subject = $_POST['subject'];
    $new_role = $_POST['role'];
    $email = $_POST['email'];

    // Check if user already exists
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $new_username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        // Insert new user
        $insert_sql = "INSERT INTO users (username, password, role, subject, email) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", $new_username, $hashed_password, $new_role, $subject, $email);
        
        if ($insert_stmt->execute()) {
            // Send an email to the new user
            $to = $email;
            $subject = "Set Your Password";
            $message = "Hello $new_username,\n\nAn account has been created for you.\n\nUsername: $new_username\nTemporary Password: $initial_password\n\nPlease log in and change your password immediately.\n\nBest regards,\nAdmin Team";
            $headers = "From: UniSA Exam Generator";
            //mail($to, $subject, $message, $headers); Uncomment this when we are able to send email

            $_SESSION['success'] = "User created successfully. An email has been sent.";
            header("Location: users.php");
            exit();
        } else {
            $error = "Error creating user.";
        }
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
        <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220">
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>
                <p>Please complete the fields below to add a new user:</p>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="subject" required>
                            <option value="" disabled selected>Select Subject</option>
                            <option value="Math">Math</option>
                            <option value="Science">Science</option>
                            <option value="English">English</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select class="form-control" name="role" required>
                            <option value="" disabled selected>Select Role (Coordinator, Tutor)</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Tutor">Tutor</option>
                        </select>
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
</body>
</html>
