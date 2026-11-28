<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/layout.php';

$loadError = '';

$totalUsers = 0;
$totalClients = 0;
$totalFreelancers = 0;
$dualProfileUsers = 0;
$totalManagementUsers = 0;
$totalAdminUsers = 0;
$totalStaffUsers = 0;
$totalProjects = 0;
$totalProposals = 0;
$totalAssignments = 0;
$totalServices = 0;
$totalSkills = 0;

$userStatuses = [
    'active' => 0,
    'suspended' => 0,
    'closed' => 0
];

$projectStatuses = [
    'open' => 0,
    'in_progress' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$recentUsers = [];
$recentProjects = [];

try {
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `USER`")->fetchColumn();
    $totalClients = (int)$pdo->query("SELECT COUNT(*) FROM CLIENT")->fetchColumn();
    $totalFreelancers = (int)$pdo->query("SELECT COUNT(*) FROM FREELANCER")->fetchColumn();
    $dualProfileUsers = (int)$pdo->query("
        SELECT COUNT(*)
        FROM CLIENT c
        INNER JOIN FREELANCER f ON c.user_id = f.user_id
    ")->fetchColumn();

    $totalManagementUsers = (int)$pdo->query("SELECT COUNT(*) FROM MANAGEMENT")->fetchColumn();
    $totalAdminUsers = (int)$pdo->query("SELECT COUNT(*) FROM ADMINISTRATION")->fetchColumn();
    $totalStaffUsers = (int)$pdo->query("
        SELECT COUNT(DISTINCT u.user_id)
        FROM `USER` u
        LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
        LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
        WHERE m.user_id IS NOT NULL OR a.user_id IS NOT NULL
    ")->fetchColumn();

    $totalProjects = (int)$pdo->query("SELECT COUNT(*) FROM PROJECT")->fetchColumn();
    $totalProposals = (int)$pdo->query("SELECT COUNT(*) FROM PROPOSAL")->fetchColumn();
    $totalAssignments = (int)$pdo->query("SELECT COUNT(*) FROM ASSIGNMENT")->fetchColumn();
    $totalServices = (int)$pdo->query("SELECT COUNT(*) FROM AI_SERVICE")->fetchColumn();
    $totalSkills = (int)$pdo->query("SELECT COUNT(*) FROM AI_SKILL")->fetchColumn();

    $userStatusRows = $pdo->query("
        SELECT account_status, COUNT(*) AS total
        FROM `USER`
        GROUP BY account_status
    ")->fetchAll();
    foreach ($userStatusRows as $row) {
        if (array_key_exists($row['account_status'], $userStatuses)) {
            $userStatuses[$row['account_status']] = (int)$row['total'];
        }
    }

    $projectStatusRows = $pdo->query("
        SELECT project_status, COUNT(*) AS total
        FROM PROJECT
        GROUP BY project_status
    ")->fetchAll();
    foreach ($projectStatusRows as $row) {
        if (array_key_exists($row['project_status'], $projectStatuses)) {
            $projectStatuses[$row['project_status']] = (int)$row['total'];
        }
    }

    $recentUsers = $pdo->query("
        SELECT
            u.user_id,
            u.full_name,
            u.email,
            u.account_status,
            u.created_at,
            CASE WHEN c.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_client,
            CASE WHEN f.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_freelancer,
            CASE WHEN m.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_management,
            CASE WHEN a.user_id IS NOT NULL THEN 1 ELSE 0 END AS is_admin
        FROM `USER` u
        LEFT JOIN CLIENT c ON u.user_id = c.user_id
        LEFT JOIN FREELANCER f ON u.user_id = f.user_id
        LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
        LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
        ORDER BY u.created_at DESC
        LIMIT 5
    ")->fetchAll();

    $recentProjects = $pdo->query("
        SELECT
            p.project_id,
            p.project_title,
            p.budget_min,
            p.budget_max,
            p.project_status,
            p.deadline,
            p.created_at,
            u.full_name AS client_name
        FROM PROJECT p
        LEFT JOIN USER_PROJECT up ON p.project_id = up.project_id
        LEFT JOIN `USER` u ON up.user_id = u.user_id
        ORDER BY p.created_at DESC
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $ex) {
    $loadError = 'Some platform statistics could not be loaded right now.';
}

function calc_percent(int $part, int $whole): int
{
    if ($whole <= 0) {
        return 0;
    }
    return (int)round(($part / $whole) * 100);
}

$firstName = explode(' ', trim($currentStaff['full_name']))[0];

render_management_header(
    'Overview',
    'Live platform summary and operational health',
    'dashboard',
    $currentStaff,
    $staffRoleLabel
);
?>

<section class="mgmt-banner">
    <div>
        <span class="eyebrow">FREELANCERAI MANAGEMENT</span>
        <h2>Welcome back, <span><?= e($firstName) ?></span></h2>
        <p>Monitor registered accounts, track project activity, and maintain the official catalog of AI services and AI skills.</p>
    </div>
    <span class="mgmt-pill"><?= e($staffRoleLabel) ?></span>
</section>

<?php if ($loadError !== ''): ?>
    <div class="alert-banner alert-error"><?= e($loadError) ?></div>
<?php endif; ?>

<section class="stats-grid">
    <div class="stat-card stat-card-dark">
        <p class="stat-label">Total Registered Users</p>
        <p class="stat-value"><?= $totalUsers ?></p>
        <p class="stat-sub"><?= $dualProfileUsers ?> with both Client &amp; Freelancer</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Clients</p>
        <p class="stat-value"><?= $totalClients ?></p>
        <p class="stat-sub">Registered client profiles</p>
    </div>

    <div class="stat-card stat-card-accent">
        <p class="stat-label">Freelancers</p>
        <p class="stat-value"><?= $totalFreelancers ?></p>
        <p class="stat-sub">Registered freelancer profiles</p>
    </div>

    <div class="stat-card stat-card-dark">
        <p class="stat-label">Staff Accounts</p>
        <p class="stat-value"><?= $totalStaffUsers ?></p>
        <p class="stat-sub">
            <?= $totalManagementUsers ?> management<?= $hasAdminProfile ? ' · ' . $totalAdminUsers . ' admin' : '' ?>
        </p>
    </div>
</section>

<section class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Projects</p>
        <p class="stat-value"><?= $totalProjects ?></p>
        <p class="stat-sub"><?= $projectStatuses['open'] ?> open · <?= $projectStatuses['in_progress'] ?> in progress</p>
    </div>

    <div class="stat-card stat-card-accent">
        <p class="stat-label">Total Proposals</p>
        <p class="stat-value"><?= $totalProposals ?></p>
        <p class="stat-sub">Submitted by freelancers</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Total Assignments</p>
        <p class="stat-value"><?= $totalAssignments ?></p>
        <p class="stat-sub">Active &amp; completed contracts</p>
    </div>

    <div class="stat-card stat-card-accent">
        <p class="stat-label">AI Catalog</p>
        <p class="stat-value"><?= $totalServices + $totalSkills ?></p>
        <p class="stat-sub"><?= $totalServices ?> services · <?= $totalSkills ?> skills</p>
    </div>
</section>

<section class="mgmt-grid-two">
    <div class="mgmt-panel">
        <div class="panel-header">
            <h3>User Account Status Distribution</h3>
            <a href="users.php" class="panel-link">Manage Users</a>
        </div>
        <div class="chart-list">
            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Active Accounts</span>
                    <span class="chart-row-value"><?= $userStatuses['active'] ?> (<?= calc_percent($userStatuses['active'], $totalUsers) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-success" style="width: <?= calc_percent($userStatuses['active'], $totalUsers) ?>%;"></div>
                </div>
            </div>

            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Suspended Accounts</span>
                    <span class="chart-row-value"><?= $userStatuses['suspended'] ?> (<?= calc_percent($userStatuses['suspended'], $totalUsers) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-warning" style="width: <?= calc_percent($userStatuses['suspended'], $totalUsers) ?>%;"></div>
                </div>
            </div>

            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Closed Accounts</span>
                    <span class="chart-row-value"><?= $userStatuses['closed'] ?> (<?= calc_percent($userStatuses['closed'], $totalUsers) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-danger" style="width: <?= calc_percent($userStatuses['closed'], $totalUsers) ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mgmt-panel">
        <div class="panel-header">
            <h3>Projects by Status</h3>
            <a href="projects.php" class="panel-link">View Projects</a>
        </div>
        <div class="chart-list">
            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Open</span>
                    <span class="chart-row-value"><?= $projectStatuses['open'] ?> (<?= calc_percent($projectStatuses['open'], $totalProjects) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-cyan" style="width: <?= calc_percent($projectStatuses['open'], $totalProjects) ?>%;"></div>
                </div>
            </div>

            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">In Progress</span>
                    <span class="chart-row-value"><?= $projectStatuses['in_progress'] ?> (<?= calc_percent($projectStatuses['in_progress'], $totalProjects) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-warning" style="width: <?= calc_percent($projectStatuses['in_progress'], $totalProjects) ?>%;"></div>
                </div>
            </div>

            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Completed</span>
                    <span class="chart-row-value"><?= $projectStatuses['completed'] ?> (<?= calc_percent($projectStatuses['completed'], $totalProjects) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-success" style="width: <?= calc_percent($projectStatuses['completed'], $totalProjects) ?>%;"></div>
                </div>
            </div>

            <div>
                <div class="chart-row-head">
                    <span class="chart-row-label">Cancelled</span>
                    <span class="chart-row-value"><?= $projectStatuses['cancelled'] ?> (<?= calc_percent($projectStatuses['cancelled'], $totalProjects) ?>%)</span>
                </div>
                <div class="chart-track">
                    <div class="chart-fill chart-fill-danger" style="width: <?= calc_percent($projectStatuses['cancelled'], $totalProjects) ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mgmt-grid-main">
    <div>
        <div class="mgmt-panel">
            <div class="panel-header">
                <h3>Recent User Registrations</h3>
                <a href="users.php" class="panel-link">View All Users</a>
            </div>
            <?php if ($recentUsers): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Profiles</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $row): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($row['full_name']) ?></strong><br>
                                        <span class="text-sub"><?= e($row['email']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['is_admin']): ?><span class="badge badge-staff">Admin</span><?php endif; ?>
                                        <?php if ($row['is_management']): ?><span class="badge badge-staff">Management</span><?php endif; ?>
                                        <?php if ($row['is_client']): ?><span class="badge badge-role">Client</span><?php endif; ?>
                                        <?php if ($row['is_freelancer']): ?><span class="badge badge-role">Freelancer</span><?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= e($row['account_status']) ?>">
                                            <?= e($row['account_status']) ?>
                                        </span>
                                    </td>
                                    <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="empty-message">No registered users found.</p>
            <?php endif; ?>
        </div>

        <div class="mgmt-panel">
            <div class="panel-header">
                <h3>Recent Projects</h3>
                <a href="projects.php" class="panel-link">View All Projects</a>
            </div>
            <?php if ($recentProjects): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Budget</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProjects as $proj): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($proj['project_title']) ?></strong><br>
                                        <span class="text-sub">Posted <?= e(date('d M Y', strtotime($proj['created_at']))) ?></span>
                                    </td>
                                    <td><?= e($proj['client_name'] ?: 'Unassigned') ?></td>
                                    <td>
                                        Rs. <?= number_format((float)$proj['budget_min'], 2) ?>
                                        - Rs. <?= number_format((float)$proj['budget_max'], 2) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= e($proj['project_status']) ?>">
                                            <?= e(str_replace('_', ' ', $proj['project_status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="empty-message">No projects have been published yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="mgmt-panel">
            <div class="panel-header">
                <h3>Your Staff Profile</h3>
            </div>
            <div class="meta-list">
                <div class="meta-row">
                    <span class="meta-label">Full Name</span>
                    <span class="meta-value"><?= e($currentStaff['full_name']) ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Email</span>
                    <span class="meta-value"><?= e($currentStaff['email']) ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Access Tier</span>
                    <span class="meta-value"><?= $hasAdminProfile ? 'Administration' : 'Management' ?></span>
                </div>
                <?php if ($hasAdminProfile): ?>
                    <div class="meta-row">
                        <span class="meta-label">Admin Level</span>
                        <span class="meta-value"><?= e(ucwords(str_replace('_', ' ', $adminLevel))) ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Department</span>
                        <span class="meta-value"><?= e($currentStaff['admin_department']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($hasManagementProfile): ?>
                    <div class="meta-row">
                        <span class="meta-label">Management Dept.</span>
                        <span class="meta-value"><?= e($currentStaff['management_department']) ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Designation</span>
                        <span class="meta-value"><?= e($currentStaff['management_designation']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="meta-row">
                    <span class="meta-label">Account Status Updates</span>
                    <span class="meta-value"><?= $canManageAccountStatus ? 'Authorized' : 'View Only' ?></span>
                </div>
            </div>
        </div>

        <div class="mgmt-panel">
            <div class="panel-header">
                <h3>Quick Navigation</h3>
            </div>
            <div class="quick-links">
                <a href="users.php"><span>Inspect Registered Users</span><span>&rarr;</span></a>
                <a href="services.php"><span>Manage AI Services</span><span>&rarr;</span></a>
                <a href="skills.php"><span>Manage AI Skills</span><span>&rarr;</span></a>
                <a href="projects.php"><span>Monitor Platform Projects</span><span>&rarr;</span></a>
            </div>
        </div>
    </div>
</section>

<?php
render_management_footer();
