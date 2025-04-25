<?php
session_start();
include('db.php');
include('functions.php'); // Contains log_activity()

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Hash the password using MD5 (current policy)
    $password_md5 = md5($password);

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT user_ID, user_email, user_password, user_role, first_name, last_name 
            FROM dbmaster.user WHERE user_email = ? AND user_password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password_md5);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_ID, $user_email, $user_password, $user_role, $first_name, $last_name);
        $stmt->fetch();

        // Set session variables
        $_SESSION['username'] = $user_email;
        $_SESSION['user_id'] = $user_ID;
        $_SESSION['user_role'] = $user_role;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        // Log successful login
        log_activity($conn, 'session_start', 'User logged in');

        header("Location: dashboard.php");
        exit();
    } else {
        // Log failed login
        log_activity($conn, 'error', 'Failed login', "Invalid credentials for $username");

        $_SESSION['error'] = "Invalid username or password!";
        header("Location: login.php");
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white" style="min-width:350px;">
            <div class="text-center">
                <img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220">
                <h3>Login</h3>
            </div>

            <!-- Displays error or success message if one is available -->
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Email</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                            Show
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark w-100">Login</button>
            </form>
            <div class="text-center mt-2">
                <a href="forgot_password.php" class="text-white">Forgotten password</a>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.textContent = type === 'password' ? 'Show' : 'Hide';
    });
    </script>
</body>
</html>
