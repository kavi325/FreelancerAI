
<?php
require_once __DIR__ . "/config/database.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($fullName === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    } elseif (strlen($password) < 12) {
        $message = "Use a password with at least 12 characters.";
    } else {
        try {
            $check = $pdo->prepare(
                "SELECT user_id FROM USER WHERE email = ?"
            );
            $check->execute([$email]);

            if ($check->fetch()) {
                throw new Exception("This email already exists.");
            }

            $pdo->beginTransaction();

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $user = $pdo->prepare(
                "INSERT INTO USER (full_name, email, password_hash)
                 VALUES (?, ?, ?)"
            );

            $user->execute([$fullName, $email, $hash]);

            $userId = $pdo->lastInsertId();

            $admin = $pdo->prepare(
                "INSERT INTO ADMINISTRATION
                 (user_id, admin_level, department, assigned_date)
                 VALUES (?, ?, ?, CURDATE())"
            );

            $admin->execute([
                $userId,
                "Super Admin",
                "System Administration"
            ]);

            $pdo->commit();

            $success = true;
            $message = "Super Admin account created successfully. User ID: " . $userId;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = $e instanceof PDOException
                ? "Database operation failed. Check your database configuration and schema."
                : $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Super Admin</title>
</head>
<body>
    <h2>FreelancerAI - Initial Admin Setup</h2>

    <p><?= htmlspecialchars($message) ?></p>

    <?php if (!$success): ?>
        <form method="POST">
            <label>Full Name</label><br>
            <input type="text" name="full_name" required
                   value="System Admin"><br><br>

            <label>Email</label><br>
            <input type="email" name="email" required
                   value="adminfreelance@system.com"><br><br>

            <label>Password</label><br>
            <input type="password" name="password"
                   minlength="12" required><br><br>

            <button type="submit">Create Super Admin</button>
        </form>
    <?php else: ?>
        <h3>Setup completed!</h3>
        <p>Delete setup_admin.php from your project root immediately.</p>
    <?php endif; ?>
</body>
</html>
