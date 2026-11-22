
<?php
$pageTitle = "FreelancerAI | Find AI Talent";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">THE AI FREELANCING MARKETPLACE</span>
            <h1>Find the right talent.<br><span>Build something smarter.</span></h1>
            <p>Connect with skilled AI professionals and turn your ideas into real-world solutions.</p>
            <div class="hero-buttons">
                <a href="projects/browse.php" class="btn-primary">Explore Projects</a>
                <a href="auth/register.php" class="btn-secondary">Join FreelancerAI</a>
            </div>
        </div>
        <div class="hero-side">
            <div class="hero-icon">AI</div>
            <h3>Ideas meet intelligence.</h3>
            <p>A marketplace built for the next generation of digital work.</p>
        </div>
    </section>

    <section class="marketplace">

        <aside class="sidebar">
            <h3>Explore Categories</h3>
            <a href="#">Machine Learning</a>
            <a href="#">AI Automation</a>
            <a href="#">Generative AI</a>
            <a href="#">Natural Language Processing</a>
            <a href="#">Computer Vision</a>
            <a href="#">AI Chatbots</a>

            <div class="sidebar-box">
                <h3>Are you an AI freelancer?</h3>
                <p>Showcase your skills and find new opportunities.</p>
                <a href="auth/register.php">Create an account →</a>
            </div>
        </aside>

        <div class="main-content">
            <div class="section-heading">
                <div>
                    <h2>Explore AI Services</h2>
                    <p>Discover what talented professionals can build.</p>
                </div>
                <a href="projects/browse.php">View all →</a>
            </div>

            <div class="service-grid">

                <article class="service-card">
                    <div class="service-icon blue">ML</div>
                    <h3>Machine Learning</h3>
                    <p>Intelligent models, predictions and data-driven solutions.</p>
                    <a href="projects/browse.php">Explore services →</a>
                </article>

                <article class="service-card">
                    <div class="service-icon orange">AI</div>
                    <h3>AI Automation</h3>
                    <p>Automate repetitive tasks and improve business workflows.</p>
                    <a href="projects/browse.php">Explore services →</a>
                </article>

                <article class="service-card">
                    <div class="service-icon purple">GPT</div>
                    <h3>Generative AI</h3>
                    <p>Build intelligent content and generative AI applications.</p>
                    <a href="projects/browse.php">Explore services →</a>
                </article>

                <article class="service-card">
                    <div class="service-icon green">CV</div>
                    <h3>Computer Vision</h3>
                    <p>Image recognition, object detection and visual intelligence.</p>
                    <a href="projects/browse.php">Explore services →</a>
                </article>

            </div>

            <section class="bottom-banner">
                <div>
                    <h2>Have an idea for an AI project?</h2>
                    <p>Find professionals who can bring your project to life.</p>
                </div>
                <a href="auth/register.php" class="btn-primary">Get Started</a>
            </section>
        </div>

    </section>

</main>

<footer>
    <div class="container footer-content">
        <p>© <?php echo date("Y"); ?> FreelancerAI. All rights reserved.</p>
        <p>Your Skills. Your Intelligence. Your Opportunities.</p>
    </div>
</footer>

</body>
</html>
