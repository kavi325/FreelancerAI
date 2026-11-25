
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Proposals | FreelancerAI</title>
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
        <a href="../projects/my_projects.php">My Projects</a>
        <a href="../projects/create.php">Create Project</a>
        <a href="manage.php" class="active">Proposals</a>
        <a href="../assignments/my_assignments.php">Assignments</a>
        <a href="../client/profile.php">My Profile</a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">PROPOSAL MANAGEMENT</span>
            <h1>Freelancer <span>Proposals</span></h1>
            <p>Review freelancer applications and choose the right professional for your projects.</p>
        </div>
    </section>

    <section>
        <div class="section-heading">
            <div>
                <h2>Received Proposals</h2>
                <p>Review the proposals submitted for your projects.</p>
            </div>
        </div>

        <div class="team-table-wrapper">
            <table class="team-table">
                <thead>
                    <tr>
                        <th>Freelancer</th>
                        <th>Project</th>
                        <th>Proposed Amount</th>
                        <th>Estimated Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="6">
                            Received proposals will appear here
                            after database integration.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="institution-section">

        <div class="section-heading">
            <div>
                <h2>Proposal Information</h2>
                <p>Review the details of an individual freelancer proposal.</p>
            </div>
        </div>

        <article class="about-info-card">

            <h3>Freelancer Details</h3>
            <p><strong>Name:</strong> Freelancer name will appear here.</p>
            <p><strong>Skills:</strong> Freelancer skills will appear here.</p>

            <h3>Proposal Details</h3>
            <p><strong>Project:</strong> Project title will appear here.</p>
            <p><strong>Proposed Amount:</strong> To be displayed.</p>
            <p><strong>Estimated Duration:</strong> To be displayed.</p>

            <h3>Cover Letter</h3>
            <p>
                The freelancer's proposal description and cover letter
                will appear here.
            </p>

            <div class="hero-buttons">
                <button type="button" class="btn-primary" disabled>
                    Accept Proposal
                </button>

                <button type="button" class="btn-secondary" disabled>
                    Reject Proposal
                </button>
            </div>

            <p>Proposal actions will be enabled after PHP integration.</p>

        </article>

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
