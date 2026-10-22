<?php
// audit_log.php
// Include this file from PHP pages when an admin action occurs.

require_once __DIR__ . "/db.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Record an admin action in the audit_logs table.
 *
 * Example:
 * auditLog("Event created", "Event ID: 12");
 */
function auditLog(string $action, string $details = ""): bool
{
    global $conn;

    try {
        $userId = isset($_SESSION["user_id"])
            ? (int) $_SESSION["user_id"]
            : null;

        $ipAddress = $_SERVER["REMOTE_ADDR"] ?? "unknown";

        $stmt = $conn->prepare(
            "INSERT INTO audit_logs (user_id, action, details, ip_address)
             VALUES (:user_id, :action, :details, :ip_address)"
        );

        $stmt->execute([
            "user_id" => $userId,
            "action" => $action,
            "details" => $details,
            "ip_address" => $ipAddress
        ]);

        return true;
    } catch (Throwable $e) {
        // Log the technical error without interrupting the user's action.
        error_log("Audit log error: " . $e->getMessage());
        return false;
    }
}