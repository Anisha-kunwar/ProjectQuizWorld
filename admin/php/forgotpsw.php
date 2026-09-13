<?php

require_once "connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$email = trim($_POST["email"] ?? "");


/* ---------- VALIDATION ---------- */

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
        "message" => "Invalid email format."
    ]);
    exit;
}


/* ---------- CHECK ADMIN ---------- */

$sql = "
    SELECT admin_id, email
    FROM Admins
    WHERE email = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {
    echo json_encode([
        "success" => false,
        "message" => "No admin account found with this email."
    ]);
    exit;
}


/* ---------- GENERATE OTP ---------- */

$otp = random_int(100000, 999999);


/*
    For now we store OTP in session.

    Later, if you want actual email OTP,
    we can connect an email service.
*/

session_start();

$_SESSION["reset_email"] = $email;
$_SESSION["reset_otp"] = $otp;
$_SESSION["otp_time"] = time();


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "message" => "OTP generated successfully."
]);

?>