<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

try {
    $stats = [
        "users" => (int) $conn
            ->query("SELECT COUNT(*) FROM users")
            ->fetchColumn(),

        "activeStudents" => (int) $conn
            ->query("SELECT COUNT(*) FROM students WHERE status = 'Active'")
            ->fetchColumn(),

        "upcomingEvents" => (int) $conn
            ->query(
                "SELECT COUNT(*) FROM events
                 WHERE event_date >= CURDATE()
                   AND status = 'Active'"
            )
            ->fetchColumn(),

        "registrations" => (int) $conn
            ->query("SELECT COUNT(*) FROM registrations")
            ->fetchColumn()
    ];

    echo json_encode([
        "success" => true,
        "data" => $stats
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Could not load dashboard statistics."
    ], JSON_UNESCAPED_UNICODE);
}