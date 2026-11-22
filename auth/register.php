
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-page register-page">
    <div class="auth-brand">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <p>Find opportunities. Share expertise. Build together.</p>
    </div>

    <div class="register-container">
        <div class="auth-heading">
            <h1>Join FreelancerAI</h1>
            <p>Choose how you would like to use our platform.</p>
        </div>

        <div class="role-options">
            <a href="register_client.php" class="role-card">
                <div class="role-icon">C</div>
                <h2>I'm a Client</h2>
                <p>I want to publish projects, find AI professionals and hire freelancers.</p>
                <span>Register as Client →</span>
            </a>

            <a href="register_freelancer.php" class="role-card">
                <div class="role-icon">F</div>
                <h2>I'm a Freelancer</h2>
                <p>I want to showcase my AI skills, discover projects and earn through my work.</p>
                <span>Register as Freelancer →</span>
            </a>

            <a href="register_client.php?role=both" class="role-card both-role">
                <div class="role-icon">B</div>
                <h2>Both</h2>
                <p>I want to hire other professionals and offer my own services.</p>
                <span>Register with Both Roles →</span>
            </a>
        </div>

        <div class="auth-switch">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

    <a href="../index.php" class="back-home">← Back to Homepage</a>
</div>

</body>
</html>
