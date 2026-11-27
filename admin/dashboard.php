
<?php
require_once "../includes/admin_guard.php";
require_once "../config/database.php";

$adminName = $_SESSION["full_name"] ?? "Administrator";

$stats = [
    "users" => 0,
    "freelancers" => 0,
    "clients" => 0,
    "administrators" => 0,
    "management" => 0,
    "projects" => 0,
    "open_projects" => 0,
    "assignments" => 0,
    "payments" => 0,
    "ai_services" => 0,
    "ai_skills" => 0
];

$recentUsers = [];
$recentProjects = [];
$paymentTotal = 0;

try {
    $queries = [
        "users" => "SELECT COUNT(*) FROM `USER`",
        "freelancers" => "SELECT COUNT(*) FROM FREELANCER",
        "clients" => "SELECT COUNT(*) FROM CLIENT",
        "administrators" => "SELECT COUNT(*) FROM ADMINISTRATION",
        "management" => "SELECT COUNT(*) FROM MANAGEMENT",
        "projects" => "SELECT COUNT(*) FROM PROJECT",
        "open_projects" => "SELECT COUNT(*) FROM PROJECT WHERE project_status = 'open'",
        "assignments" => "SELECT COUNT(*) FROM ASSIGNMENT",
        "payments" => "SELECT COUNT(*) FROM PAYMENT",
        "ai_services" => "SELECT COUNT(*) FROM AI_SERVICE",
        "ai_skills" => "SELECT COUNT(*) FROM AI_SKILL"
    ];

    foreach ($queries as $key => $sql) {
        $stats[$key] = (int) $pdo->query($sql)->fetchColumn();
    }

    $recentUsersStmt = $pdo->query("
        SELECT user_id, full_name, email, account_status, created_at
        FROM `USER`
        ORDER BY created_at DESC
        LIMIT 5
    ");

    $recentUsers = $recentUsersStmt->fetchAll();

    $recentProjectsStmt = $pdo->query("
        SELECT
            p.project_id,
            p.project_title,
            p.project_status,
            p.budget_min,
            p.budget_max,
            p.created_at,
            u.full_name AS client_name
        FROM PROJECT p
        LEFT JOIN USER_PROJECT up ON p.project_id = up.project_id
        LEFT JOIN `USER` u ON up.user_id = u.user_id
        ORDER BY p.created_at DESC
        LIMIT 5
    ");

    $recentProjects = $recentProjectsStmt->fetchAll();

    $paymentTotal = (float) $pdo->query("
        SELECT COALESCE(SUM(amount), 0)
        FROM PAYMENT
        WHERE payment_status = 'completed'
    ")->fetchColumn();

} catch (PDOException $e) {
    $databaseError = "Some dashboard information could not be loaded. Please check your database tables and columns.";
}

function e($value) {
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

function formatCurrency($amount) {
    return "Rs. " . number_format((float) $amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Dashboard | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-brand">
            <a href="dashboard.php">Freelancer<span>AI</span></a>
            <small>ADMINISTRATION</small>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-label">MAIN MENU</span>

            <a href="dashboard.php" class="sidebar-link active">
                <span>▦</span> Dashboard
            </a>

            <a href="users.php" class="sidebar-link">
                <span>♙</span> All Users
            </a>

            <a href="freelancers.php" class="sidebar-link">
                <span>♧</span> Freelancers
            </a>

            <a href="clients.php" class="sidebar-link">
                <span>♧</span> Clients
            </a>

            <a href="projects.php" class="sidebar-link">
                <span>▣</span> Projects
            </a>

            <a href="proposals.php" class="sidebar-link">
                <span>▤</span> Proposals
            </a>

            <a href="assignments.php" class="sidebar-link">
                <span>▧</span> Assignments
            </a>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-label">PLATFORM CONTROL</span>

            <a href="management.php" class="sidebar-link">
                <span>♙</span> Management
            </a>

            <a href="administrators.php" class="sidebar-link">
                <span>♜</span> Super Admins
            </a>

            <a href="services.php" class="sidebar-link">
                <span>✧</span> AI Services
            </a>

            <a href="skills.php" class="sidebar-link">
                <span>◇</span> AI Skills
            </a>
        </div>

        <div class="sidebar-section">
            <span class="sidebar-label">FINANCIAL CONTROL</span>

            <a href="payments.php" class="sidebar-link">
                <span>▤</span> Payments
            </a>

            <a href="reviews.php" class="sidebar-link">
                <span>☆</span> Reviews
            </a>

            <a href="system_settings.php" class="sidebar-link">
                <span>⚙</span> System Settings
            </a>
        </div>

        <div class="sidebar-bottom">
            <div class="admin-profile">
                <div class="profile-avatar">
                    <?= strtoupper(substr($adminName, 0, 1)) ?>
                </div>
                <div>
                    <strong><?= e($adminName) ?></strong>
                    <small>Super Administrator</small>
                </div>
            </div>

            <a href="../auth/logout.php" class="logout-link">↪ Sign Out</a>
        </div>
    </aside>

    <main class="admin-main">

        <header class="admin-topbar">
            <div>
                <span class="breadcrumb">Administration / Overview</span>
                <h1>Dashboard</h1>
            </div>

            <div class="topbar-right">
                <span class="admin-status">
                    <span></span> System Administrator
                </span>
                <div class="topbar-avatar">
                    <?= strtoupper(substr($adminName, 0, 1)) ?>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <div>
                <span class="welcome-tag">PLATFORM CONTROL CENTRE</span>
                <h2>Welcome back, <?= e($adminName) ?></h2>
                <p>Monitor platform performance and manage FreelancerAI operations.</p>
            </div>

            <div class="welcome-date">
                <?= date("d M Y") ?>
            </div>
        </section>

        <?php if (isset($databaseError)): ?>
            <div class="database-warning">
                <?= e($databaseError) ?>
            </div>
        <?php endif; ?>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Platform Overview</h2>
                    <p>Key platform statistics</p>
                </div>
            </div>

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">♙</div>
                    <div class="stat-info">
                        <span>Total Users</span>
                        <h3><?= number_format($stats["users"]) ?></h3>
                        <small>Registered accounts</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon cyan">♧</div>
                    <div class="stat-info">
                        <span>Freelancers</span>
                        <h3><?= number_format($stats["freelancers"]) ?></h3>
                        <small>Professional accounts</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">♧</div>
                    <div class="stat-info">
                        <span>Clients</span>
                        <h3><?= number_format($stats["clients"]) ?></h3>
                        <small>Client accounts</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">▣</div>
                    <div class="stat-info">
                        <span>Total Projects</span>
                        <h3><?= number_format($stats["projects"]) ?></h3>
                        <small><?= number_format($stats["open_projects"]) ?> currently open</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">▧</div>
                    <div class="stat-info">
                        <span>Assignments</span>
                        <h3><?= number_format($stats["assignments"]) ?></h3>
                        <small>Platform assignments</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon teal">▤</div>
                    <div class="stat-info">
                        <span>Payments</span>
                        <h3><?= number_format($stats["payments"]) ?></h3>
                        <small>Payment records</small>
                    </div>
                </div>

            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Administration & AI</h2>
                    <p>Privileged accounts and platform resources</p>
                </div>
            </div>

            <div class="secondary-stats">

                <div class="secondary-stat">
                    <span>Super Administrators</span>
                    <strong><?= number_format($stats["administrators"]) ?></strong>
                    <a href="administrators.php">Manage accounts →</a>
                </div>

                <div class="secondary-stat">
                    <span>Management Staff</span>
                    <strong><?= number_format($stats["management"]) ?></strong>
                    <a href="management.php">Manage staff →</a>
                </div>

                <div class="secondary-stat">
                    <span>AI Services</span>
                    <strong><?= number_format($stats["ai_services"]) ?></strong>
                    <a href="services.php">Manage services →</a>
                </div>

                <div class="secondary-stat">
                    <span>AI Skills</span>
                    <strong><?= number_format($stats["ai_skills"]) ?></strong>
                    <a href="skills.php">Manage skills →</a>
                </div>

            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Financial Overview</h2>
                    <p>Total completed payment value</p>
                </div>
                <a href="payments.php" class="view-all">View payments →</a>
            </div>

            <div class="financial-card">
                <div>
                    <span>Completed Payments</span>
                    <h2><?= e(formatCurrency($paymentTotal)) ?></h2>
                    <small>Based on completed payment records</small>
                </div>
                <div class="financial-icon">Rs.</div>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Recent Registrations</h2>
                    <p>Latest accounts registered on the platform</p>
                </div>
                <a href="users.php" class="view-all">View all users →</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email Address</th>
                            <th>Registered</th>
                            <th>Account Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentUsers)): ?>
                            <tr>
                                <td colspan="4" class="empty-state">No registered users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($user["full_name"]) ?></strong>
                                        <small class="table-id">ID: <?= e($user["user_id"]) ?></small>
                                    </td>
                                    <td><?= e($user["email"]) ?></td>
                                    <td><?= e($user["created_at"]) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user["account_status"] === "active" ? "status-active" : "status-inactive" ?>">
                                            <?= e(ucfirst($user["account_status"])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Recent Projects</h2>
                    <p>Latest projects submitted by clients</p>
                </div>
                <a href="projects.php" class="view-all">View all projects →</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentProjects)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">No projects found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentProjects as $project): ?>
                                <tr>
                                    <td><strong><?= e($project["project_title"]) ?></strong></td>
                                    <td><?= e($project["client_name"] ?? "Unknown") ?></td>
                                    <td>
                                        <?= e(formatCurrency($project["budget_min"])) ?>
                                        –
                                        <?= e(formatCurrency($project["budget_max"])) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-project">
                                            <?= e(ucfirst($project["project_status"])) ?>
                                        </span>
                                    </td>
                                    <td><?= e($project["created_at"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="admin-footer">
            <span>FreelancerAI Administration</span>
            <span>Platform Control Centre</span>
        </footer>

    </main>
</div>

</body>
</html>
