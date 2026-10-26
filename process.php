
<?php

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Please submit the registration form."
    ]);
    exit;
}

/* -------------------------------------------------
   Helper function
------------------------------------------------- */
function cleanText($value)
{
    return trim((string)$value);
}

/* -------------------------------------------------
   Get form data
------------------------------------------------- */
$name = cleanText($_POST["name"] ?? "");
$enrollment = cleanText($_POST["enrollment"] ?? "");
$email = cleanText($_POST["email"] ?? "");
$mobile = cleanText($_POST["mobile"] ?? "");
$password = (string)($_POST["password"] ?? "");
$confirmPassword = (string)(
    $_POST["confirmPassword"]
    ?? $_POST["confirm_password"]
    ?? ""
);
$course = cleanText($_POST["course"] ?? "");
$year = cleanText($_POST["year"] ?? "");
$gender = cleanText($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

/* -------------------------------------------------
   Validation
------------------------------------------------- */
$errors = [];

/* Name */
if ($name === "") {
    $errors["name"] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]{3,50}$/", $name)) {
    $errors["name"] = "Name must contain only letters and spaces.";
}

/* Enrollment */
if ($enrollment === "") {
    $errors["enrollment"] = "Enrollment number is required.";
} elseif (!preg_match("/^[A-Za-z0-9]{5,15}$/", $enrollment)) {
    $errors["enrollment"] = "Enter a valid enrollment number.";
}

/* Email */
if ($email === "") {
    $errors["email"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address.";
}

/* Mobile */
if ($mobile === "") {
    $errors["mobile"] = "Mobile number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors["mobile"] = "Mobile number must contain exactly 10 digits.";
}

/* Password */
if ($password === "") {
    $errors["password"] = "Password is required.";
} elseif (
    !preg_match(
        "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,30}$/",
        $password
    )
) {
    $errors["password"] =
        "Password must contain uppercase, lowercase, number, special character and be 8-30 characters.";
}

/* Confirm password */
if ($confirmPassword === "") {
    $errors["confirmPassword"] = "Please confirm your password.";
} elseif ($password !== $confirmPassword) {
    $errors["confirmPassword"] = "Passwords do not match.";
}

/* Course */
if ($course === "") {
    $errors["course"] = "Please select a course.";
}

/* Year */
if ($year === "") {
    $errors["year"] = "Please select a year.";
}

/* Gender */
if ($gender === "") {
    $errors["gender"] = "Please select your gender.";
}

/* Terms */
if (!$terms) {
    $errors["terms"] = "You must accept the terms and conditions.";
}

/* -------------------------------------------------
   Return validation errors
------------------------------------------------- */
if (!empty($errors)) {
    echo json_encode([
        "success" => false,
        "message" => "Please correct the errors in the form.",
        "errors" => $errors
    ]);
    exit;
}

/* -------------------------------------------------
   File path
------------------------------------------------- */
$csvFile = __DIR__ . DIRECTORY_SEPARATOR . "students.csv";

/*
   Final CSV structure:

   Name
   Enrollment
   Email
   Mobile
   Course
   Year
   Gender
   Password Hash
   Registered At
*/

$header = [
    "Name",
    "Enrollment",
    "Email",
    "Mobile",
    "Course",
    "Year",
    "Gender",
    "Password Hash",
    "Registered At"
];

/* -------------------------------------------------
   Password hashing
------------------------------------------------- */
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

if ($passwordHash === false) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to securely process the password."
    ]);
    exit;
}

/* -------------------------------------------------
   Open CSV file
------------------------------------------------- */
$file = fopen($csvFile, "c+");

if ($file === false) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to open students.csv for writing."
    ]);
    exit;
}

/* Lock file */
if (!flock($file, LOCK_EX)) {
    fclose($file);

    echo json_encode([
        "success" => false,
        "message" => "Unable to lock students.csv."
    ]);
    exit;
}

