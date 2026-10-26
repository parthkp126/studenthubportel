<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php?error=login_required");
    exit;
}

if (($_SESSION["role"] ?? "") !== "Admin") {
    http_response_code(403);
    exit("403 Forbidden: Admin access required.");
}

require_once __DIR__ . "/db.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function getCount(PDO $conn, string $sql): int
{
    try {
        return (int) $conn->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        error_log("Dashboard count error: " . $e->getMessage());
        return 0;
    }
}

$userCount = getCount($conn, "SELECT COUNT(*) FROM users");
$studentCount = getCount(
    $conn,
    "SELECT COUNT(*) FROM students WHERE status = 'Active'"
);
$eventCount = getCount($conn, "SELECT COUNT(*) FROM events");

$registrations = [];
$registrationError = false;

try {
    $registrationStmt = $conn->query(
        "SELECT
            r.registration_id,
            r.student_id,
            r.event_id,
            s.name AS student_name,
            s.email AS student_email,
            s.course AS student_course,
            e.event_name
         FROM registrations AS r
         LEFT JOIN students AS s ON s.student_id = r.student_id
         LEFT JOIN events AS e ON e.event_id = r.event_id
         ORDER BY r.registration_id DESC
         LIMIT 5"
    );
    $registrations = $registrationStmt->fetchAll();
} catch (Throwable $e) {
    error_log("Latest registrations query error: " . $e->getMessage());
    $registrationError = true;
}

$auditLogs = [];
$auditError = false;

try {
    $auditStmt = $conn->query(
        "SELECT audit_logs.*, users.name AS admin_name
         FROM audit_logs
         LEFT JOIN users ON users.id = audit_logs.user_id
         ORDER BY audit_logs.created_at DESC
         LIMIT 8"
    );
    $auditLogs = $auditStmt->fetchAll();
} catch (Throwable $e) {
    error_log("Audit log query error: " . $e->getMessage());
    $auditError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Portal Hub</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">

    <style>
        body {
            min-height: 100vh;
            background: #f3f6fb;
            color: #172033;
        }

        .dashboard-container {
            max-width: 1200px;
        }

        .stat-number {
            color: #2459d3;
            font-size: 2rem;
            font-weight: 700;
        }

        .card {
            border: 1px solid #e1e7ef;
        }

        .table thead th {
            background: #eff6ff;
            color: #1d4ed8;
            white-space: nowrap;
        }
    </style>
</head>

<body>
<main class="container dashboard-container py-4">
    <header class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-2">Admin Dashboard</h1>
                <p class="mb-0">
                    Welcome, <?= escapeHtml($_SESSION["name"] ?? "Admin") ?>
                </p>
            </div>

            <form action="logout.php" method="POST">
                <button class="btn btn-danger" type="submit">Logout</button>
            </form>
        </div>
    </header>

    <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Admin navigation">
    <a class="btn btn-primary" href="admin_events.php">Manage Events</a>
    <a class="btn btn-primary" href="student.php">Student Records</a>
    <a class="btn btn-primary" href="admin_contact_messages.php">Contact Messages</a>
    <a class="btn btn-primary" href="admin_notices.php">Manage Notices</a>
    <a class="btn btn-outline-primary" href="index.html">Main Website</a>
</nav>

    <section class="row g-3 mb-4" aria-label="Dashboard statistics">
        <div class="col-12 col-md-4">
            <article class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-number"><?= $userCount ?></div>
                    <div>Total Users</div>
                </div>
            </article>
        </div>

        <div class="col-12 col-md-4">
            <article class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-number"><?= $studentCount ?></div>
                    <div>Active Students</div>
                </div>
            </article>
        </div>

        <div class="col-12 col-md-4">
            <article class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-number"><?= $eventCount ?></div>
                    <div>Total Events</div>
                </div>
            </article>
        </div>
    </section>

    <section class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Latest Registrations</h2>

            <?php if ($registrationError): ?>
                <div class="alert alert-danger mb-0" role="alert">
                    Could not load registrations. Check the registrations, students, and events table columns.
                </div>
            <?php elseif (count($registrations) === 0): ?>
                <p class="text-secondary mb-0">
                    No registrations found. Register a student for an event to display a row here.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Registration ID</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Event</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $registration): ?>
                                <tr>
                                    <td>
                                        <?= escapeHtml($registration["registration_id"] ?? "—") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($registration["student_name"] ?? "Student not found") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($registration["student_email"] ?? "—") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($registration["student_course"] ?? "—") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($registration["event_name"] ?? "Event not found") ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4 mb-3">Recent Admin Actions</h2>

            <?php if ($auditError): ?>
                <div class="alert alert-danger mb-0" role="alert">
                    Could not load audit logs. Check that the audit_logs table exists.
                </div>
            <?php elseif (count($auditLogs) === 0): ?>
                <p class="text-secondary mb-0">
                    No admin actions have been recorded yet.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Date and Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $log): ?>
                                <tr>
                                    <td>
                                        <?= escapeHtml($log["admin_name"] ?? "Unknown") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($log["action"] ?? "—") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($log["details"] ?? "—") ?>
                                    </td>
                                    <td>
                                        <?= escapeHtml($log["created_at"] ?? "—") ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>