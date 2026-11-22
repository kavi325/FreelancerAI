
<?php
$registerBoth = isset($_GET['role']) && $_GET['role'] === 'both';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Registration | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-brand">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <p>Create your client account</p>
    </div>

    <div class="auth-card registration-card">
        <div class="auth-heading">
            <span class="form-label-top">CLIENT REGISTRATION</span>
            <h1>Create Your Account</h1>
            <p>Start finding the right AI professionals for your projects.</p>
        </div>

        <form action="#" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="Enter your phone number">
            </div>

            <div class="form-group">
                <label>Client Type</label>
                <select name="client_type" required>
                    <option value="">Select client type</option>
                    <option value="individual">Individual</option>
                    <option value="organization">Organization</option>
                </select>
            </div>

            <div class="form-group">
                <label>Organization Name <span class="optional">(Optional)</span></label>
                <input type="text" name="organization_name" placeholder="Enter organization name">
            </div>

            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" placeholder="Enter your country" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <input type="hidden" name="register_both" value="<?php echo $registerBoth ? '1' : '0'; ?>">

            <button type="submit" class="auth-button">Create Client Account</button>
        </form>

        <div class="auth-switch">
            Already registered? <a href="login.php">Login</a>
        </div>
    </div>

    <a href="register.php" class="back-home">← Back to Registration Options</a>
</div>

</body>
</html>
