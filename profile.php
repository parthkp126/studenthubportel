<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php?error=login_required");
    exit;
}

require_once __DIR__ . "/db.php";

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

try {
    $stmt = $conn->prepare(
        "SELECT name, enrollment, email, mobile, course, year, gender
         FROM users
         WHERE id = :user_id
         LIMIT 1"
    );

    $stmt->execute([
        "user_id" => $_SESSION["user_id"]
    ]);

    $student = $stmt->fetch();

    if (!$student) {
        http_response_code(404);
        exit("Student profile not found.");
    }
} catch (PDOException $e) {
    error_log("Profile query error: " . $e->getMessage());
    http_response_code(500);
    exit("Could not load the student profile.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - Student Portal Hub</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        body.dark {
            background: #0f172a;
            color: #e5e7eb;
        }

        .profile-section {
            width: 90%;
            max-width: 750px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .profile-section h2 {
            margin-bottom: 30px;
            color: #123c69;
            font-size: 32px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            display: grid;
            place-items: center;
            margin: 0 auto 25px;
            border: 5px solid #1e6091;
            border-radius: 50%;
            background: #eff6ff;
            font-size: 60px;
        }

        .profile-info p {
            padding: 14px;
            margin: 10px 0;
            border-left: 4px solid #1e6091;
            border-radius: 7px;
            background: #f4f7fb;
        }

        .profile-info b {
            color: #123c69;
        }

        #themeBtn {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font: inherit;
        }

        body.dark header,
        body.dark .profile-section {
            background: #1e293b;
            color: #e5e7eb;
        }

        body.dark header a {
            color: #e5e7eb;
        }

        body.dark .profile-section h2,
        body.dark .profile-info b {
            color: #bfdbfe;
        }

        body.dark .profile-info p {
            border-left-color: #60a5fa;
            background: #172033;
            color: #e5e7eb;
        }

        body.dark .profile-avatar {
            border-color: #60a5fa;
            background: #172033;
        }

        body.dark #themeBtn {
            border-color: #475569;
            background: #334155;
            color: #e5e7eb;
        }

        body.dark footer {
            background: #1e293b;
            color: #e5e7eb;
        }

        @media (max-width: 700px) {
            .profile-section {
                width: 95%;
                padding: 22px;
            }
        }
    </style>
</head>

<body>
<header>
    <h1>Student Portal Hub</h1>

    <nav>
        <a href="index.html">Home</a>
        <a href="courses.html">Courses</a>
        <a href="assignment.html">Assignments</a>
        <a href="attendance.html">Attendance</a>
        <a href="faculty.html">Faculty</a>
        <a href="notic.html">Notice</a>
        <a href="profile.php" class="active">Profile</a>
        <a href="contact.html">Contact</a>

        <form action="logout.php" method="post" style="display:inline">
            <button type="submit">Logout</button>
        </form>

        <button type="button" id="themeBtn">🌙 Theme</button>
    </nav>
</header>

<main>
    <section class="profile-section">
        <h2>Student Profile</h2>

        <div class="profile-avatar" aria-hidden="true">🎓</div>

        <div class="profile-info">
            <p><b>Name:</b> <?= escapeHtml($student["name"]) ?></p>
            <p><b>Enrollment No:</b> <?= escapeHtml($student["enrollment"]) ?></p>
            <p><b>Course:</b> <?= escapeHtml($student["course"]) ?></p>
            <p><b>Year:</b> <?= escapeHtml($student["year"]) ?></p>
            <p><b>Gender:</b> <?= escapeHtml($student["gender"]) ?></p>
            <p><b>Email:</b> <?= escapeHtml($student["email"]) ?></p>
            <p><b>Phone:</b> <?= escapeHtml($student["mobile"]) ?></p>
            <p><b>College:</b> CSPIT, CHARUSAT</p>
        </div>
    </section>
</main>

<footer>
    <p>© 2026 Student Portal Hub</p>
</footer>

<script src="js/script.js" defer></script>
</body>
</html>