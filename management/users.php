<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/layout.php';

$allowedAccountStatuses = ['active', 'suspended', 'closed'];
$allowedRoleFilters = ['all', 'client', 'freelancer', 'both', 'management', 'administration'];

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errorMessage = 'Invalid security token. Please refresh the page and try again.';
    } elseif ($action === 'update_status') {
        if (!$canManageAccountStatus) {
            http_response_code(403);
            $errorMessage = 'Updating user account statuses requires Administration authorization.';
        } else {
            $targetUserId = filter_input(INPUT_POST, 'target_user_id', FILTER_VALIDATE_INT);
            $newStatus = trim((string)($_POST['account_status'] ?? ''));

            if (!$targetUserId || $targetUserId <= 0) {
                $errorMessage = 'Invalid user selected.';
            } elseif (!in_array($newStatus, $allowedAccountStatuses, true)) {
                $errorMessage = 'Invalid account status selected.';
            } elseif ($targetUserId === $currentUserId) {
                $errorMessage = 'You cannot modify the status of your own account.';
            } else {
                try {
                    $targetStmt = $pdo->prepare("
                        SELECT
                            u.user_id,
                            u.full_name,
                            u.account_status,
                            a.admin_level AS target_admin_level,
                            m.user_id AS target_management_id
                        FROM `USER` u
                        LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
                        LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
                        WHERE u.user_id = ?
                        LIMIT 1
                    ");
                    $targetStmt->execute([$targetUserId]);
                    $targetUser = $targetStmt->fetch();

                    if (!$targetUser) {
                        $errorMessage = 'The selected user account was not found.';
                    } elseif (!$isSuperAdmin && (!empty($targetUser['target_admin_level']) || !empty($targetUser['target_management_id']))) {
                        http_response_code(403);
                        $errorMessage = 'Only a Super Admin can change the status of staff accounts.';
                    } elseif ($targetUser['account_status'] === $newStatus) {
                        $successMessage = 'Account status is already ' . $newStatus . '.';
                    } else {
                        $updateStmt = $pdo->prepare("
                            UPDATE `USER`
                            SET account_status = ?
                            WHERE user_id = ?
                        ");
                        $updateStmt->execute([$newStatus, $targetUserId]);
                        $successMessage = 'Updated status for ' . $targetUser['full_name'] . ' to ' . $newStatus . '.';
                    }
                } catch (PDOException $ex) {
                    $errorMessage = 'Could not update account status. Please try again.';
                }
            }
        }
    } else {
        $errorMessage = 'Unsupported action requested.';
    }
}

$searchTerm = trim((string)($_GET['search'] ?? ''));
$roleFilter = trim((string)($_GET['role'] ?? 'all'));
$statusFilter = trim((string)($_GET['status'] ?? 'all'));
$viewUserId = filter_input(INPUT_GET, 'view', FILTER_VALIDATE_INT);

if (!in_array($roleFilter, $allowedRoleFilters, true)) {
    $roleFilter = 'all';
}

if ($statusFilter !== 'all' && !in_array($statusFilter, $allowedAccountStatuses, true)) {
    $statusFilter = 'all';
}

$whereClauses = [];
$params = [];

if ($searchTerm !== '') {
    $whereClauses[] = '(u.full_name LIKE ? OR u.email LIKE ?)';
    $likeValue = '%' . $searchTerm . '%';
    $params[] = $likeValue;
    $params[] = $likeValue;
}

if ($statusFilter !== 'all') {
    $whereClauses[] = 'u.account_status = ?';
    $params[] = $statusFilter;
}

if ($roleFilter === 'client') {
    $whereClauses[] = 'c.user_id IS NOT NULL';
} elseif ($roleFilter === 'freelancer') {
    $whereClauses[] = 'f.user_id IS NOT NULL';
} elseif ($roleFilter === 'both') {
    $whereClauses[] = 'c.user_id IS NOT NULL AND f.user_id IS NOT NULL';
} elseif ($roleFilter === 'management') {
    $whereClauses[] = 'm.user_id IS NOT NULL';
} elseif ($roleFilter === 'administration') {
    $whereClauses[] = 'a.user_id IS NOT NULL';
}

$sql = "
    SELECT
        u.user_id,
        u.full_name,
        u.email,
        u.account_status,
        u.created_at,
        c.user_id AS client_id,
        f.user_id AS freelancer_id,
        m.user_id AS management_id,
        a.user_id AS admin_id,
        a.admin_level
    FROM `USER` u
    LEFT JOIN CLIENT c ON u.user_id = c.user_id
    LEFT JOIN FREELANCER f ON u.user_id = f.user_id
    LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
    LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
";

if (!empty($whereClauses)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
}

$sql .= ' ORDER BY u.created_at DESC';

$users = [];
try {
    $userStmt = $pdo->prepare($sql);
    $userStmt->execute($params);
    $users = $userStmt->fetchAll();
} catch (PDOException $ex) {
    $errorMessage = 'Unable to load user accounts right now.';
}

$selectedUser = null;
$selectedUserPhones = [];

if ($viewUserId && $viewUserId > 0) {
    try {
        $detailStmt = $pdo->prepare("
            SELECT
                u.user_id,
                u.full_name,
                u.email,
                u.account_status,
                u.created_at,
                c.organization_name,
                c.client_type,
                c.organization_type,
                c.country AS client_country,
                f.professional_title,
                f.experience_years,
                f.hourly_rate,
                f.country AS freelancer_country,
                f.availability_status,
                m.department AS management_department,
                m.designation AS management_designation,
                a.admin_level,
                a.department AS admin_department,
                a.assigned_date
            FROM `USER` u
            LEFT JOIN CLIENT c ON u.user_id = c.user_id
            LEFT JOIN FREELANCER f ON u.user_id = f.user_id
            LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
            LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
            WHERE u.user_id = ?
            LIMIT 1
        ");
        $detailStmt->execute([$viewUserId]);
        $selectedUser = $detailStmt->fetch() ?: null;

        if ($selectedUser) {
            $phoneStmt = $pdo->prepare("
                SELECT phone_number
                FROM USER_PHONE
                WHERE user_id = ?
                ORDER BY phone_number ASC
            ");
            $phoneStmt->execute([$viewUserId]);
            $selectedUserPhones = $phoneStmt->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (PDOException $ex) {
        $errorMessage = 'Unable to load user profile details.';
    }
}

$csrfToken = get_csrf_token();

render_management_header(
    'User Management',
    'Search accounts, inspect role profiles, and enforce account status controls',
    'users',
    $currentStaff,
    $staffRoleLabel
);
?>

<section class="mgmt-banner">
    <div>
        <span class="eyebrow">USER DIRECTORY</span>
        <h2>Registered <span>User Accounts</span></h2>
        <p>Filter accounts by role or status and inspect linked Client, Freelancer, Management, and Administration profiles.</p>
    </div>
    <span class="mgmt-pill"><?= count($users) ?> Matching Accounts</span>
</section>

<?php if ($successMessage !== ''): ?>
    <div class="alert-banner alert-success"><?= e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="alert-banner alert-error"><?= e($errorMessage) ?></div>
<?php endif; ?>

<?php if (!$canManageAccountStatus): ?>
    <div class="alert-banner alert-info">
        Signed in with a Management profile: you can search and inspect all user profiles. Changing account status requires Administration authorization.
    </div>
<?php endif; ?>

<?php if ($selectedUser): ?>
    <section class="mgmt-panel">
        <div class="panel-header">
            <h3>Profile Details: <?= e($selectedUser['full_name']) ?> (#<?= (int)$selectedUser['user_id'] ?>)</h3>
            <a href="users.php" class="btn-outline btn-small">Close Details</a>
        </div>

        <div class="detail-grid">
            <div class="detail-box">
                <h4>Account Summary</h4>
                <p><strong>Email:</strong> <?= e($selectedUser['email']) ?></p>
                <p>
                    <strong>Status:</strong>
                    <span class="badge badge-<?= e($selectedUser['account_status']) ?>">
                        <?= e($selectedUser['account_status']) ?>
                    </span>
                </p>
                <p><strong>Registered:</strong> <?= e(date('d M Y, H:i', strtotime($selectedUser['created_at']))) ?></p>
                <p>
                    <strong>Phone Numbers:</strong>
                    <?= $selectedUserPhones ? e(implode(', ', $selectedUserPhones)) : 'None recorded' ?>
                </p>
            </div>

            <?php if (!empty($selectedUser['client_type'])): ?>
                <div class="detail-box">
                    <h4>Client Profile</h4>
                    <p><strong>Client Type:</strong> <?= e(ucfirst($selectedUser['client_type'])) ?></p>
                    <p><strong>Organization:</strong> <?= e($selectedUser['organization_name'] ?: 'N/A') ?></p>
                    <p><strong>Organization Type:</strong> <?= e($selectedUser['organization_type'] ?: 'N/A') ?></p>
                    <p><strong>Country:</strong> <?= e($selectedUser['client_country']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($selectedUser['professional_title'])): ?>
                <div class="detail-box">
                    <h4>Freelancer Profile</h4>
                    <p><strong>Title:</strong> <?= e($selectedUser['professional_title']) ?></p>
                    <p><strong>Experience:</strong> <?= e($selectedUser['experience_years']) ?> years</p>
                    <p><strong>Hourly Rate:</strong> Rs. <?= number_format((float)$selectedUser['hourly_rate'], 2) ?></p>
                    <p><strong>Availability:</strong> <?= e(ucfirst($selectedUser['availability_status'])) ?></p>
                    <p><strong>Country:</strong> <?= e($selectedUser['freelancer_country']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($selectedUser['management_department'])): ?>
                <div class="detail-box">
                    <h4>Management Profile</h4>
                    <p><strong>Department:</strong> <?= e($selectedUser['management_department']) ?></p>
                    <p><strong>Designation:</strong> <?= e($selectedUser['management_designation']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($selectedUser['admin_level'])): ?>
                <div class="detail-box">
                    <h4>Administration Profile</h4>
                    <p><strong>Admin Level:</strong> <?= e(ucwords(str_replace('_', ' ', $selectedUser['admin_level']))) ?></p>
                    <p><strong>Department:</strong> <?= e($selectedUser['admin_department']) ?></p>
                    <p><strong>Assigned Date:</strong> <?= e(date('d M Y', strtotime($selectedUser['assigned_date']))) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="mgmt-panel">
    <form method="GET" action="users.php" class="filter-bar">
        <div class="filter-field">
            <label for="search">Search Full Name or Email</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Type a name or email address..."
                value="<?= e($searchTerm) ?>"
            >
        </div>

        <div class="filter-field">
            <label for="role">Role / Profile</label>
            <select id="role" name="role">
                <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>All Roles</option>
                <option value="client" <?= $roleFilter === 'client' ? 'selected' : '' ?>>Client</option>
                <option value="freelancer" <?= $roleFilter === 'freelancer' ? 'selected' : '' ?>>Freelancer</option>
                <option value="both" <?= $roleFilter === 'both' ? 'selected' : '' ?>>Client &amp; Freelancer</option>
                <option value="management" <?= $roleFilter === 'management' ? 'selected' : '' ?>>Management</option>
                <option value="administration" <?= $roleFilter === 'administration' ? 'selected' : '' ?>>Administration</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="status">Account Status</label>
            <select id="status" name="status">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>

        <div class="action-group">
            <button type="submit" class="btn-action">Apply Filters</button>
            <a href="users.php" class="btn-outline">Reset</a>
        </div>
    </form>

    <?php if ($users): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Profiles</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <?php
                        $rowUserId = (int)$row['user_id'];
                        $isRowStaff = !empty($row['admin_id']) || !empty($row['management_id']);
                        $canEditRow = $canManageAccountStatus
                            && $rowUserId !== $currentUserId
                            && ($isSuperAdmin || !$isRowStaff);
                        ?>
                        <tr>
                            <td>#<?= $rowUserId ?></td>
                            <td>
                                <strong><?= e($row['full_name']) ?></strong><br>
                                <span class="text-sub"><?= e($row['email']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($row['admin_id'])): ?>
                                    <span class="badge badge-staff">Admin: <?= e(str_replace('_', ' ', $row['admin_level'])) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($row['management_id'])): ?>
                                    <span class="badge badge-staff">Management</span>
                                <?php endif; ?>
                                <?php if (!empty($row['client_id'])): ?>
                                    <span class="badge badge-role">Client</span>
                                <?php endif; ?>
                                <?php if (!empty($row['freelancer_id'])): ?>
                                    <span class="badge badge-role">Freelancer</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= e($row['account_status']) ?>">
                                    <?= e($row['account_status']) ?>
                                </span>
                            </td>
                            <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                            <td>
                                <div class="action-group">
                                    <a href="users.php?view=<?= $rowUserId ?>" class="btn-outline btn-small">View</a>
                                    <?php if ($canEditRow): ?>
                                        <form method="POST" action="users.php" class="inline-form">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="target_user_id" value="<?= $rowUserId ?>">
                                            <select name="account_status" aria-label="Account status">
                                                <?php foreach ($allowedAccountStatuses as $statusOption): ?>
                                                    <option value="<?= e($statusOption) ?>" <?= $row['account_status'] === $statusOption ? 'selected' : '' ?>>
                                                        <?= e(ucfirst($statusOption)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn-action btn-small">Update</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-message">No user accounts match the current filters.</p>
    <?php endif; ?>
</section>

<?php
render_management_footer();
