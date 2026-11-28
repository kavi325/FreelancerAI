# ai-projects-services-freelancing-platform-PUSL2019-G12

A database-driven web application designed to connect clients and organizations with AI freelancers. The platform supports freelancer profiles, AI skills and services, project posting, proposal management, project assignments, payment processing, ratings, reviews, and administrative reporting.

## Local Setup

1. Clone the repository into your local Apache / Laragon web root (`FreelancerAI/`).
2. Import the database schema from  `config/db.sql`  into MySQL / MariaDB.
3. Update `config/database.php` with your own local MySQL username and password. Do not commit personal database passwords.
4. Open the application in your browser at `http://localhost/FreelancerAI/`.

## Management Dashboard Module (S.V. Thilakarathne)

### Included Files
- `management/auth_guard.php` — Session validation, database-backed `MANAGEMENT` / `ADMINISTRATION` role checks, CSRF protection, and output escaping.
- `management/layout.php` — Shared sidebar navigation, header, and responsive layout.
- `assets/css/management.css` — Management dashboard styles reusing CSS variables from `assets/css/style.css`.
- `management/dashboard.php` — Platform overview with live database counts, status distribution charts, recent users, and recent projects.
- `management/users.php` — User search, role/status filtering, profile details viewer, and Administration-only account status updates.
- `management/services.php` — `AI_SERVICE` catalog listing, search, add/edit with duplicate prevention, and Administration-only delete.
- `management/skills.php` — `AI_SKILL` catalog listing with freelancer/project usage counts, add/edit with duplicate prevention, and protected deletion for referenced skills.
- `management/projects.php` — Read-only platform-wide project oversight with search and status filtering.
- `management/logout.php` — Staff logout handler.

### Demo Test Accounts (Local Testing)

Sign in at `http://localhost/FreelancerAI/auth/login.php`:

| Role | Email | Password | Permissions in Management Dashboard |
| --- | --- | --- | --- |
| Management (`MANAGEMENT`) | `management@gmail.com` | `Manage@2026` | View Overview, search/inspect Users, add/edit AI Services & AI Skills, view Projects (read-only) |
| Super Admin (`ADMINISTRATION`) | `admin@gmail.com` | `Admin@2026` | All Management features + update other users' `account_status` + delete unused AI Services and unreferenced AI Skills |

### Verification Notes
- Logged-out users are redirected to `auth/login.php`.
- Client and Freelancer accounts receive HTTP `403 Access Restricted` if they try to open `management/` pages.
- Password hashes are never selected or displayed in any management view.
