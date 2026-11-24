
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project | FreelancerAI</title>
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
        <a href="my_projects.php">My Projects</a>
        <a href="create.php" class="active">Create Project</a>
        <a href="../proposals/manage.php">Proposals</a>
        <a href="../assignments/my_assignments.php">Assignments</a>
        <a href="../client/profile.php">My Profile</a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">NEW PROJECT</span>
            <h1>Create Your <span>Project</span></h1>
            <p>Describe your project and find the right AI freelancer for your needs.</p>
        </div>
    </section>

    <section class="institution-section">

        <div class="section-heading">
            <div>
                <h2>Project Information</h2>
                <p>Complete the details below to publish your project.</p>
            </div>
        </div>

        <form action="" method="post">

            <div class="form-group">
                <label for="project_title">Project Title</label>
                <input
                    type="text"
                    id="project_title"
                    name="project_title"
                    placeholder="Enter a clear project title"
                    maxlength="200"
                    required
                >
            </div>

            <div class="form-group">
                <label for="project_description">Project Description</label>
                <textarea
                    id="project_description"
                    name="project_description"
                    rows="6"
                    placeholder="Describe your project, objectives and expected outcomes..."
                    required
                ></textarea>
            </div>

            <div class="form-group">
                <label for="budget_min">Minimum Budget</label>
                <input
                    type="number"
                    id="budget_min"
                    name="budget_min"
                    min="0"
                    step="0.01"
                    placeholder="Enter minimum budget"
                    required
                >
            </div>

            <div class="form-group">
                <label for="budget_max">Maximum Budget</label>
                <input
                    type="number"
                    id="budget_max"
                    name="budget_max"
                    min="0"
                    step="0.01"
                    placeholder="Enter maximum budget"
                    required
                >
            </div>

            <div class="form-group">
                <label for="deadline">Project Deadline</label>
                <input
                    type="date"
                    id="deadline"
                    name="deadline"
                    required
                >
            </div>

            <div class="form-group">
                <label for="required_skills">Required AI Skills</label>
                <select
                    id="required_skills"
                    name="required_skills[]"
                    multiple
                    required
                >
                    <option value="Machine Learning">Machine Learning</option>
                    <option value="Deep Learning">Deep Learning</option>
                    <option value="Natural Language Processing">Natural Language Processing</option>
                    <option value="Computer Vision">Computer Vision</option>
                    <option value="Generative AI">Generative AI</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Robotics">Robotics</option>
                </select>
                <p>Select the skills required for your project.</p>
            </div>

            <div class="form-group">
                <label for="additional_requirements">Additional Requirements</label>
                <textarea
                    id="additional_requirements"
                    name="additional_requirements"
                    rows="4"
                    placeholder="Enter any additional instructions or expectations..."
                ></textarea>
            </div>

            <div class="hero-buttons">
                <button type="submit" class="btn-primary">Publish Project</button>
                <a href="my_projects.php" class="btn-secondary">Cancel</a>
            </div>

        </form>

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
