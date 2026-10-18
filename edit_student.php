<?php
require_once __DIR__ . "/db.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$studentId = filter_var(
    $_POST["student_id"] ?? $_GET["id"] ?? "",
    FILTER_VALIDATE_INT
);

$message = "";
$messageType = "";
$student = null;

if (!$studentId) {
    http_response_code(400);
    exit("Invalid student ID.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || $course === "") {
        $message = "Please enter a name, valid email, and course.";
        $messageType = "error";
    } else {
        try {
            $update = $conn->prepare(
                "UPDATE students
                 SET name = :name, email = :email, course = :course
                 WHERE student_id = :id AND status = 'Active'"
            );

            $update->execute([
                "name" => $name,
                "email" => $email,
                "course" => $course,
                "id" => $studentId
            ]);

            $message = "Student details updated successfully.";
            $messageType = "success";
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Could not update the student. Please try again.";
            $messageType = "error";
        }
    }
}

$stmt = $conn->prepare(
    "SELECT student_id, name, email, course
     FROM students
     WHERE student_id = :id AND status = 'Active'"
);
$stmt->execute(["id" => $studentId]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    exit("Active student record not found.");
}

$courses = [
    "Computer Science",
    "Information Technology",
    "Computer Engineering"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .student-form {
            width: min(600px, calc(100% - 32px));
            margin: 40px auto;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .student-form label {
            display: block;
            margin: 14px 0 6px;
            font-weight: bold;
        }

        .student-form input,
        .student-form select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd2e1;
            border-radius: 6px;
        }

        .student-form button {
            margin-top: 18px;
            padding: 11px 18px;
            border: 0;
            border-radius: 6px;
            background: #315bd6;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .form-message {
            padding: 10px;
            border-radius: 6px;
        }

        .form-message.success {
            background: #e8f7ee;
            color: #067647;
        }

        .form-message.error {
            background: #fff0f0;
            color: #b42318;
        }
    </style>
</head>
<body>
    <main class="student-form">
        <h1>Edit Student</h1>

        <?php if ($message !== ""): ?>
            <p class="form-message <?= escapeHtml($messageType) ?>">
                <?= escapeHtml($message) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="edit_student.php?id=<?= (int) $studentId ?>">
            <input type="hidden" name="student_id" value="<?= (int) $studentId ?>">

            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= escapeHtml($student["name"]) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= escapeHtml($student["email"]) ?>"
                required
            >

            <label for="course">Course</label>
            <select id="course" name="course" required>
                <?php foreach ($courses as $courseOption): ?>
                    <option
                        value="<?= escapeHtml($courseOption) ?>"
                        <?= $student["course"] === $courseOption ? "selected" : "" ?>
                    >
                        <?= escapeHtml($courseOption) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Save Changes</button>
        </form>

        <p><a href="student.php">Back to student records</a></p>
    </main>
</body>
</html>