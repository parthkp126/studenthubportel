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

$message = "";
$messageType = "";
$uploadDirectory = __DIR__ . "/event_uploads/";
$maxFileSize = 2 * 1024 * 1024;
$allowedMimeTypes = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp"
];

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventName = trim($_POST["event_name"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $posterPath = null;

    if ($eventName === "" || $eventDate === "" || $venue === "" || $category === "") {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } else {
        $date = DateTime::createFromFormat("Y-m-d", $eventDate);

        if (!$date || $date->format("Y-m-d") !== $eventDate) {
            $message = "Please enter a valid event date.";
            $messageType = "error";
        } elseif (
            isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES["poster"];

            if ($file["error"] !== UPLOAD_ERR_OK) {
                $message = "The poster upload failed. Please try again.";
                $messageType = "error";
            } elseif ($file["size"] > $maxFileSize) {
                $message = "Poster file size must be 2 MB or less.";
                $messageType = "error";
            } else {
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $fileInfo->file($file["tmp_name"]);

                if (!isset($allowedMimeTypes[$mimeType])) {
                    $message = "Invalid poster type. Upload a JPG, PNG, or WEBP image.";
                    $messageType = "error";
                } elseif (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
                    $message = "The event_uploads folder is missing or is not writable.";
                    $messageType = "error";
                } else {
                    $extension = $allowedMimeTypes[$mimeType];
                    $fileName = bin2hex(random_bytes(16)) . "." . $extension;
                    $destination = $uploadDirectory . $fileName;

                    if (!move_uploaded_file($file["tmp_name"], $destination)) {
                        $message = "Could not save the poster. Please try again.";
                        $messageType = "error";
                    } else {
                        $posterPath = "event_uploads/" . $fileName;
                    }
                }
            }
        }

        if ($message === "") {
            try {
                $stmt = $conn->prepare(
                    "INSERT INTO events
                        (event_name, event_date, venue, description, category, poster_path)
                     VALUES
                        (:event_name, :event_date, :venue, :description, :category, :poster_path)"
                );

                $stmt->execute([
    "event_name" => $eventName,
    "event_date" => $eventDate,
    "venue" => $venue,
    "description" => $description,
    "category" => $category,
    "poster_path" => $posterPath
]);

$eventId = (int) $conn->lastInsertId();
auditLog(
    "Event created",
    "Event ID: " . $eventId . "; Name: " . $eventName
);

$message = "Event created successfully.";
                $messageType = "success";
            } catch (PDOException $e) {
                if ($posterPath !== null && is_file(__DIR__ . "/" . $posterPath)) {
                    unlink(__DIR__ . "/" . $posterPath);
                }

                error_log($e->getMessage());
                $message = "Could not save the event. Please check the database.";
                $messageType = "error";
            }
        }
    }
}

$listStmt = $conn->query(
    "SELECT event_id, event_name, event_date, venue, description, category, poster_path
     FROM events
     ORDER BY event_date DESC, event_id DESC"
);
$events = $listStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Event Management</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f3f6fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 1000px;
            margin: 0 auto;
        }

        section {
            margin-bottom: 24px;
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
            resize: vertical;
        }

        button {
            margin-top: 16px;
            padding: 11px 18px;
            border: 0;
            border-radius: 6px;
            background: #2459d3;
            color: white;
            font: inherit;
            cursor: pointer;
        }

        .message {
            margin-bottom: 16px;
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

        .event {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 18px;
            padding: 18px 0;
            border-top: 1px solid #e2e8f0;
        }

        .event img {
            width: 180px;
            max-height: 130px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .event {
                grid-template-columns: 1fr;
            }

            .event img {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main>
    <h1>Admin Event Management</h1>

    <section>
        <h2>Create Event</h2>

        <?php if ($message !== ""): ?>
            <div class="message <?= escapeHtml($messageType) ?>" role="alert">
                <?= escapeHtml($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="event_name">Event name *</label>
            <input
                type="text"
                id="event_name"
                name="event_name"
                maxlength="100"
                required
                value="<?= escapeHtml($_POST["event_name"] ?? "") ?>"
            >

            <label for="event_date">Event date *</label>
            <input
                type="date"
                id="event_date"
                name="event_date"
                required
                value="<?= escapeHtml($_POST["event_date"] ?? "") ?>"
            >

            <label for="venue">Venue *</label>
            <input
                type="text"
                id="venue"
                name="venue"
                maxlength="100"
                required
                value="<?= escapeHtml($_POST["venue"] ?? "") ?>"
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
                foreach ($categories as $item):
                ?>
                    <option
                        value="<?= escapeHtml($item) ?>"
                        <?= (($_POST["category"] ?? "") === $item) ? "selected" : "" ?>
                    >
                        <?= escapeHtml($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description"><?= escapeHtml($_POST["description"] ?? "") ?></textarea>

            <label for="poster">Poster (JPG, PNG, or WEBP; maximum 2 MB)</label>
            <input
                type="file"
                id="poster"
                name="poster"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <button type="submit">Create Event</button>
        </form>
    </section>

    <section>
        <h2>Event List</h2>

        <?php if (count($events) === 0): ?>
            <p>No events have been added yet.</p>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <article class="event">
                    <div>
                        <?php if (!empty($event["poster_path"])): ?>
                            <img
                                src="<?= escapeHtml($event["poster_path"]) ?>"
                                alt="<?= escapeHtml($event["event_name"]) ?> poster"
                            >
                        <?php else: ?>
                            <p>No poster uploaded.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <h3><?= escapeHtml($event["event_name"]) ?></h3>
                        <p><strong>Date:</strong> <?= escapeHtml($event["event_date"]) ?></p>
                        <p><strong>Venue:</strong> <?= escapeHtml($event["venue"]) ?></p>
                        <p><strong>Category:</strong> <?= escapeHtml($event["category"]) ?></p>
                        <p><?= nl2br(escapeHtml($event["description"])) ?></p>
                        <p>
    <a href="edit_event.php?id=<?= urlencode((string) $event["event_id"]) ?>">
        Edit Event
    </a>
</p>
<p>
    <a href="delete_event.php?id=<?= urlencode((string) $event["event_id"]) ?>">
        Delete Event
    </a>
</p>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>