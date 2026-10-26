<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . "/db.php";

header("Content-Type: application/json; charset=UTF-8");

function sendJson(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function readJsonInput(): array
{
    $rawInput = file_get_contents("php://input");

    if ($rawInput === false || trim($rawInput) === "") {
        return [];
    }

    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        sendJson(400, [
            "success" => false,
            "message" => "Invalid JSON request.",
            "errors" => (object) []
        ]);
    }

    return $data;
}

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

try {
    if ($method === "GET") {
        $search = trim($_GET["search"] ?? "");
        $course = trim($_GET["course"] ?? "");

        $sql = "SELECT id, name, email, course, status, created_at
                FROM api_students
                WHERE status = 'Active'";

        $params = [];

        if ($search !== "") {
            $sql .= " AND (name LIKE :name OR email LIKE :email)";
            $searchTerm = "%" . $search . "%";
            $params["name"] = $searchTerm;
            $params["email"] = $searchTerm;
        }

        if ($course !== "") {
            $sql .= " AND course = :course";
            $params["course"] = $course;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        sendJson(200, [
            "success" => true,
            "message" => "Student records loaded.",
            "data" => $stmt->fetchAll(),
            "errors" => (object) []
        ]);
    }

    if ($method === "POST") {
        $data = readJsonInput();

        $name = trim((string) ($data["name"] ?? ""));
        $email = trim((string) ($data["email"] ?? ""));
        $course = trim((string) ($data["course"] ?? ""));

        $errors = [];

        if ($name === "") {
            $errors["name"] = "Name is required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Enter a valid email address.";
        }

        if ($course === "") {
            $errors["course"] = "Course is required.";
        }

        if ($errors !== []) {
            sendJson(400, [
                "success" => false,
                "message" => "Please correct the form fields.",
                "errors" => $errors
            ]);
        }

        $stmt = $conn->prepare(
            "INSERT INTO api_students (name, email, course)
             VALUES (:name, :email, :course)"
        );

        $stmt->execute([
            "name" => $name,
            "email" => $email,
            "course" => $course
        ]);

        sendJson(201, [
            "success" => true,
            "message" => "Student added successfully.",
            "data" => [
                "id" => (int) $conn->lastInsertId()
            ],
            "errors" => (object) []
        ]);
    }

    if ($method === "PUT") {
        $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
        $data = readJsonInput();

        if (!$id || $id < 1) {
            sendJson(400, [
                "success" => false,
                "message" => "A valid student ID is required.",
                "errors" => (object) []
            ]);
        }

        $name = trim((string) ($data["name"] ?? ""));
        $email = trim((string) ($data["email"] ?? ""));
        $course = trim((string) ($data["course"] ?? ""));

        $errors = [];

        if ($name === "") {
            $errors["name"] = "Name is required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Enter a valid email address.";
        }

        if ($course === "") {
            $errors["course"] = "Course is required.";
        }

        if ($errors !== []) {
            sendJson(400, [
                "success" => false,
                "message" => "Please correct the form fields.",
                "errors" => $errors
            ]);
        }

        $stmt = $conn->prepare(
            "UPDATE api_students
             SET name = :name, email = :email, course = :course
             WHERE id = :id AND status = 'Active'"
        );

        $stmt->execute([
            "name" => $name,
            "email" => $email,
            "course" => $course,
            "id" => $id
        ]);

        if ($stmt->rowCount() === 0) {
            $check = $conn->prepare(
                "SELECT id FROM api_students
                 WHERE id = :id AND status = 'Active'"
            );
            $check->execute(["id" => $id]);

            if (!$check->fetch()) {
                sendJson(404, [
                    "success" => false,
                    "message" => "Active student record not found.",
                    "errors" => (object) []
                ]);
            }
        }

        sendJson(200, [
            "success" => true,
            "message" => "Student updated successfully.",
            "data" => ["id" => $id],
            "errors" => (object) []
        ]);
    }

    if ($method === "DELETE") {
        $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            sendJson(400, [
                "success" => false,
                "message" => "A valid student ID is required.",
                "errors" => (object) []
            ]);
        }

        $stmt = $conn->prepare(
            "UPDATE api_students
             SET status = 'Inactive'
             WHERE id = :id AND status = 'Active'"
        );

        $stmt->execute(["id" => $id]);

        if ($stmt->rowCount() === 0) {
            sendJson(404, [
                "success" => false,
                "message" => "Active student record not found.",
                "errors" => (object) []
            ]);
        }

        sendJson(200, [
            "success" => true,
            "message" => "Student marked inactive successfully.",
            "data" => ["id" => $id],
            "errors" => (object) []
        ]);
    }

    header("Allow: GET, POST, PUT, DELETE");

    sendJson(405, [
        "success" => false,
        "message" => "This request method is not supported.",
        "errors" => (object) []
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());

    if ($e->getCode() === "23000") {
        sendJson(409, [
            "success" => false,
            "message" => "A student with this email already exists.",
            "errors" => [
                "email" => "This email is already registered."
            ]
        ]);
    }

    sendJson(500, [
        "success" => false,
        "message" => "A database error occurred.",
        "errors" => (object) []
    ]);

} catch (Throwable $e) {
    error_log($e->getMessage());

    sendJson(500, [
        "success" => false,
        "message" => "An unexpected server error occurred.",
        "errors" => (object) []
    ]);
}