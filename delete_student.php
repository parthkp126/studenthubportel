<?php
require_once __DIR__ . "/db.php";

$studentId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$studentId) {
    http_response_code(400);
    exit("Invalid student ID.");
}

$stmt = $conn->prepare(
    "SELECT student_id, name
     FROM students
     WHERE student_id = :student_id AND status = 'Active'"
);
$stmt->execute(["student_id" => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    exit("Active student not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (($_POST["confirm"] ?? "") === "yes") {
        $deleteStmt = $conn->prepare(
            "UPDATE students
             SET status = 'Inactive'
             WHERE student_id = :student_id AND status = 'Active'"
        );
        $deleteStmt->execute(["student_id" => $studentId]);

        header("Location: student.php?deleted=1");
        exit;
    }

    header("Location: student.php");
    exit;
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete</title>
</head>
<body>
    <h1>Confirm Delete</h1>

    <p>શું તમે આ student ને inactive કરવા માંગો છો?</p>
    <p><strong><?= escapeHtml($student["name"]) ?></strong></p>

    <form method="POST">
        <button type="submit" name="confirm" value="yes">Yes, delete</button>
        <button type="submit" name="confirm" value="no">Cancel</button>
    </form>
</body>
</html>