
# Student Portal Hub — Test & Security Report

## Test Environment

- OS: Windows
- Server: XAMPP (Apache and MySQL)
- Database: studentportelhub
- Browser: __________________
- Test date: __________________

## Functional Testing

| Test | Result | Evidence |
|---|---|---|
| Admin login and dashboard | [ ] Pass [ ] Fail | `admin-dashboard.png` |
| Student login and dashboard | [ ] Pass [ ] Fail | `student-dashboard.png` |
| Dashboard counts and latest registrations | [ ] Pass [ ] Fail | `dashboard-overview.png` |
| Student API list/add/edit/delete | [ ] Pass [ ] Fail | Screenshots in report |
| Event create/update/delete | [ ] Pass [ ] Fail | Screenshots in report |
| Admin action recorded in `audit_logs` | [ ] Pass [ ] Fail | `audit-log-proof.png` |

## Security Testing

| Check | Result | Notes |
|---|---|---|
| Student cannot open Admin dashboard (403) | [ ] Pass [ ] Fail | `student-403.png` |
| Passwords verified with `password_verify()` | [ ] Pass [ ] Fail | |
| Session ID regenerated after login | [ ] Pass [ ] Fail | |
| Database queries use prepared statements | [ ] Pass [ ] Fail | |
| Student deletion changes status to Inactive | [ ] Pass [ ] Fail | |
| Poster type and size are validated | [ ] Pass [ ] Fail | |
| Admin actions are recorded in audit logs | [ ] Pass [ ] Fail | |

## Accessibility Checklist

| Check | Result |
|---|---|
| Forms have labels for their fields | [ ] Pass [ ] Needs work |
| Pages can be navigated using the keyboard | [ ] Pass [ ] Needs work |
| Images have meaningful alternative text | [ ] Pass [ ] Needs work |
| Headings follow a clear order | [ ] Pass [ ] Needs work |
| Text and background have readable contrast | [ ] Pass [ ] Needs work |
| Pages work at mobile screen widths | [ ] Pass [ ] Needs work |
| Success and error messages are clearly shown | [ ] Pass [ ] Needs work |

## Issues Found

- ________________________________________________
- ________________________________________________

## Summary

=======
# Student Portal Hub — Test & Security Report

## Test Environment

- OS: Windows
- Server: XAMPP (Apache and MySQL)
- Database: studentportelhub
- Browser: __________________
- Test date: __________________

## Functional Testing

| Test | Result | Evidence |
|---|---|---|
| Admin login and dashboard | [ ] Pass [ ] Fail | `admin-dashboard.png` |
| Student login and dashboard | [ ] Pass [ ] Fail | `student-dashboard.png` |
| Dashboard counts and latest registrations | [ ] Pass [ ] Fail | `dashboard-overview.png` |
| Student API list/add/edit/delete | [ ] Pass [ ] Fail | Screenshots in report |
| Event create/update/delete | [ ] Pass [ ] Fail | Screenshots in report |
| Admin action recorded in `audit_logs` | [ ] Pass [ ] Fail | `audit-log-proof.png` |

## Security Testing

| Check | Result | Notes |
|---|---|---|
| Student cannot open Admin dashboard (403) | [ ] Pass [ ] Fail | `student-403.png` |
| Passwords verified with `password_verify()` | [ ] Pass [ ] Fail | |
| Session ID regenerated after login | [ ] Pass [ ] Fail | |
| Database queries use prepared statements | [ ] Pass [ ] Fail | |
| Student deletion changes status to Inactive | [ ] Pass [ ] Fail | |
| Poster type and size are validated | [ ] Pass [ ] Fail | |
| Admin actions are recorded in audit logs | [ ] Pass [ ] Fail | |

## Accessibility Checklist

| Check | Result |
|---|---|
| Forms have labels for their fields | [ ] Pass [ ] Needs work |
| Pages can be navigated using the keyboard | [ ] Pass [ ] Needs work |
| Images have meaningful alternative text | [ ] Pass [ ] Needs work |
| Headings follow a clear order | [ ] Pass [ ] Needs work |
| Text and background have readable contrast | [ ] Pass [ ] Needs work |
| Pages work at mobile screen widths | [ ] Pass [ ] Needs work |
| Success and error messages are clearly shown | [ ] Pass [ ] Needs work |

## Issues Found

- ________________________________________________
- ________________________________________________

## Summary


The tests above were performed on the local XAMPP installation. Results are recorded in the tables, with screenshots provided as evidence.