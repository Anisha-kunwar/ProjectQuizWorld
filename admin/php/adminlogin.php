<?php

session_start();

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
$password = $_POST["password"] ?? "";


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

if ($password === "") {
    echo json_encode([
        "success" => false,
        "message" => "Password is required."
    ]);
    exit;
}


/* ---------- FIND ADMIN ---------- */

$sql = "SELECT admin_id, name, email, password
        FROM Admins
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

$admin = $result->fetch_assoc();


/* ---------- PASSWORD CHECK ---------- */

if (!password_verify($password, $admin["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}


/* ---------- CREATE SESSION ---------- */

session_regenerate_id(true);

$_SESSION["admin_id"] = $admin["admin_id"];
$_SESSION["admin_name"] = $admin["name"];
$_SESSION["admin_email"] = $admin["email"];


echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "redirect" => "../html/adminDashboard.html"
]);

?>