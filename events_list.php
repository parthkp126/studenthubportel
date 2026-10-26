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
            description,
            category,
            poster_path
         FROM events
         WHERE event_date >= CURDATE()
           AND status = 'Active'
         ORDER BY event_date ASC"
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
        "message" => "Could not load events.",
        "data" => []
    ], JSON_UNESCAPED_UNICODE);
}