<?php
require_once __DIR__ . "/db.php";

$stmt = $conn->prepare(
    "SELECT student_id, name, email, course
     FROM students
     WHERE status = :status
     ORDER BY student_id DESC"
);
$stmt->execute(["status" => "Active"]);
$students = $stmt->fetchAll();

$filename = "students_" . date("Y-m-d_H-i-s") . ".csv";

header("Content-Type: text/csv; charset=UTF-8");
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen("php://output", "w");

fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ["Student ID", "Name", "Email", "Course"]);

foreach ($students as $student) {
    fputcsv($output, [
        $student["student_id"],
        $student["name"],
        $student["email"],
        $student["course"]
    ]);
}

fclose($output);
exit;