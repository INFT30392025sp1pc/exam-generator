<?php
function log_activity(
    $conn,
    $event_type,
    $activity,
    $details = null,
    $entity_type = null,
    $entity_id = null
) {
    // Logs non-sensitive activity as documented at ... [suggest creating kba for UI]
    // Examples for use in the application:
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
// Enables or disables PHP error reporting based on the passed flag
function enableDebug($enabled = false)
{
    if ($enabled) {
        // Show all errors on screen (useful for development)
        ini_set('display_errors', 1);             // Show runtime errors
        ini_set('display_startup_errors', 1);     // Show startup sequence errors
        error_reporting(E_ALL);                   // Report all types of errors
    } else {
        // Suppress errors from being shown on screen (recommended for production)
        ini_set('display_errors', 0);
        error_reporting(0);
    }
}

?>