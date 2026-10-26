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
require_once __DIR__ . "/audit_log.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postedToken = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $postedToken)) {
        http_response_code(403);
        exit("Invalid form token. Reload the page and try again.");
    }

    $action = $_POST["action"] ?? "";

    if ($action === "deactivate") {
        $noticeId = filter_input(INPUT_POST, "notice_id", FILTER_VALIDATE_INT);

        if (!$noticeId) {
            $message = "Invalid notice ID.";
            $messageType = "error";
        } else {
            try {
                $stmt = $conn->prepare(
                    "UPDATE portal_notices
                     SET status = 'Inactive'
                     WHERE notice_id = :notice_id AND status = 'Active'"
                );
                $stmt->execute(["notice_id" => $noticeId]);

                if ($stmt->rowCount() > 0) {
                    auditLog("Notice deactivated", "Notice ID: " . $noticeId);
                    $message = "Notice deactivated. It will no longer appear publicly.";
                    $messageType = "success";
                } else {
                    $message = "Notice was not found or is already inactive.";
                    $messageType = "error";
                }
            } catch (PDOException $e) {
                error_log("Notice deactivation error: " . $e->getMessage());
                $message = "Could not deactivate the notice.";
                $messageType = "error";
            }
        }
    } elseif ($action === "create") {
        $title = trim($_POST["title"] ?? "");
        $noticeMessage = trim($_POST["message"] ?? "");
        $expiresAt = trim($_POST["expires_at"] ?? "");
        $expiryValue = null;

        if ($title === "" || $noticeMessage === "") {
            $message = "Please enter both a title and a notice message.";
            $messageType = "error";
        } elseif (mb_strlen($title) > 150) {
            $message = "The title must be 150 characters or fewer.";
            $messageType = "error";
        } elseif (mb_strlen($noticeMessage) > 5000) {
            $message = "The message must be 5000 characters or fewer.";
            $messageType = "error";
        } else {
            if ($expiresAt !== "") {
                $expiryDate = DateTime::createFromFormat("Y-m-d", $expiresAt);

                if (!$expiryDate || $expiryDate->format("Y-m-d") !== $expiresAt) {
                    $message = "Please enter a valid expiry date.";
                    $messageType = "error";
                } else {
                    $expiryValue = $expiresAt;
                }
            }

            if ($message === "") {
                try {
                    $stmt = $conn->prepare(
                        "INSERT INTO portal_notices
                            (title, message, expires_at, status)
                         VALUES
                            (:title, :message, :expires_at, 'Active')"
                    );

                    $stmt->execute([
                        "title" => $title,
                        "message" => $noticeMessage,
                        "expires_at" => $expiryValue
                    ]);

                    $noticeId = (int) $conn->lastInsertId();

                    auditLog(
                        "Notice created",
                        "Notice ID: " . $noticeId . "; Title: " . $title
                    );

                    $message = "Notice added successfully.";
                    $messageType = "success";
                } catch (PDOException $e) {
                    error_log("Notice creation error: " . $e->getMessage());
                    $message = "Could not save the notice. Check the portal_notices table.";
                    $messageType = "error";
                }
            }
        }
    }

    // Issue a fresh token after each form submission.
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

try {
    $stmt = $conn->query(
        "SELECT notice_id, title, message, expires_at, status, created_at
         FROM portal_notices
         ORDER BY created_at DESC"
    );
    $notices = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Notice list error: " . $e->getMessage());
    $notices = [];
    $message = "Could not load notices.";
    $messageType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices - Student Portal Hub</title>

    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f3f6fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 900px;
            margin: 0 auto;
        }

        .panel,
        .notice-card {
            margin-bottom: 20px;
            padding: 22px;
            background: #fff;
            border: 1px solid #dce3ed;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-weight: bold;
        }

        input,
        textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            border: 1px solid #b9c4d3;
            border-radius: 7px;
            font: inherit;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            margin-top: 16px;
            padding: 10px 16px;
            border: 0;
            border-radius: 7px;
            background: #2459d3;
            color: #fff;
            font: inherit;
            cursor: pointer;
        }

        .deactivate-button {
            margin-top: 8px;
            background: #b42318;
        }

        a {
            color: #2459d3;
        }

        .message {
            margin: 14px 0;
            padding: 12px;
            border-radius: 7px;
        }

        .success {
            background: #e7f7ed;
            color: #176534;
        }

        .error {
            background: #fdeaea;
            color: #9d2020;
        }

        .notice-meta {
            color: #52627a;
            font-size: 0.95rem;
        }

        .inactive {
            opacity: 0.7;
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
        <h1>Manage Notices</h1>
        <p><a href="admin_dashboard.php">← Back to Admin Dashboard</a></p>

        <?php if ($message !== ""): ?>
            <p class="message <?= escapeHtml($messageType) ?>" role="status">
                <?= escapeHtml($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="create">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= escapeHtml($_SESSION["csrf_token"]) ?>">

            <label for="title">Notice title *</label>
            <input
                type="text"
                id="title"
                name="title"
                maxlength="150"
                required>

            <label for="message">Notice message *</label>
            <textarea
                id="message"
                name="message"
                maxlength="5000"
                required></textarea>

            <label for="expires_at">Expiry date (optional)</label>
            <input
                type="date"
                id="expires_at"
                name="expires_at">

            <button type="submit">Add Notice</button>
        </form>
    </section>

    <section class="panel">
        <h2>Saved Notices</h2>

        <?php if (count($notices) === 0): ?>
            <p>No notices have been added yet.</p>
        <?php else: ?>
            <?php foreach ($notices as $notice): ?>
                <article class="notice-card <?= $notice["status"] === "Inactive" ? "inactive" : "" ?>">
                    <h3><?= escapeHtml($notice["title"]) ?></h3>
                    <p><?= nl2br(escapeHtml($notice["message"])) ?></p>

                    <p class="notice-meta">
                        Status: <?= escapeHtml($notice["status"]) ?>
                        · Added: <?= escapeHtml($notice["created_at"]) ?>
                        <?php if (!empty($notice["expires_at"])): ?>
                            · Expires: <?= escapeHtml($notice["expires_at"]) ?>
                        <?php endif; ?>
                    </p>

                    <?php if ($notice["status"] === "Active"): ?>
                        <form
                            method="POST"
                            onsubmit="return confirm('Deactivate this notice? It will disappear from the public pages.');">

                            <input type="hidden" name="action" value="deactivate">
                            <input
                                type="hidden"
                                name="notice_id"
                                value="<?= (int) $notice["notice_id"] ?>">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapeHtml($_SESSION["csrf_token"]) ?>">

                            <button class="deactivate-button" type="submit">
                                Deactivate Notice
                            </button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>