<?php
require_once __DIR__ . "/db.php";

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");

$sql = "SELECT student_id, name, email, course
        FROM students
        WHERE status = :status";

$params = ["status" => "Active"];

if ($search !== "") {
    $sql .= " AND (
        name LIKE :name_search
        OR email LIKE :email_search
        OR course LIKE :course_search
    )";

    $searchTerm = "%" . $search . "%";
    $params["name_search"] = $searchTerm;
    $params["email_search"] = $searchTerm;
    $params["course_search"] = $searchTerm;
}

if ($courseFilter !== "") {
    $sql .= " AND course = :course";
    $params["course"] = $courseFilter;
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$courseStmt = $conn->query(
    "SELECT DISTINCT course
     FROM students
     WHERE status = 'Active'
       AND course IS NOT NULL
       AND course <> ''
     ORDER BY course"
);
$courses = $courseStmt->fetchAll(PDO::FETCH_COLUMN);

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
    <title>Student Records</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .student-records {
            width: min(1100px, calc(100% - 32px));
            margin: 40px auto;
            padding: 24px;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .student-records table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-records th,
        .student-records td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .student-records th {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .student-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 16px 0 20px;
        }

        .student-filters input,
        .student-filters select,
        .student-filters button,
        .student-filters a,
        .student-actions a {
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font: inherit;
        }

        .student-filters button,
        .student-filters a,
        .student-actions a,
        .student-top-actions a {
            display: inline-block;
            cursor: pointer;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
        }

        .student-top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .student-top-actions a {
            padding: 10px 12px;
            border-radius: 6px;
        }

        .student-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .student-actions .delete-link {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <main class="student-records">
        <h1>Student Records</h1>

        <div class="student-top-actions">
            <a href="add_student.php">Add Student</a>
            <a href="export_students.php">Export Active Students CSV</a>
        </div>

        <form method="GET" action="student.php" class="student-filters">
            <input
                type="search"
                name="search"
                placeholder="Search name, email, or course"
                value="<?= escapeHtml($search) ?>"
            >

            <select name="course">
                <option value="">All courses</option>

                <?php foreach ($courses as $course): ?>
                    <option
                        value="<?= escapeHtml($course) ?>"
                        <?= $courseFilter === $course ? "selected" : "" ?>
                    >
                        <?= escapeHtml($course) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Search / Filter</button>
            <a href="student.php">Clear</a>
        </form>

        <?php if (isset($_GET["deleted"]) && $_GET["deleted"] === "1"): ?>
            <p>Student status changed to Inactive.</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= escapeHtml($student["student_id"]) ?></td>
                            <td><?= escapeHtml($student["name"]) ?></td>
                            <td><?= escapeHtml($student["email"]) ?></td>
                            <td><?= escapeHtml($student["course"]) ?></td>
                            <td class="student-actions">
                                <a href="edit_student.php?id=<?= urlencode((string) $student["student_id"]) ?>">
                                    Edit
                                </a>

                                <a
                                    class="delete-link"
                                    href="delete_student.php?id=<?= urlencode((string) $student["student_id"]) ?>"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No active student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>