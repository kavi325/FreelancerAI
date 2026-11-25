
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $country = trim($_POST["country"] ?? "");
    $title = trim($_POST["professional_title"] ?? "");
    $biography = trim($_POST["biography"] ?? "");
    $experience = $_POST["experience_years"] ?? 0;
    $rate = $_POST["hourly_rate"] ?? 0;
    $availability = $_POST["availability_status"] ?? "available";

    if (
        $fullName === "" ||
        !is_numeric($experience) ||
        !is_numeric($rate) ||
        !in_array($availability, ["available", "busy", "unavailable"], true)
    ) {
        $message = "Please enter valid profile information.";
    } else {
        try {
            $pdo->beginTransaction();

            $userStmt = $pdo->prepare(
                "UPDATE USER SET full_name = ? WHERE user_id = ?"
            );
            $userStmt->execute([$fullName, $userId]);

            $profileStmt = $pdo->prepare(
                "UPDATE FREELANCER
                 SET professional_title = ?,
                     biography = ?,
                     experience_years = ?,
                     hourly_rate = ?,
                     country = ?,
                     availability_status = ?
                 WHERE user_id = ?"
            );

            $profileStmt->execute([
                $title,
                $biography,
                $experience,
                $rate,
                $country,
                $availability,
                $userId
            ]);

            $pdo->commit();
            $message = "Profile updated successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Unable to update your profile.";
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT u.full_name, u.email, f.professional_title,
            f.biography, f.experience_years, f.hourly_rate,
            f.country, f.availability_status
     FROM USER u
     INNER JOIN FREELANCER f ON u.user_id = f.user_id
     WHERE u.user_id = ?"
);

$stmt->execute([$userId]);
$profile = $stmt->fetch();

if (!$profile) {
    exit("Freelancer profile not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container header-content">
        <a href="../index.php" class="logo">Freelancer<span>AI</span></a>
        <a href="profile.php" class="account-link">My Account</a>
    </div>
</header>

<nav class="navigation">
    <div class="container nav-content">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php" class="active">My Profile</a>
        <a href="skills.php">My Skills</a>
        <a href="../projects/browse.php">Browse Projects</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</nav>

<main class="container">

    <section class="hero">
        <div>
            <span class="eyebrow">FREELANCER PROFILE</span>
            <h1>Manage Your <span>Profile</span></h1>
            <p>Show clients your professional experience, skills and expertise.</p>
        </div>
    </section>

    <section class="institution-section">

        <div class="section-heading">
            <div>
                <h2>Personal Information</h2>
                <p>Keep your professional information up to date.</p>
            </div>
        </div>

        <?php if ($message !== ""): ?>
            <p class="profile-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="profile.php">

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name"
                    value="<?= htmlspecialchars($profile["full_name"]) ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email"
                    value="<?= htmlspecialchars($profile["email"]) ?>"
                    disabled>
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country"
                    value="<?= htmlspecialchars($profile["country"] ?? "") ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="professional_title">Professional Title</label>
                <input type="text" id="professional_title"
                    name="professional_title"
                    value="<?= htmlspecialchars($profile["professional_title"] ?? "") ?>"
                    placeholder="e.g. Machine Learning Engineer"
                    required>
            </div>

            <div class="form-group">
                <label for="biography">Professional Biography</label>
                <textarea id="biography" name="biography"
                    rows="6"
                    placeholder="Tell clients about your experience and expertise..."
                    required><?= htmlspecialchars($profile["biography"] ?? "") ?></textarea>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience</label>
                <input type="number" id="experience_years"
                    name="experience_years"
                    min="0" max="999.9" step="0.1"
                    value="<?= htmlspecialchars($profile["experience_years"] ?? "0") ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="hourly_rate">Hourly Rate (Rs.)</label>
                <input type="number" id="hourly_rate"
                    name="hourly_rate"
                    min="0" step="0.01"
                    value="<?= htmlspecialchars($profile["hourly_rate"] ?? "0") ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="availability_status">Availability</label>
                <select id="availability_status"
                    name="availability_status" required>
                    <option value="available"
                        <?= $profile["availability_status"] === "available" ? "selected" : "" ?>>
                        Available
                    </option>
                    <option value="busy"
                        <?= $profile["availability_status"] === "busy" ? "selected" : "" ?>>
                        Busy
                    </option>
                    <option value="unavailable"
                        <?= $profile["availability_status"] === "unavailable" ? "selected" : "" ?>>
                        Unavailable
                    </option>
                </select>
            </div>

            <div class="project-actions">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="dashboard.php" class="btn-secondary">Cancel</a>
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
