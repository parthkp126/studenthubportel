
<?php

ob_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

function respond($success, $message, $errors = [])
{
    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "errors" => (object) $errors
    ]);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(false, "Please submit the registration form.");
}

function cleanText($value)
{
    return trim((string) $value);
}

$name = cleanText($_POST["name"] ?? "");
$enrollment = cleanText($_POST["enrollment"] ?? "");
$email = cleanText($_POST["email"] ?? "");
$mobile = cleanText($_POST["mobile"] ?? "");
$password = (string) ($_POST["password"] ?? "");
$confirmPassword = (string) ($_POST["confirmPassword"] ?? "");
$course = cleanText($_POST["course"] ?? "");
$year = cleanText($_POST["year"] ?? "");
$gender = cleanText($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

$errors = [];

if (!preg_match("/^[a-zA-Z ]{3,50}$/", $name)) {
    $errors["name"] = "Enter a valid name (3–50 letters).";
}

if (!preg_match("/^[a-zA-Z0-9]{5,15}$/", $enrollment)) {
    $errors["enrollment"] = "Enter a valid enrollment number (5–15 letters or digits).";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address.";
}

if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors["mobile"] = "Mobile number must contain exactly 10 digits.";
}

if (
    strlen($password) < 8 ||
    strlen($password) > 30 ||
    !preg_match("/[a-z]/", $password) ||
    !preg_match("/[A-Z]/", $password) ||
    !preg_match("/[0-9]/", $password) ||
    !preg_match("/[^a-zA-Z0-9]/", $password)
) {
    $errors["password"] =
        "Password must contain uppercase, lowercase, number and special character (8–30 characters).";
}

if ($password !== $confirmPassword) {
    $errors["confirmPassword"] = "Passwords do not match.";
}

$allowedCourses = [
    "Computer Science",
    "Information Technology",
    "Computer Engineering"
];

if (!in_array($course, $allowedCourses, true)) {
    $errors["course"] = "Please select a valid course.";
}

$allowedYears = [
    "First Year",
    "Second Year",
    "Third Year",
    "Fourth Year"
];

if (!in_array($year, $allowedYears, true)) {
    $errors["year"] = "Please select a valid year.";
}

$allowedGenders = [
    "Male",
    "Female",
    "Other"
];

if (!in_array($gender, $allowedGenders, true)) {
    $errors["gender"] = "Please select a gender.";
}

if (!$terms) {
    $errors["terms"] = "Please accept the terms and conditions.";
}

if (!empty($errors)) {
    respond(false, "Please correct the errors in the form.", $errors);
}

try {

    $database = $conn->query("SELECT DATABASE()")->fetchColumn();

    if ($database !== "studentportelhub") {
        error_log("Incorrect database selected: " . $database);

        respond(
            false,
            "Connected to the wrong database. Please check db.php."
        );
    }

    $check = $conn->prepare(
        "SELECT id, email, enrollment
         FROM users
         WHERE email = :email OR enrollment = :enrollment"
    );

    $check->execute([
        "email" => $email,
        "enrollment" => $enrollment
    ]);

    $existingUsers = $check->fetchAll();

    foreach ($existingUsers as $existing) {

        if (strcasecmp($existing["email"], $email) === 0) {
            respond(
                false,
                "This email is already registered.",
                ["email" => "Email address already exists."]
            );
        }

        if (strcasecmp($existing["enrollment"], $enrollment) === 0) {
            respond(
                false,
                "This enrollment number is already registered.",
                ["enrollment" => "Enrollment number already exists."]
            );
        }
    }

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    if ($hashedPassword === false) {
        respond(false, "Password hashing failed.");
    }

    $insert = $conn->prepare(
        "INSERT INTO users
        (name, enrollment, email, mobile, password, course, year, gender)
        VALUES
        (:name, :enrollment, :email, :mobile, :password, :course, :year, :gender)"
    );

    $insert->execute([
        "name" => $name,
        "enrollment" => $enrollment,
        "email" => $email,
        "mobile" => $mobile,
        "password" => $hashedPassword,
        "course" => $course,
        "year" => $year,
        "gender" => $gender
    ]);

    $newId = $conn->lastInsertId();

    $verify = $conn->prepare(
        "SELECT id FROM users WHERE id = :id"
    );

    $verify->execute([
        "id" => $newId
    ]);

    if (!$verify->fetch()) {
        respond(
            false,
            "The registration could not be verified in the database."
        );
    }

    respond(
        true,
        "Registration successful! Your details have been saved. ID: " . $newId
    );

} catch (PDOException $e) {

    error_log("Registration database error: " . $e->getMessage());

    if ($e->getCode() === "23000") {
        respond(
            false,
            "This email or enrollment number is already registered."
        );
    }

    respond(
        false,
        "Database error. Please check db.php and the users table."
    );
}

