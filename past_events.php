<?php
require_once __DIR__ . "/db.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

try {
    $stmt = $conn->query(
    "SELECT event_id, event_name, event_date, venue, description, category, poster_path
     FROM events
     WHERE event_date < CURDATE()
     ORDER BY event_date DESC"
);

    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Could not load past events. Check the events table.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Events - Student Portal Hub</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .past-events-page {
            max-width: 1100px;
            margin: 40px auto;
            padding: 24px;
        }

        .past-events-heading {
            margin-bottom: 24px;
        }

        .past-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .past-event-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid #dce3ed;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, 0.07);
        }

        .past-event-card img {
            display: block;
            width: 100%;
            height: 190px;
            object-fit: cover;
        }

        .past-event-content {
            padding: 20px;
        }

        .past-event-content h2 {
            margin-top: 0;
        }

        .past-event-date,
        .past-event-meta {
            color: #52627a;
        }

        .past-events-empty {
            padding: 22px;
            background: #fff;
            border-radius: 12px;
        }

        body.dark .past-event-card,
        body.dark .past-events-empty {
            background: #1e293b;
            color: #e5e7eb;
            border-color: #475569;
        }

        body.dark .past-event-date,
        body.dark .past-event-meta {
            color: #cbd5e1;
        }
    </style>
</head>

<body>
<main class="past-events-page">
    <header class="past-events-heading">
        <p><a href="events.html">← Back to upcoming events</a></p>
        <h1>Past Events</h1>
        <p>Look back at events held at Student Portal Hub.</p>
    </header>

    <?php if (count($events) === 0): ?>
        <div class="past-events-empty">
            <p>No past events are available yet.</p>
        </div>
    <?php else: ?>
        <section class="past-events-grid" aria-label="Past events">
            <?php foreach ($events as $event): ?>
                <article class="past-event-card">
                    <?php if (!empty($event["poster_path"])): ?>
                        <img
                            src="<?= escapeHtml($event["poster_path"]) ?>"
                            alt="<?= escapeHtml($event["event_name"]) ?> poster"
                            loading="lazy">
                    <?php endif; ?>

                    <div class="past-event-content">
                        <h2><?= escapeHtml($event["event_name"]) ?></h2>
                        <p class="past-event-date">
                            Date: <?= escapeHtml($event["event_date"]) ?>
                        </p>
                        <p class="past-event-meta">
                            <?= escapeHtml($event["category"] ?? "") ?>
                            <?php if (!empty($event["venue"])): ?>
                                · <?= escapeHtml($event["venue"]) ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($event["description"])): ?>
                            <p><?= nl2br(escapeHtml($event["description"])) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>