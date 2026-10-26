<?php
$host = "localhost";
$dbname = "studentportelhub";
$dbuser = "root";
$dbpass = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli($host, $dbuser, $dbpass, $dbname);
    $mysqli->set_charset("utf8mb4");

} catch (Throwable $e) {
    error_log($e->getMessage());

    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed. Check db.php settings.",
        "errors" => (object)[]
    ]);
    exit;
}
?>