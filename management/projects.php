<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/layout.php';

$allowedProjectStatuses = ['open', 'in_progress', 'completed', 'cancelled'];
$searchTerm = trim((string)($_GET['search'] ?? ''));
$statusFilter = trim((string)($_GET['status'] ?? 'all'));
$errorMessage = '';

if ($statusFilter !== 'all' && !in_array($statusFilter, $allowedProjectStatuses, true)) {
    $statusFilter = 'all';
}

$whereClauses = [];
$params = [];

if ($searchTerm !== '') {
    $whereClauses[] = '(p.project_title LIKE ? OR u.full_name LIKE ? OR c.organization_name LIKE ?)';
    $likeValue = '%' . $searchTerm . '%';
    $params[] = $likeValue;
    $params[] = $likeValue;
    $params[] = $likeValue;
}

if ($statusFilter !== 'all') {
    $whereClauses[] = 'p.project_status = ?';
    $params[] = $statusFilter;
}

$sql = "
    SELECT
        p.project_id,
        p.project_title,
        p.budget_min,
        p.budget_max,
        p.project_status,
        p.deadline,
        p.created_at,
        u.full_name AS client_name,
        c.organization_name,
        (SELECT COUNT(*) FROM PROPOSAL pr WHERE pr.project_id = p.project_id) AS proposal_count
    FROM PROJECT p
    LEFT JOIN USER_PROJECT up ON p.project_id = up.project_id
    LEFT JOIN CLIENT c ON up.user_id = c.user_id
    LEFT JOIN `USER` u ON c.user_id = u.user_id
";

if (!empty($whereClauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
}

$sql .= ' ORDER BY p.created_at DESC';

$projects = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (PDOException $ex) {
    $errorMessage = 'Unable to load platform projects right now.';
}

render_management_header(
    'Project Oversight',
    'Platform-wide read-only monitoring of client projects and proposal activity',
    'projects',
    $currentStaff,
    $staffRoleLabel
);
?>

<section class="mgmt-banner">
    <div>
        <span class="eyebrow">READ-ONLY MONITORING</span>
        <h2>Project <span>Oversight</span></h2>
        <p>Track all client-published projects, budget ranges, deadlines, and proposal counts without interfering with client project ownership.</p>
    </div>
    <span class="mgmt-pill"><?= count($projects) ?> Projects Found</span>
</section>

<?php if ($errorMessage !== ''): ?>
    <div class="alert-banner alert-error"><?= e($errorMessage) ?></div>
<?php endif; ?>

<section class="mgmt-panel">
    <form method="GET" action="projects.php" class="filter-bar">
        <div class="filter-field">
            <label for="search">Search Project or Client</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Enter project title, client name, or organization..."
                value="<?= e($searchTerm) ?>"
            >
        </div>

        <div class="filter-field">
            <label for="status">Project Status</label>
            <select id="status" name="status">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="open" <?= $statusFilter === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <div class="action-group">
            <button type="submit" class="btn-action">Apply Filters</button>
            <a href="projects.php" class="btn-outline">Reset</a>
        </div>
    </form>

    <?php if ($projects): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project Title</th>
                        <th>Client</th>
                        <th>Budget Range</th>
                        <th>Proposals</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>#<?= (int)$project['project_id'] ?></td>
                            <td><strong><?= e($project['project_title']) ?></strong></td>
                            <td>
                                <?= e($project['client_name'] ?: 'Unassigned') ?>
                                <?php if (!empty($project['organization_name'])): ?>
                                    <br><span class="text-sub"><?= e($project['organization_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                Rs. <?= number_format((float)$project['budget_min'], 2) ?>
                                - Rs. <?= number_format((float)$project['budget_max'], 2) ?>
                            </td>
                            <td><?= (int)$project['proposal_count'] ?></td>
                            <td>
                                <span class="badge badge-<?= e($project['project_status']) ?>">
                                    <?= e(str_replace('_', ' ', $project['project_status'])) ?>
                                </span>
                            </td>
                            <td><?= !empty($project['deadline']) ? e(date('d M Y', strtotime($project['deadline']))) : 'Not set' ?></td>
                            <td><?= e(date('d M Y', strtotime($project['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-message">No projects match the selected criteria.</p>
    <?php endif; ?>
</section>

<?php
render_management_footer();
