<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$message = trim($_POST["message"] ?? "");

$errors = [];

$name = htmlspecialchars($name);
$email = htmlspecialchars($email);
$mobile = htmlspecialchars($mobile);
$course = htmlspecialchars($course);
$year = htmlspecialchars($year);
$message = htmlspecialchars($message);

if ($name === "") {
    $errors[] = "Name is required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}

if ($course === "") {
    $errors[] = "Please select a course.";
}

if ($year === "") {
    $errors[] = "Please select your academic year.";
}

if ($message === "") {
    $errors[] = "Message is required.";
}

if (!empty($errors)) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Validation Error</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="result error">

    <h2>Validation Error</h2>

    <p>Please correct the following problems:</p>

    <?php foreach ($errors as $error) { ?>

        <p>
            <?php echo $error; ?>
        </p>

    <?php } ?>

    <a href="index.php">
        Go Back to Registration
    </a>

</div>

</body>
</html>

<?php
    exit;
}

$file = "students.csv";

$isNewFile = !file_exists($file);

$handle = fopen($file, "a");

if ($isNewFile) {

    fputcsv(
        $handle,
        [
            "Name",
            "Email",
            "Mobile",
            "Course",
            "Year",
            "Message"
        ]
    );
}

fputcsv(
    $handle,
    [
        $name,
        $email,
        $mobile,
        $course,
        $year,
        $message
    ]
);

fclose($handle);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registration Successful</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="result success">

    <h2>Registration Successful!</h2>

    <p>
        Your student registration has been
        submitted successfully.
    </p>

    <p>
        Your information has been saved
        in the student records file.
    </p>

    <a href="index.php">
        Register Another Student
    </a>

</div>

</body>

</html>