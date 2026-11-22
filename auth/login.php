
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-brand">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <p>Your Skills. Your Intelligence. Your Opportunities.</p>
    </div>

    <div class="auth-card">
        <div class="auth-heading">
            <h1>Welcome Back</h1>
            <p>Sign in to continue to your FreelancerAI account.</p>
        </div>

        <form action="#" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-options">
                <label class="remember-option">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="auth-button">Login to Account</button>
        </form>

        <div class="auth-divider"><span>New to FreelancerAI?</span></div>

        <a href="register.php" class="auth-outline-button">Create an Account</a>
    </div>

    <a href="../index.php" class="back-home">← Back to Homepage</a>
</div>

</body>
</html>