/* -------------------------------------------------
   Check existing CSV
------------------------------------------------- */
$existingHeader = [];
$existingRows = [];

rewind($file);

while (($row = fgetcsv($file, 0, ",", '"', "")) !== false) {
    if (count($row) === 0) {
        continue;
    }

    if (empty($existingHeader)) {
        $existingHeader = $row;
    } else {
        $existingRows[] = $row;
    }
}

/* -------------------------------------------------
   If old CSV structure exists, migrate it
------------------------------------------------- */
$headerMatches = ($existingHeader === $header);

if (!$headerMatches && !empty($existingHeader)) {

    /*
       Old possible structure:

       Name,
       Enrollment,
       Email,
       Mobile,
       Course,
       Semester,
       Registered At
    */

    $migratedRows = [];

    foreach ($existingRows as $oldRow) {

        $oldName = $oldRow[0] ?? "";
        $oldEnrollment = $oldRow[1] ?? "";
        $oldEmail = $oldRow[2] ?? "";
        $oldMobile = $oldRow[3] ?? "";
        $oldCourse = $oldRow[4] ?? "";
        $oldSemester = $oldRow[5] ?? "";
        $oldRegisteredAt = $oldRow[6] ?? "";

        $migratedRows[] = [
            $oldName,
            $oldEnrollment,
            $oldEmail,
            $oldMobile,
            $oldCourse,
            $oldSemester,
            "",
            "",
            $oldRegisteredAt
        ];
    }

    /*
       Rewrite file using final structure.
    */
    ftruncate($file, 0);
    rewind($file);

    fputcsv($file, $header, ",", '"', "");

    foreach ($migratedRows as $row) {
        fputcsv($file, $row, ",", '"', "");
    }

} elseif (empty($existingHeader)) {

    /*
       Empty/new CSV
    */
    ftruncate($file, 0);
    rewind($file);

    fputcsv($file, $header, ",", '"', "");
}

/* -------------------------------------------------
   Prevent duplicate enrollment
------------------------------------------------- */
$duplicateEnrollment = false;

/*
   Re-read final CSV to check enrollment.
*/
rewind($file);

$firstRow = true;

while (($row = fgetcsv($file, 0, ",", '"', "")) !== false) {

    if ($firstRow) {
        $firstRow = false;
        continue;
    }

    $savedEnrollment = trim($row[1] ?? "");

    if (
        $savedEnrollment !== "" &&
        strcasecmp($savedEnrollment, $enrollment) === 0
    ) {
        $duplicateEnrollment = true;
        break;
    }
}

if ($duplicateEnrollment) {

    flock($file, LOCK_UN);
    fclose($file);

    echo json_encode([
        "success" => false,
        "message" => "This enrollment number is already registered.",
        "errors" => [
            "enrollment" => "Enrollment number already exists."
        ]
    ]);

    exit;
}

/* -------------------------------------------------
   Add new student
------------------------------------------------- */
$newRow = [
    $name,
    $enrollment,
    $email,
    $mobile,
    $course,
    $year,
    $gender,
    $passwordHash,
    date("Y-m-d H:i:s")
];

/*
   Move to end of file.
*/
fseek($file, 0, SEEK_END);

$result = fputcsv(
    $file,
    $newRow,
    ",",
    '"',
    ""
);

if ($result === false) {

    flock($file, LOCK_UN);
    fclose($file);

    echo json_encode([
        "success" => false,
        "message" => "Unable to save student data."
    ]);

    exit;
}

/* -------------------------------------------------
   Close file
------------------------------------------------- */
fflush($file);
flock($file, LOCK_UN);
fclose($file);

/* -------------------------------------------------
   Success response
------------------------------------------------- */
echo json_encode([
    "success" => true,
    "message" => "Registration successful!",
    "student" => [
        "name" => $name,
        "enrollment" => $enrollment,
        "email" => $email,
        "mobile" => $mobile,
        "course" => $course,
        "year" => $year,
        "gender" => $gender
    ]
]);

exit;
?>

