
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/database.php";

$userId = $_SESSION['user_id'];

$userQuery = $pdo->prepare("
    SELECT u.full_name, u.email,
           f.professional_title, f.country,
           f.hourly_rate, f.availability_status,
           f.biography
    FROM `USER` u
    LEFT JOIN FREELANCER f ON u.user_id = f.user_id
    WHERE u.user_id = ?
");

$userQuery->execute([$userId]);
$user = $userQuery->fetch();

if (!$user) {
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}

$proposalQuery = $pdo->prepare("
    SELECT COUNT(*) FROM PROPOSAL WHERE user_id = ?
");
$proposalQuery->execute([$userId]);
$totalProposals = $proposalQuery->fetchColumn();

$pendingQuery = $pdo->prepare("
    SELECT COUNT(*) FROM PROPOSAL
    WHERE user_id = ? AND proposal_status = 'pending'
");
$pendingQuery->execute([$userId]);
$pendingProposals = $pendingQuery->fetchColumn();

$assignmentQuery = $pdo->prepare("
    SELECT COUNT(*)
    FROM ASSIGNMENT a
    INNER JOIN PROPOSAL p ON a.proposal_id = p.proposal_id
    WHERE p.user_id = ?
");
$assignmentQuery->execute([$userId]);
$totalAssignments = $assignmentQuery->fetchColumn();

$activeQuery = $pdo->prepare("
    SELECT COUNT(*)
    FROM ASSIGNMENT a
    INNER JOIN PROPOSAL p ON a.proposal_id = p.proposal_id
    WHERE p.user_id = ?
    AND a.assignment_status IN ('active', 'in_progress')
");
$activeQuery->execute([$userId]);
$activeAssignments = $activeQuery->fetchColumn();

$skillQuery = $pdo->prepare("
    SELECT COUNT(*) FROM FREELANCER_SKILL WHERE user_id = ?
");
$skillQuery->execute([$userId]);
$totalSkills = $skillQuery->fetchColumn();

$recentProposalQuery = $pdo->prepare("
    SELECT p.proposal_id, p.proposed_amount,
           p.proposal_status, p.submitted_at,
           pr.project_title
    FROM PROPOSAL p
    INNER JOIN PROJECT pr ON p.project_id = pr.project_id
    WHERE p.user_id = ?
    ORDER BY p.submitted_at DESC
    LIMIT 5
");
$recentProposalQuery->execute([$userId]);
$recentProposals = $recentProposalQuery->fetchAll();

$projectQuery = $pdo->query("
    SELECT project_id, project_title, project_description,
           budget_min, budget_max, deadline
    FROM PROJECT
    WHERE project_status = 'open'
    ORDER BY created_at DESC
    LIMIT 5
");
$availableProjects = $projectQuery->fetchAll();

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$firstName = explode(' ', trim($user['full_name']))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Dashboard | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .freelancer-dashboard {
            background: #F4F8FB;
            min-height: 75vh;
            padding: 35px 0 60px;
        }

        .dashboard-welcome {
            background: linear-gradient(120deg, #CAF0F8, #ADE8F4);
            padding: 35px;
            margin-bottom: 30px;
            border: 1px solid #ADE8F4;
        }

        .dashboard-welcome .eyebrow {
            color: #0077B6;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .dashboard-welcome h1 {
            color: #03045E;
            font-size: 32px;
            margin: 12px 0;
        }

        .dashboard-welcome h1 span {
            color: #0077B6;
        }

        .dashboard-welcome p {
            color: #526B82;
            margin: 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #FFFFFF;
            border: 1px solid #DCE7EF;
            padding: 24px;
        }

        .stat-card p {
            color: #61758A;
            font-size: 14px;
            margin: 0 0 12px;
        }

        .stat-card h2 {
            color: #03045E;
            font-size: 30px;
            margin: 0;
        }

        .dashboard-columns {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
            align-items: start;
        }

        .dashboard-panel {
            background: #FFFFFF;
            border: 1px solid #DCE7EF;
            padding: 25px;
            margin-bottom: 25px;
            min-width: 0;
        }

        .panel-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .panel-heading h2 {
            color: #03045E;
            font-size: 20px;
            margin: 0;
        }

        .panel-heading a {
            color: #0077B6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .proposal-item,
        .project-item {
            padding: 17px 0;
            border-bottom: 1px solid #E7EEF4;
        }

        .proposal-item:last-child,
        .project-item:last-child {
            border-bottom: none;
        }

        .proposal-item h3,
        .project-item h3 {
            color: #023E8A;
            font-size: 16px;
            margin: 0 0 9px;
        }

        .proposal-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #718096;
            font-size: 13px;
        }

        .status {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            background: #E8F4FA;
            color: #0077B6;
        }

        .status.pending {
            background: #FFF4D6;
            color: #946200;
        }

        .status.accepted,
        .status.completed {
            background: #DDF7E8;
            color: #187344;
        }

        .status.rejected,
        .status.cancelled {
            background: #FCE4E4;
            color: #B42318;
        }

        .project-item p {
            color: #718096;
            font-size: 13px;
            line-height: 1.6;
            margin: 8px 0;
        }

        .project-budget {
            color: #0077B6;
            font-size: 14px;
            font-weight: 700;
        }

        .profile-summary {
            background: #FFFFFF;
            border: 1px solid #DCE7EF;
            padding: 25px;
        }

        .profile-summary h2 {
            color: #03045E;
            font-size: 20px;
            margin: 0 0 18px;
        }

        .profile-summary h3 {
            color: #023E8A;
            margin: 0 0 7px;
        }

        .profile-summary p {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        .profile-link {
            display: inline-block;
            margin-top: 12px;
            padding: 11px 18px;
            background: #0077B6;
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .quick-links a {
            display: block;
            padding: 14px;
            border: 1px solid #DCE7EF;
            color: #023E8A;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .quick-links a:hover {
            background: #CAF0F8;
            border-color: #48CAE4;
        }

        .empty-state {
            color: #718096;
            text-align: center;
            padding: 25px 10px;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-welcome {
                padding: 25px;
            }

            .dashboard-welcome h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div class="container header-content">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <a href="profile.php" class="account-link">My Account</a>
    </div>
</header>

<nav class="navigation" aria-label="Freelancer navigation">
    <div class="container nav-content">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="profile.php">My Profile</a>
        <a href="skills.php">My Skills</a>
        <a href="../projects/browse.php">Browse Projects</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</nav>

<main class="freelancer-dashboard">
    <div class="container">

        <section class="dashboard-welcome">
            <span class="eyebrow">FREELANCER DASHBOARD</span>
            <h1>Welcome back, <span><?= e($firstName) ?>!</span></h1>
            <p>Manage your professional profile, explore opportunities and track your freelance journey.</p>
        </section>

        <section class="dashboard-grid">

            <div class="stat-card">
                <p>Total Proposals</p>
                <h2><?= (int)$totalProposals ?></h2>
            </div>

            <div class="stat-card">
                <p>Pending Proposals</p>
                <h2><?= (int)$pendingProposals ?></h2>
            </div>

            <div class="stat-card">
                <p>Total Assignments</p>
                <h2><?= (int)$totalAssignments ?></h2>
            </div>

            <div class="stat-card">
                <p>Active Assignments</p>
                <h2><?= (int)$activeAssignments ?></h2>
            </div>

        </section>

        <section class="dashboard-columns">

            <div>

                <div class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Recent Proposals</h2>
                    </div>

                    <?php if ($recentProposals): ?>

                        <?php foreach ($recentProposals as $proposal): ?>

                            <div class="proposal-item">
                                <h3><?= e($proposal['project_title']) ?></h3>

                                <div class="proposal-meta">
                                    <span>Proposal #<?= (int)$proposal['proposal_id'] ?></span>
                                    <span>Rs. <?= number_format((float)$proposal['proposed_amount'], 2) ?></span>
                                    <span><?= e(date('d M Y', strtotime($proposal['submitted_at']))) ?></span>
                                    <span class="status <?= e(strtolower($proposal['proposal_status'])) ?>">
                                        <?= e($proposal['proposal_status']) ?>
                                    </span>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-state">
                            You haven't submitted any proposals yet.
                        </div>

                    <?php endif; ?>
                </div>

                <div class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Latest Projects</h2>
                        <a href="../projects/browse.php">Browse Projects</a>
                    </div>

                    <?php if ($availableProjects): ?>

                        <?php foreach ($availableProjects as $project): ?>

                            <div class="project-item">
                                <h3><?= e($project['project_title']) ?></h3>

                                <p>
                                    <?= e(mb_strimwidth($project['project_description'], 0, 130, '...')) ?>
                                </p>

                                <div class="proposal-meta">
                                    <span class="project-budget">
                                        Rs. <?= number_format((float)$project['budget_min'], 2) ?>
                                        - Rs. <?= number_format((float)$project['budget_max'], 2) ?>
                                    </span>

                                    <span>
                                        Deadline: <?= e(date('d M Y', strtotime($project['deadline']))) ?>
                                    </span>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-state">
                            No open projects are currently available.
                        </div>

                    <?php endif; ?>
                </div>

            </div>

            <div>

                <div class="profile-summary">
                    <h2>Professional Profile</h2>

                    <h3><?= e($user['professional_title'] ?: 'Add your professional title') ?></h3>

                    <p><?= e($user['country'] ?: 'Country not specified') ?></p>

                    <p>
                        Hourly Rate:
                        <strong>
                            Rs. <?= number_format((float)($user['hourly_rate'] ?? 0), 2) ?>
                        </strong>
                    </p>

                    <p>
                        Availability:
                        <span class="status">
                            <?= e($user['availability_status'] ?: 'Not specified') ?>
                        </span>
                    </p>

                    <p>
                        Skills added:
                        <strong><?= (int)$totalSkills ?></strong>
                    </p>

                    <a href="profile.php" class="profile-link">Edit My Profile</a>
                </div>

                <div class="dashboard-panel" style="margin-top: 25px;">
                    <div class="panel-heading">
                        <h2>Quick Actions</h2>
                    </div>

                    <div class="quick-links">
                        <a href="../projects/browse.php">Explore Available Projects</a>
                        <a href="skills.php">Manage My AI Skills</a>
                        <a href="profile.php">Update Professional Profile</a>
                    </div>
                </div>

            </div>

        </section>

    </div>
</main>

<footer>
    <div class="container footer-content">
        <p>&copy; 2026 FreelancerAI. All rights reserved.</p>
        <p>Your Skills. Your Intelligence. Your Opportunities.</p>
    </div>
</footer>

</body>
</html>
