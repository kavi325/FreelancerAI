
<?php
$clientName = $clientName ?? 'Client';
$totalProjects = $totalProjects ?? '—';
$openProjects = $openProjects ?? '—';
$proposalsReceived = $proposalsReceived ?? '—';
$activeAssignments = $activeAssignments ?? '—';
$recentProjects = $recentProjects ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container header-content">
        <a href="../index.php" class="logo">
            Freelancer<span>AI</span>
        </a>

        <a href="profile.php" class="account-link">
            My Account
        </a>
    </div>
</header>

<nav class="navigation" aria-label="Client navigation">
    <div class="container nav-content">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="../projects/my_projects.php">My Projects</a>
        <a href="../projects/create.php">Create Project</a>
        <a href="../proposals/manage.php">Proposals</a>
        <a href="../assignments/my_assignments.php">Assignments</a>
        <a href="profile.php">My Profile</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</nav>

<main class="container client-dashboard">

    <section class="dashboard-welcome">
        <div>
            <span class="eyebrow">CLIENT WORKSPACE</span>
            <h1>
                Welcome, <?= htmlspecialchars((string)$clientName, ENT_QUOTES, 'UTF-8'); ?>!
            </h1>
            <p>
                Manage your AI projects, review freelancer proposals
                and keep track of your assignments.
            </p>
        </div>

        <a href="../projects/create.php" class="btn-primary">
            + Create New Project
        </a>
    </section>

    <section class="dashboard-overview" aria-labelledby="overview-heading">

        <div class="section-heading">
            <div>
                <h2 id="overview-heading">Dashboard Overview</h2>
                <p>A summary of your project activity.</p>
            </div>
        </div>

        <div class="dashboard-stats">

            <article class="stat-card">
                <span class="stat-label">Total Projects</span>
                <h3>
                    <?= htmlspecialchars((string)$totalProjects, ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <p>Projects you have created</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Open Projects</span>
                <h3>
                    <?= htmlspecialchars((string)$openProjects, ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <p>Projects accepting proposals</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Proposals Received</span>
                <h3>
                    <?= htmlspecialchars((string)$proposalsReceived, ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <p>Proposals from freelancers</p>
            </article>

            <article class="stat-card">
                <span class="stat-label">Active Assignments</span>
                <h3>
                    <?= htmlspecialchars((string)$activeAssignments, ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <p>Currently ongoing projects</p>
            </article>

        </div>
    </section>

    <section class="dashboard-projects" aria-labelledby="recent-projects-heading">

        <div class="section-heading">
            <div>
                <h2 id="recent-projects-heading">Recent Projects</h2>
                <p>Keep track of the projects you have posted.</p>
            </div>

            <a href="../projects/my_projects.php">View All Projects →</a>
        </div>

        <?php if (!empty($recentProjects)): ?>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th scope="col">Project Title</th>
                            <th scope="col">Status</th>
                            <th scope="col">Budget Range</th>
                            <th scope="col">Deadline</th>
                            <th scope="col">Created Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($recentProjects as $project): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars((string)$project['project_title'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <span class="status-badge">
                                        <?= htmlspecialchars((string)$project['project_status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>

                                <td>
                                    <?= number_format((float)$project['budget_min'], 2); ?>
                                    -
                                    <?= number_format((float)$project['budget_max'], 2); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)($project['deadline'] ?? 'Not specified'), ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)$project['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <a href="../projects/details.php?project_id=<?= urlencode((string)$project['project_id']); ?>">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No Projects Yet</h3>
                <p>
                    You haven't created any projects yet.
                    Start by publishing your first AI project.
                </p>
                <a href="../projects/create.php" class="btn-primary">
                    Create Your First Project
                </a>
            </div>

        <?php endif; ?>

    </section>

    <section class="dashboard-quick-links" aria-labelledby="quick-links-heading">

        <div class="section-heading">
            <div>
                <h2 id="quick-links-heading">Manage Your Work</h2>
                <p>Quick access to your client activities.</p>
            </div>
        </div>

        <div class="service-grid">

            <article class="service-card">
                <div class="service-icon blue">P</div>
                <h3>My Projects</h3>
                <p>View and manage all the projects you have published.</p>
                <a href="../projects/my_projects.php">Manage Projects →</a>
            </article>

            <article class="service-card">
                <div class="service-icon orange">+</div>
                <h3>Create a Project</h3>
                <p>Publish a new AI project and find suitable professionals.</p>
                <a href="../projects/create.php">Create Project →</a>
            </article>

            <article class="service-card">
                <div class="service-icon purple">R</div>
                <h3>Review Proposals</h3>
                <p>Explore proposals submitted by interested freelancers.</p>
                <a href="../proposals/manage.php">View Proposals →</a>
            </article>

            <article class="service-card">
                <div class="service-icon green">A</div>
                <h3>Assignments</h3>
                <p>Monitor the progress of your accepted projects.</p>
                <a href="../assignments/my_assignments.php">View Assignments →</a>
            </article>

        </div>
    </section>

</main>

<footer>
    <div class="container footer-content">
        <p>&copy; <?= date('Y'); ?> FreelancerAI. All rights reserved.</p>
        <p>Your Skills. Your Intelligence. Your Opportunities.</p>
    </div>
</footer>

</body>
</html>
