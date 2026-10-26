<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

try {
    $stmt = $conn->prepare(
        "SELECT
            event_id,
            event_name AS title,
            event_date AS date,
            venue,
            category
         FROM events
         WHERE event_date >= CURDATE()
         ORDER BY event_date ASC
         LIMIT 3"
    );

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll()
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Could not load upcoming events.",
        "data" => []
    ], JSON_UNESCAPED_UNICODE);
}