<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="custom.css">
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card p-4 shadow-lg login-card text-white">
                <div class="text-left">
                    <a href="login.php">
                        <u>Back</u></a>
                </div>
                <div class="text-center">
                    <img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3" width="220">
                    <h3>Forgotten Password</h3>
                    <p>If you have forgotten your password, please contact your administrator.</p>
                </div>
            </div>
        </div>
    </body>

    </html>