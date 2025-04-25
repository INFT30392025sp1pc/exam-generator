<?php
function log_activity(
    $conn,
    $event_type,
    $activity,
    $details = null,
    $entity_type = null,
    $entity_id = null
) {
    // Logs non-sensitive activity as documented at ...
    // Example usage:
    // log_activity($conn, 'session_start', 'User logged in');
    // log_activity($conn, 'session_end', 'User logged out');
    // log_activity($conn, 'error', 'Failed login', "Invalid credentials for $username");
    // log_activity($conn, 'activity', 'Created exam question', "Exam ID: $exam_id, Question ID: $question_id");
    // and more as needed by client
    // log_activity($conn, 'login', 'User logged in');
    // log_activity($conn, 'create_exam', 'Created exam', "Exam created for SP4", 'exam', $exam_id);

    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO dbmaster.activity_log
        (user_id, event_type, entity_type, entity_id, activity, details, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param(
        "ississsss",
        $user_id,
        $event_type,
        $entity_type,
        $entity_id,
        $activity,
        $details,
        $ip_address,
        $user_agent,
        $created_at
    );

    $result = $stmt->execute();
    if ($result === false) {
        error_log("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    return $result;
}
?>
