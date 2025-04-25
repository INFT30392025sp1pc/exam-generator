<?php
function log_activity($conn, $event_type, $activity, $details = null) {
    // Logs non-sensitive activity as documented at...
    // Example usage: 
    // log_activity($conn, 'session_start', 'User logged in');
    // log_activity($conn, 'session_end', 'User logged out');
    // log_activity($conn, 'error', 'Failed login', "Invalid credentials for $username");
    // log_activity($conn, 'activity', 'Created exam question', "Exam ID: $exam_id, Question ID: $question_id");
    // and more as needed by client

    $user_id = $_SESSION['user_id'] ?? null;
    $user_email = $_SESSION['username'] ?? null; // Assuming 'username' is user_email
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $sql = "INSERT INTO `dbmaster`.`activity_log`
        (user_id, user_email, event_type, activity, details, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssss",
        $user_id,
        $user_email,
        $event_type,
        $activity,
        $details,
        $ip_address,
        $user_agent
    );
    $stmt->execute();
    $stmt->close();
}
?>
