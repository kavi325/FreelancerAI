
<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

// Basic information
$fullName = trim($_POST["full_name"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$phone = trim($_POST["phone"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$country = trim($_POST["country"] ?? "");
$role = $_POST["role"] ?? "";

// Validate basic information
if (
    $fullName === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $country === "" ||
    $password === "" ||
    $password !== $confirmPassword ||
    !in_array($role, ["client", "freelancer", "both"], true)
) {
    die("Please provide valid registration details.");
}

if (strlen($password) < 8) {
    die("Password must contain at least 8 characters.");
}

// Collect client information
$clientType = $_POST["client_type"] ?? "";
$organizationName = trim($_POST["organization_name"] ?? "");
$organizationType = trim($_POST["organization_type"] ?? "");
$businessDescription = trim($_POST["business_description"] ?? "");

// Collect freelancer information
$title = trim($_POST["professional_title"] ?? "");
$biography = trim($_POST["biography"] ?? "");
$experience = $_POST["experience_years"] ?? "";
$hourlyRate = $_POST["hourly_rate"] ?? "";
$availability = $_POST["availability_status"] ?? "available";

// Validate client details
if ($role === "client" || $role === "both") {

    if (!in_array($clientType, ["individual", "organization"], true)) {
        die("Please select a valid client type.");
    }

    if ($clientType === "organization" && $organizationName === "") {
        die("Organization name is required.");
    }
}

// Validate freelancer details
if ($role === "freelancer" || $role === "both") {

    if (
        $title === "" ||
        !is_numeric($experience) ||
        (float)$experience < 0 ||
        (float)$experience > 999.9 ||
        !is_numeric($hourlyRate) ||
        (float)$hourlyRate < 0 ||
        (float)$hourlyRate > 99999999.99 ||
        !in_array($availability, ["available", "busy", "unavailable"], true)
    ) {
        die("Please provide valid freelancer details.");
    }
}

try {

    $pdo->beginTransaction();

    // Check whether email already exists
    $check = $pdo->prepare(
        "SELECT user_id FROM `USER` WHERE email = ?"
    );

    $check->execute([$email]);

    if ($check->fetch()) {
        throw new Exception("This email is already registered.");
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Create main user account
    $stmt = $pdo->prepare(
        "INSERT INTO `USER`
        (full_name, email, password_hash)
        VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $fullName,
        $email,
        $hash
    ]);

    $userId = $pdo->lastInsertId();

    // Save phone number
    if ($phone !== "") {

        $phoneStmt = $pdo->prepare(
            "INSERT INTO USER_PHONE
            (user_id, phone_number)
            VALUES (?, ?)"
        );

        $phoneStmt->execute([
            $userId,
            $phone
        ]);
    }

    // Create client profile
    if ($role === "client" || $role === "both") {

        $clientStmt = $pdo->prepare(
            "INSERT INTO CLIENT
            (
                user_id,
                organization_name,
                client_type,
                organization_type,
                business_description,
                country
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $clientStmt->execute([
            $userId,
            $organizationName ?: null,
            $clientType,
            $organizationType ?: null,
            $businessDescription ?: null,
            $country
        ]);
    }

    // Create freelancer profile
    if ($role === "freelancer" || $role === "both") {

        $freelancerStmt = $pdo->prepare(
            "INSERT INTO FREELANCER
            (
                user_id,
                professional_title,
                biography,
                experience_years,
                hourly_rate,
                country,
                availability_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $freelancerStmt->execute([
            $userId,
            $title,
            $biography ?: null,
            $experience,
            $hourlyRate,
            $country,
            $availability
        ]);
    }

    // Complete registration
    $pdo->commit();

    header("Location: login.php?registered=1");
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Development error handling
    if ($e instanceof PDOException) {
        error_log($e->getMessage());
        die("Registration failed due to a database error.");
    }

    die(htmlspecialchars($e->getMessage()));
}
