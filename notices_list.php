<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

try {
    $stmt = $conn->prepare(
        "SELECT notice_id, title, message, created_at
         FROM portal_notices
         WHERE status = 'Active'
           AND (expires_at IS NULL OR expires_at >= CURDATE())
         ORDER BY created_at DESC
         LIMIT 3"
    );

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll()
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Notice API error: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Could not load notices.",
        "data" => []
    ], JSON_UNESCAPED_UNICODE);
}