<?php
session_start();

$message = "";
$messageClass = "";

if (($_GET["error"] ?? "") === "invalid") {
    $message = "Invalid email or password.";
    $messageClass = "error";
} elseif (($_GET["message"] ?? "") === "logged_out") {
    $message = "You have been logged out successfully.";
    $messageClass = "success";
} elseif (($_GET["error"] ?? "") === "timeout") {
    $message = "Your session has expired. Please log in again.";
    $messageClass = "error";
} elseif (($_GET["error"] ?? "") === "login_required") {
    $message = "Please log in to continue.";
    $messageClass = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Hub - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-container">
        <h1>Student Portal Hub Login</h1>

        <?php if ($message !== ""): ?>
            <p class="<?= htmlspecialchars($messageClass, ENT_QUOTES, "UTF-8") ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Login</button>
        </form>
    </main>
</body>
</html>