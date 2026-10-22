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
    "SELECT event_id, event_name, event_date, venue, description, category
     FROM events
     WHERE event_id = :event_id"
);
$stmt->execute(["event_id" => $eventId]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);
    exit("Event not found.");
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventName = trim($_POST["event_name"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");

    $date = DateTime::createFromFormat("Y-m-d", $eventDate);
    $validDate = $date && $date->format("Y-m-d") === $eventDate;

    if ($eventName === "" || $eventDate === "" || $venue === "" || $category === "") {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!$validDate) {
        $message = "Please enter a valid event date.";
        $messageType = "error";
    } else {
        try {
            $updateStmt = $conn->prepare(
                "UPDATE events
                 SET event_name = :event_name,
                     event_date = :event_date,
                     venue = :venue,
                     description = :description,
                     category = :category
                 WHERE event_id = :event_id"
            );

            $updateStmt->execute([
                "event_name" => $eventName,
                "event_date" => $eventDate,
                "venue" => $venue,
                "description" => $description,
                "category" => $category,
                "event_id" => $eventId
            ]);

            auditLog(
                "Event updated",
                "Event ID: " . $eventId . "; Name: " . $eventName
            );

            $stmt->execute(["event_id" => $eventId]);
            $event = $stmt->fetch();

            $message = "Event updated successfully.";
            $messageType = "success";
        } catch (PDOException $e) {
            error_log("Event update error: " . $e->getMessage());
            $message = "Could not update the event. Please check the database.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f3f6fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 700px;
            margin: 30px auto;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #dce3ed;
            border-radius: 12px;
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            border: 1px solid #b9c4d3;
            border-radius: 6px;
            font: inherit;
        }

        textarea {
            min-height: 100px;
        }

        button,
        .back-link {
            display: inline-block;
            margin-top: 16px;
            padding: 11px 18px;
            border: 0;
            border-radius: 6px;
            background: #2459d3;
            color: #ffffff;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
        }

        .success {
            background: #e7f7ed;
            color: #176534;
        }

        .error {
            background: #fdeaea;
            color: #9d2020;
        }
    </style>
</head>
<body>
<main>
    <h1>Edit Event</h1>

    <?php if ($message !== ""): ?>
        <p class="message <?= escapeHtml($messageType) ?>" role="alert">
            <?= escapeHtml($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input
            type="hidden"
            name="event_id"
            value="<?= escapeHtml($event["event_id"]) ?>"
        >

        <label for="event_name">Event name *</label>
        <input
            type="text"
            id="event_name"
            name="event_name"
            maxlength="100"
            required
            value="<?= escapeHtml($_POST["event_name"] ?? $event["event_name"]) ?>"
        >

        <label for="event_date">Event date *</label>
        <input
            type="date"
            id="event_date"
            name="event_date"
            required
            value="<?= escapeHtml($_POST["event_date"] ?? $event["event_date"]) ?>"
        >

        <label for="venue">Venue *</label>
        <input
            type="text"
            id="venue"
            name="venue"
            maxlength="100"
            required
            value="<?= escapeHtml($_POST["venue"] ?? $event["venue"]) ?>"
        >

        <label for="category">Category *</label>
        <select id="category" name="category" required>
            <option value="">Select a category</option>
            <?php
            $categories = [
                "Technology",
                "Competition",
                "Career",
                "Sports",
                "Cultural",
                "Workshop"
            ];
            $selectedCategory = $_POST["category"] ?? $event["category"];

            foreach ($categories as $item):
            ?>
                <option
                    value="<?= escapeHtml($item) ?>"
                    <?= $selectedCategory === $item ? "selected" : "" ?>
                >
                    <?= escapeHtml($item) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= escapeHtml($_POST["description"] ?? $event["description"]) ?></textarea>

        <button type="submit">Update Event</button>
    </form>

    <a class="back-link" href="admin_events.php">Back to Events</a>
</main>
</body>
</html>
