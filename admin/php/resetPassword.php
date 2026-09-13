<?php

session_start();

require_once "connection.php";

header("Content-Type: application/json");


/* ---------- GET DATA ---------- */

$email = trim($_POST["email"] ?? "");

$otp = trim($_POST["otp"] ?? "");

$newPassword = $_POST["new_password"] ?? "";

$confirmPassword = $_POST["confirm_password"] ?? "";


/* ---------- EMAIL ---------- */

if ($email === "") {

    echo json_encode([
        "success" => false,
        "message" => "Email is required."
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email."
    ]);

    exit;
}


/* ---------- OTP ---------- */

if ($otp === "") {

    echo json_encode([
        "success" => false,
        "message" => "OTP is required."
    ]);

    exit;
}

if (!preg_match("/^[0-9]{6}$/", $otp)) {

    echo json_encode([
        "success" => false,
        "message" => "OTP must contain 6 digits."
    ]);

    exit;
}


/* ---------- CHECK OTP ---------- */

if (
    !isset($_SESSION["reset_email"]) ||
    !isset($_SESSION["reset_otp"]) ||
    !isset($_SESSION["otp_time"])
) {

    echo json_encode([
        "success" => false,
        "message" => "OTP session expired. Please request a new OTP."
    ]);

    exit;
}


if ($_SESSION["reset_email"] !== $email) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email."
    ]);

    exit;
}


/* OTP valid for 10 minutes */

if (time() - $_SESSION["otp_time"] > 600) {

    unset(
        $_SESSION["reset_email"],
        $_SESSION["reset_otp"],
        $_SESSION["otp_time"]
    );

    echo json_encode([
        "success" => false,
        "message" => "OTP has expired."
    ]);

    exit;
}


if ((string)$otp !== (string)$_SESSION["reset_otp"]) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid OTP."
    ]);

    exit;
}


/* ---------- PASSWORD ---------- */

if ($newPassword === "") {

    echo json_encode([
        "success" => false,
        "message" => "New password is required."
    ]);

    exit;
}


if (strlen($newPassword) < 8) {

    echo json_encode([
        "success" => false,
        "message" => "Password must contain at least 8 characters."
    ]);

    exit;
}


if ($newPassword !== $confirmPassword) {

    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);

    exit;
}


/* ---------- HASH ---------- */

$hashedPassword = password_hash(
    $newPassword,
    PASSWORD_DEFAULT
);


/* ---------- UPDATE ---------- */

$sql = "
    UPDATE Admins
    SET password = ?
    WHERE email = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $hashedPassword,
    $email
);


if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to reset password."
    ]);

    exit;
}


/* ---------- CLEAR OTP ---------- */

unset(
    $_SESSION["reset_email"],
    $_SESSION["reset_otp"],
    $_SESSION["otp_time"]
);


echo json_encode([
    "success" => true,
    "message" => "Password reset successfully."
]);

?>