
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container header-content">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <a href="../client/profile.php" class="account-link">My Account</a>
    </div>
</header>

<nav class="navigation" aria-label="Client navigation">
    <div class="container nav-content">
        <a href="../client/dashboard.php">Dashboard</a>
        <a href="my_projects.php" class="active">My Projects</a>
        <a href="create.php">Create Project</a>
        <a href="../proposals/manage.php">Proposals</a>
        <a href="../assignments/my_assignments.php">Assignments</a>
        <a href="../client/profile.php">My Profile</a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">PROJECT MANAGEMENT</span>
            <h1>My <span>Projects</span></h1>
            <p>View, organise and monitor all the projects you have posted.</p>

            <div class="hero-buttons">
                <a href="create.php" class="btn-primary">+ Create New Project</a>
            </div>
        </div>
    </section>

    <section>
        <div class="section-heading">
            <div>
                <h2>All Projects</h2>
                <p>Overview of your published projects and their progress.</p>
            </div>
        </div>

        <div class="team-table-wrapper">
            <table class="team-table">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Budget Range</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="6">
                            Project records will be displayed here
                            after database integration.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="bottom-banner">
        <div>
            <h2>Have a New Idea?</h2>
            <p>Publish another project and connect with AI professionals.</p>
        </div>
        <a href="create.php" class="btn-primary">Post a Project</a>
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
