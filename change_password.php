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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_password = $_POST['new_password'];

    // Insert the new subject
    $update_sql = "UPDATE user SET user_password = ? WHERE user_email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", md5($user_password), $username);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Password changed successfully.";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error changing password.";
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
                <a href="dashboard.php">
                    <u>Back</u>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>Welcome, you are logged in as <strong><?php echo htmlspecialchars($role); ?></strong></h4>

                <!-- Displays error or success message if one is available -->
                <?php include('partials/alerts.php'); ?>

                <p>Please enter and confirm your new password:</p>

                <form method="POST" action="" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            placeholder="Enter new password" required>
                        <small id="passwordStrength" class="form-text text-white mt-1"></small>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Confirm new password" required>
                        <small id="matchStatus" class="form-text text-white mt-1"></small>
                    </div>
                    <button type="submit" class="btn btn-light w-100 mb-2">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[\W]/.test(password)) strength++;

            const output = document.getElementById("passwordStrength");
            if (strength <= 2) {
                output.textContent = "Weak password";
                output.style.color = "red";
            } else if (strength === 3) {
                output.textContent = "Moderate password";
                output.style.color = "orange";
            } else {
                output.textContent = "Strong password";
                output.style.color = "lightgreen";
            }
        }

        function checkMatch() {
            const pass = document.getElementById("new_password").value;
            const confirm = document.getElementById("confirm_password").value;
            const matchStatus = document.getElementById("matchStatus");

            if (pass && confirm) {
                if (pass === confirm) {
                    matchStatus.textContent = "Passwords match";
                    matchStatus.style.color = "lightgreen";
                } else {
                    matchStatus.textContent = "Passwords do not match";
                    matchStatus.style.color = "red";
                }
            } else {
                matchStatus.textContent = "";
            }
        }

        function validateForm() {
            const pass = document.getElementById("new_password").value;
            const confirm = document.getElementById("confirm_password").value;

            if (pass !== confirm) {
                alert("Passwords do not match.");
                return false;
            }
            if (pass.length < 8) {
                alert("Password must be at least 8 characters.");
                return false;
            }
            return true;
        }

        document.getElementById("new_password").addEventListener("input", function () {
            checkStrength(this.value);
            checkMatch();
        });
        document.getElementById("confirm_password").addEventListener("input", checkMatch);
    </script>
</body>

</html>