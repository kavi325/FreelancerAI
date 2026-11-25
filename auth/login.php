
<?php
session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";
    } else {

        try {
            $stmt = $pdo->prepare("
                SELECT
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.password_hash,
                    u.account_status,
                    c.user_id AS client_id,
                    f.user_id AS freelancer_id,
                    m.user_id AS management_id,
                    a.user_id AS admin_id
                FROM `USER` u
                LEFT JOIN CLIENT c ON u.user_id = c.user_id
                LEFT JOIN FREELANCER f ON u.user_id = f.user_id
                LEFT JOIN MANAGEMENT m ON u.user_id = m.user_id
                LEFT JOIN ADMINISTRATION a ON u.user_id = a.user_id
                WHERE u.email = ?
                LIMIT 1
            ");

            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password_hash"])) {

                if ($user["account_status"] !== "active") {
                    $error = "Your account is not active. Please contact support.";
                } else {

                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["full_name"] = $user["full_name"];
                    $_SESSION["email"] = $user["email"];

                    if (!empty($user["admin_id"])) {

                        $_SESSION["role"] = "admin";
                        header("Location: ../management/dashboard.php");

                    } elseif (!empty($user["management_id"])) {

                        $_SESSION["role"] = "management";
                        header("Location: ../management/dashboard.php");

                    } elseif (!empty($user["freelancer_id"])) {

                        $_SESSION["role"] = "freelancer";
                        header("Location: ../freelancer/dashboard.php");

                    } elseif (!empty($user["client_id"])) {

                        $_SESSION["role"] = "client";
                        header("Location: ../client/dashboard.php");

                    } else {
                        session_unset();
                        session_destroy();
                        $error = "No account role is assigned to this user.";
                    }

                    if (empty($error)) {
                        exit;
                    }
                }

            } else {
                $error = "Invalid email address or password.";
            }

        } catch (PDOException $e) {
            $error = "Login failed. Please try again later.";
        }
    }
}
?>


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

        <?php if (!empty($error)): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="form-options">
                <label class="remember-option">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="auth-button">
                Login to Account
            </button>

        </form>

        <div class="auth-divider">
            <span>New to FreelancerAI?</span>
        </div>

        <a href="register.php" class="auth-outline-button">
            Create an Account
        </a>

    </div>

    <a href="../index.php" class="back-home">
        ← Back to Homepage
    </a>
</div>

</body>
</html>
