<?php
session_start();

if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "Admin") {
    http_response_code(403);
    exit("403 Forbidden: Admin access required.");
}

require_once __DIR__ . "/db.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

try {
    $stmt = $conn->query(
        "SELECT id, name, email, message, created_at
         FROM contact_messages
         ORDER BY created_at DESC"
    );
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Could not load contact messages. Check that the contact_messages table exists.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>

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

        .panel,
        .message-card {
            margin-bottom: 18px;
            padding: 22px;
            background: #fff;
            border: 1px solid #dce3ed;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        .message-card h2 {
            margin: 0 0 8px;
        }

        .email,
        .date {
            color: #52627a;
        }

        .message-text {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        a {
            display: inline-block;
            margin: 4px 0 18px;
            color: #2459d3;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }
        }
    </style>
</head>

<body>
<main>
    <section class="panel">
        <h1>Contact Messages</h1>
        <p><?= count($messages) ?> message(s)</p>
        <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
    </section>

    <?php if (count($messages) === 0): ?>
        <section class="panel">
            <p>No contact messages yet.</p>
        </section>
    <?php else: ?>
        <?php foreach ($messages as $item): ?>
            <article class="message-card">
                <h2><?= escapeHtml($item["name"]) ?></h2>
                <p class="email">
                    Email:
                    <a href="mailto:<?= escapeHtml($item["email"]) ?>">
                        <?= escapeHtml($item["email"]) ?>
                    </a>
                </p>
                <p class="date">
                    Received: <?= escapeHtml($item["created_at"]) ?>
                </p>
                <p class="message-text"><?= escapeHtml($item["message"]) ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>