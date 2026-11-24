
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
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <a href="profile.php" class="account-link">My Account</a>
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
    </div>
</nav>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">CLIENT WORKSPACE</span>
            <h1>Welcome to Your <span>Dashboard!</span></h1>
            <p>
                Manage your AI projects, connect with talented freelancers,
                review proposals and monitor your assignments.
            </p>
            <div class="hero-buttons">
                <a href="../projects/create.php" class="btn-primary">
                    + Create New Project
                </a>
                <a href="../projects/my_projects.php" class="btn-secondary">
                    View My Projects
                </a>
            </div>
        </div>

        <div class="hero-side">
            <div class="hero-icon">AI</div>
            <h3>Your Projects, Simplified</h3>
            <p>Manage your freelance projects in one convenient workspace.</p>
        </div>
    </section>

    <section class="dashboard-overview">
        <div class="section-heading">
            <div>
                <h2>Dashboard Overview</h2>
                <p>A summary of your project activities.</p>
            </div>
        </div>

        <div class="service-grid">

            <article class="service-card">
                <div class="service-icon blue">P</div>
                <h3>Total Projects</h3>
                <p>Number of projects you have created.</p>
                <strong aria-label="Total projects: awaiting database integration">—</strong>
            </article>

            <article class="service-card">
                <div class="service-icon orange">O</div>
                <h3>Open Projects</h3>
                <p>Projects currently accepting proposals.</p>
                <strong aria-label="Open projects: awaiting database integration">—</strong>
            </article>

            <article class="service-card">
                <div class="service-icon purple">P</div>
                <h3>Proposals Received</h3>
                <p>Proposals submitted by freelancers.</p>
                <strong aria-label="Proposals received: awaiting database integration">—</strong>
            </article>

            <article class="service-card">
                <div class="service-icon green">A</div>
                <h3>Active Assignments</h3>
                <p>Projects currently being completed.</p>
                <strong aria-label="Active assignments: awaiting database integration">—</strong>
            </article>

        </div>
    </section>

    <section class="dashboard-projects">
        <div class="section-heading">
            <div>
                <h2>Recent Projects</h2>
                <p>View the latest projects you have posted.</p>
            </div>
            <a href="../projects/my_projects.php">View All Projects →</a>
        </div>

        <div class="team-table-wrapper">
            <table class="team-table">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Status</th>
                        <th>Budget</th>
                        <th>Deadline</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">
                            Your recent projects will appear here
                            after database integration.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <div class="section-heading">
            <div>
                <h2>Quick Access</h2>
                <p>Manage your client activities.</p>
            </div>
        </div>

        <div class="service-grid">

            <article class="service-card">
                <h3>My Projects</h3>
                <p>View and manage your published projects.</p>
                <a href="../projects/my_projects.php">Manage Projects →</a>
            </article>

            <article class="service-card">
                <h3>Create Project</h3>
                <p>Publish a new project and find suitable freelancers.</p>
                <a href="../projects/create.php">Create Project →</a>
            </article>

            <article class="service-card">
                <h3>Review Proposals</h3>
                <p>Explore proposals received from freelancers.</p>
                <a href="../proposals/manage.php">View Proposals →</a>
            </article>

            <article class="service-card">
                <h3>Assignments</h3>
                <p>Track the progress of your accepted projects.</p>
                <a href="../assignments/my_assignments.php">View Assignments →</a>
            </article>

        </div>
    </section>

</main>

<footer>
    <div class="container footer-content">
        <p>&copy; 2026 FreelancerAI. All rights reserved.</p>
        <p>Your Skills. Your Intelligence. Your Opportunities.</p>
    </div>
</footer>

</body>
</html>
