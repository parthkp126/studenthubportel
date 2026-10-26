
<?php
require_once "db.php";

echo "Database connection successful!";

$stmt = $conn->query("SELECT COUNT(*) FROM users");
echo "<br>Total registered users: " . $stmt->fetchColumn();
?>
