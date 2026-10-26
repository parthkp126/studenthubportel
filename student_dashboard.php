<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login.php?error=login_required");
    exit;
}

if (($_SESSION["role"] ?? "") !== "Student") {
    http_response_code(403);
    exit("403 Forbidden: Student access required.");
}

$studentName = htmlspecialchars(
    (string) ($_SESSION["name"] ?? "Student"),
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Student Portal Hub</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: #f3f6fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        .student-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 5%;
            background: #fff;
            border-bottom: 1px solid #e1e7ef;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
        }

        .brand {
            color: #2459d3;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
        }

        .logout-button {
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #dc3545;
            color: #fff;
            font: inherit;
            cursor: pointer;
        }

        .logout-button:hover {
            background: #b42332;
        }

        .dashboard {
            width: min(1100px, 90%);
            margin: 40px auto;
        }

        .welcome-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 32px;
            border-radius: 16px;
            background: linear-gradient(120deg, #2459d3, #3978ed);
            color: #fff;
            box-shadow: 0 10px 28px rgba(36, 89, 211, 0.18);
        }

        .welcome-card h1 {
            margin: 0 0 10px;
            font-size: clamp(1.7rem, 4vw, 2.4rem);
        }

        .welcome-card p {
            margin: 0;
            line-height: 1.6;
        }

        .welcome-icon {
            font-size: 4rem;
        }

        .section-title {
            margin: 32px 0 16px;
            color: #172033;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .dashboard-card {
            display: block;
            min-height: 130px;
            padding: 22px;
            border: 1px solid #e1e7ef;
            border-radius: 14px;
            background: #fff;
            color: #172033;
            text-decoration: none;
            box-shadow: 0 5px 16px rgba(15, 23, 42, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
        }

        .card-icon {
            display: block;
            margin-bottom: 12px;
            font-size: 2rem;
        }

        .dashboard-card h2 {
            margin: 0 0 8px;
            color: #2459d3;
            font-size: 1.2rem;
        }

        .dashboard-card p {
            margin: 0;
            color: #52627a;
            line-height: 1.5;
        }

        .student-footer {
            padding: 20px;
            color: #64748b;
            text-align: center;
        }

        body.dark {
            background: #0f172a;
            color: #e5e7eb;
        }

        body.dark .student-topbar,
        body.dark .dashboard-card {
            border-color: #334155;
            background: #1e293b;
            color: #e5e7eb;
        }

        body.dark .section-title,
        body.dark .dashboard-card h2 {
            color: #bfdbfe;
        }

        body.dark .dashboard-card p,
        body.dark .student-footer {
            color: #cbd5e1;
        }

        @media (max-width: 750px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .welcome-card {
                padding: 24px;
            }

            .welcome-icon {
                font-size: 3rem;
            }
        }

        @media (max-width: 520px) {
            .student-topbar {
                padding: 14px 5%;
            }

            .brand {
                font-size: 1.1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .welcome-card {
                align-items: flex-start;
                padding: 22px;
            }
        }
    </style>
</head>

<body>
    <header class="student-topbar">
        <a class="brand" href="index.html">🎓 Student Portal Hub</a>

        <form action="logout.php" method="post">
            <button class="logout-button" type="submit">Logout</button>
        </form>
    </header>

    <main class="dashboard">
        <section class="welcome-card">
            <div>
                <h1>Welcome, <?= $studentName ?>!</h1>
                <p>Your student dashboard is ready. Choose a section to continue.</p>
            </div>
            <div class="welcome-icon" aria-hidden="true">🎓</div>
        </section>

        <h2 class="section-title">Your Portal</h2>

        <section class="dashboard-grid" aria-label="Student portal links">
            <a class="dashboard-card" href="profile.php">
                <span class="card-icon" aria-hidden="true">👤</span>
                <h2>My Profile</h2>
                <p>View your registered student details.</p>
            </a>

            <a class="dashboard-card" href="courses.html">
                <span class="card-icon" aria-hidden="true">📚</span>
                <h2>Courses</h2>
                <p>Open course information and subjects.</p>
            </a>

            <a class="dashboard-card" href="assignment.html">
                <span class="card-icon" aria-hidden="true">📝</span>
                <h2>Assignments</h2>
                <p>Review assignment information and due dates.</p>
            </a>

            <a class="dashboard-card" href="attendance.html">
                <span class="card-icon" aria-hidden="true">📊</span>
                <h2>Attendance</h2>
                <p>View attendance information.</p>
            </a>

            <a class="dashboard-card" href="events.html">
                <span class="card-icon" aria-hidden="true">🗓️</span>
                <h2>College Events</h2>
                <p>Find upcoming events and activities.</p>
            </a>

            <a class="dashboard-card" href="notic.html">
                <span class="card-icon" aria-hidden="true">📢</span>
                <h2>Notice Board</h2>
                <p>Read current college notices.</p>
            </a>
        </section>
    </main>

    <footer class="student-footer">
        <p>© 2026 Student Portal Hub</p>
    </footer>

    <script>
        if (localStorage.getItem("studenthub-theme") === "dark") {
            document.body.classList.add("dark");
        }
    </script>
</body>
</html>