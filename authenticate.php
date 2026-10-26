<?php
session_start();
require_once __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
    header("Location: login.php?error=invalid");
    exit;
}

try {
    $stmt = $mysqli->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Database query failed. Check the users table and its columns.");
}

if (!$user || !password_verify($password, $user["password"])) {
    header("Location: login.php?error=invalid");
    exit;
}

// Regenerate the session ID after successful login.
session_regenerate_id(true);

$_SESSION["user_id"] = (int) $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();

header("Location: dashboard.php");
exit;