<?php
session_start();

if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "Admin") {
    http_response_code(403);
    exit("Unauthorized Access");
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/audit_log.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$eventId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventId = filter_input(INPUT_POST, "event_id", FILTER_VALIDATE_INT);
}

if (!$eventId) {
    http_response_code(400);
    exit("Invalid event ID.");
}

$stmt = $conn->prepare(
    "SELECT event_id, event_name
     FROM events
     WHERE event_id = :event_id"
);
$stmt->execute(["event_id" => $eventId]);
$event = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["confirm"] ?? "") === "yes") {
    if (!$event) {
        http_response_code(404);
        exit("Event not found.");
    }

    $archiveStmt = $conn->prepare(
        "UPDATE events
         SET status = 'Inactive'
         WHERE event_id = :event_id AND status = 'Active'"
    );
    $archiveStmt->execute(["event_id" => $eventId]);

    if ($archiveStmt->rowCount() > 0) {
        auditLog(
            "Event archived",
            "Event ID: " . $eventId . "; Name: " . $event["event_name"]
        );

        $archived = true;
        $message = "Event archived successfully.";
    } else {
        $archived = false;
        $message = "This event was already inactive.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Location: admin_events.php");
    exit;
} elseif (!$event) {
    http_response_code(404);
    exit("Event not found.");
} else {
    $archived = false;
    $message = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive Event</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f3f6fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 600px;
            margin: 40px auto;
            padding: 24px;
            background: #fff;
            border: 1px solid #dce3ed;
            border-radius: 12px;
        }

        button,
        a {
            display: inline-block;
            margin: 8px 8px 0 0;
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            background: #2459d3;
            color: #fff;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .archive-button {
            background: #c62828;
        }

        .success {
            padding: 12px;
            border-radius: 6px;
            background: #e7f7ed;
            color: #176534;
        }
    </style>
</head>
<body>
<main>
    <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["confirm"] ?? "") === "yes"): ?>
        <h1>Archive Event</h1>
        <p class="success"><?= escapeHtml($message) ?></p>
        <a href="admin_events.php">Back to Events</a>
    <?php else: ?>
        <h1>Confirm Event Archive</h1>
        <p>Are you sure you want to archive this event?</p>
        <p><strong><?= escapeHtml($event["event_name"]) ?></strong></p>

        <form method="POST">
            <input
                type="hidden"
                name="event_id"
                value="<?= escapeHtml($event["event_id"]) ?>"
            >

            <button
                class="archive-button"
                type="submit"
                name="confirm"
                value="yes">
                Archive Event
            </button>

            <button type="submit" name="confirm" value="no">
                Cancel
            </button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>