<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php?error=login_required");
    exit;
}

if (($_SESSION["role"] ?? "") !== "Student") {
    http_response_code(403);
    exit("Unauthorized Access");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <h1>Student Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION["name"] ?? "Student") ?></p>
        <form action="logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>