
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Registration | FreelancerAI</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-page">

    <div class="auth-brand">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <p>Showcase your skills and discover opportunities</p>
    </div>

    <div class="auth-card registration-card">

        <div class="auth-heading">
            <span class="form-label-top">FREELANCER REGISTRATION</span>
            <h1>Create Your Account</h1>
            <p>Build your professional profile and start finding projects.</p>
        </div>

        <form action="process_register.php" method="POST">

            <input type="hidden" name="role" value="freelancer">

            <!-- Basic Information -->

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name"
                       placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email"
                       placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone"
                       placeholder="Enter your phone number">
            </div>

            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country"
                       placeholder="Enter your country" required>
            </div>

            <!-- Professional Information -->

            <div class="form-section">
                <h2>Professional Profile</h2>
                <p>Tell clients about your professional expertise.</p>
            </div>

            <div class="form-group">
                <label>Professional Title</label>
                <input type="text" name="professional_title"
                       placeholder="e.g. AI Engineer, Machine Learning Developer"
                       required>
            </div>

            <div class="form-group">
                <label>Biography</label>
                <textarea name="biography" rows="4"
                          placeholder="Tell clients about your experience and expertise"
                          required></textarea>
            </div>

            <div class="form-group">
                <label>Years of Experience</label>
                <input type="number" name="experience_years"
                       min="0" max="999.9" step="0.5"
                       placeholder="e.g. 2" required>
            </div>

            <div class="form-group">
                <label>Hourly Rate (USD)</label>
                <input type="number" name="hourly_rate"
                       min="0" step="0.01"
                       placeholder="Enter your hourly rate" required>
            </div>

            <div class="form-group">
                <label>Availability Status</label>
                <select name="availability_status" required>
                    <option value="available">Available</option>
                    <option value="busy">Busy</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>

            <!-- Account Security -->

            <div class="form-section">
                <h2>Account Security</h2>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Create a password"
                       minlength="8" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                       placeholder="Confirm your password"
                       minlength="8" required>
            </div>

            <button type="submit" class="auth-button">
                Create Freelancer Account
            </button>

        </form>

        <div class="auth-switch">
            Already registered? <a href="login.php">Login</a>
        </div>

    </div>

    <a href="register.php" class="back-home">
        ← Back to Registration Options
    </a>

</div>

</body>
</html>
