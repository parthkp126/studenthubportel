<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php?error=login_required");
    exit;
}

$timeoutDuration = 15 * 60;
$lastActivity = $_SESSION["last_activity"] ?? 0;

if (time() - $lastActivity > $timeoutDuration) {
    $_SESSION = [];
    session_destroy();

    header("Location: timeout.php");
    exit;
}

$_SESSION["last_activity"] = time();

if (($_SESSION["role"] ?? "") === "Admin") {
    header("Location: admin_dashboard.php");
    exit;
}

if (($_SESSION["role"] ?? "") === "Student") {
    header("Location: student_dashboard.php");
    exit;
}

$_SESSION = [];
session_destroy();

header("Location: login.php?error=invalid");
exit;