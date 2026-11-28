<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function get_csrf_token(): string
{
    if (empty($_SESSION['management_csrf_token'])) {
        $_SESSION['management_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['management_csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    if (empty($token) || empty($_SESSION['management_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['management_csrf_token'], $token);
}

function render_access_denied(string $message): void
{
    http_response_code(403);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/management.css">
</head>
<body>
<div class="access-denied-wrap">
    <div class="access-denied-card">
        <h1>Access Restricted</h1>
        <p><?= e($message) ?></p>
        <div class="action-group" style="justify-content: center;">
            <a href="../auth/login.php" class="btn-action">Go to Login</a>
            <a href="../index.php" class="btn-outline">Return to Homepage</a>
        </div>
    </div>
</div>
</body>
</html>
    <?php
    exit();
}

if (empty($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$currentUserId = (int)$_SESSION['user_id'];

try {
    $staffStmt = $pdo->prepare("
        SELECT
            u.user_id,
            u.full_name,
            u.email,
            u.account_status,
            m.department AS management_department,
            m.designation AS management_designation,
            a.admin_level,
            a.department AS admin_department,
            a.assigned_date AS admin_assigned_date
        FROM `USER` u
        LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
        LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $staffStmt->execute([$currentUserId]);
    $currentStaff = $staffStmt->fetch();
} catch (PDOException $ex) {
    render_access_denied('Unable to verify staff permissions right now. Please try again later.');
}

if (!$currentStaff || $currentStaff['account_status'] !== 'active') {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit();
}

$hasAdminProfile = !empty($currentStaff['admin_level']);
$hasManagementProfile = !empty($currentStaff['management_department']);

if (!$hasAdminProfile && !$hasManagementProfile) {
    render_access_denied('This area is restricted to authorized Management and Administration staff.');
}

$adminLevel = $hasAdminProfile ? (string)$currentStaff['admin_level'] : '';
$isSuperAdmin = $adminLevel === 'super_admin';
$isModerator = $adminLevel === 'moderator';
$canManageAccountStatus = $isSuperAdmin || $isModerator;
$canDeleteCatalogItems = $isSuperAdmin || $isModerator;

$staffRoleLabel = $hasAdminProfile
    ? 'Administration · ' . ucwords(str_replace('_', ' ', $adminLevel))
    : 'Management · ' . $currentStaff['management_designation'];
