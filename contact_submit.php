<?php
session_start();

require_once __DIR__ . "/db.php";

function redirectWithMessage($status)
{
    header("Location: contact.html?status=" . urlencode($status) . "#contactForm");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || $message === "") {
    redirectWithMessage("required");
}

if (mb_strlen($name) > 100 || mb_strlen($email) > 150) {
    redirectWithMessage("too_long");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage("invalid_email");
}

if (mb_strlen($message) > 5000) {
    redirectWithMessage("message_too_long");
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name, email, message)
         VALUES (:name, :email, :message)"
    );

    $stmt->execute([
        "name" => $name,
        "email" => $email,
        "message" => $message
    ]);

    redirectWithMessage("sent");
} catch (PDOException $e) {
    error_log($e->getMessage());
    redirectWithMessage("error");
}