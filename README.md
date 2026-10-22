# Student Portal Hub

A student portal built with PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap.

## Setup

1. Copy the project into `C:\XAMPP\htdocs\studentportelhub`.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Create a database named `studentportelhub`.
5. Select the database, open **Import**, choose `studenthub.sql`, and click **Go**.
6. Check the database name and credentials in `db.php`.
7. Open `http://localhost/studentportelhub/index.html`.

## Pages

- Login: `http://localhost/studentportelhub/login.php`
- Admin dashboard: `http://localhost/studentportelhub/admin_dashboard.php`
- Student dashboard: `http://localhost/studentportelhub/student_dashboard.php`
- Student records: `http://localhost/studentportelhub/student.php`
- Student API practical: `http://localhost/studentportelhub/student_api.html`
- Event management: `http://localhost/studentportelhub/admin_events.php`

## Testing

1. Log in as Admin and check dashboard counts, latest registrations, event management, and audit logs.
2. Log in as Student and check the Student dashboard.
3. While logged in as Student, open `admin_dashboard.php`; access should be denied with HTTP 403.
4. On `student_api.html`, test listing, adding, searching, editing, and deleting student records.
5. Open browser Developer Tools → **Network** → **Fetch/XHR** to inspect API requests and JSON responses.
6. Check `audit_logs` in phpMyAdmin after performing an admin action.

Use demo data for testing. Do not publish real user information or passwords in the database dump.