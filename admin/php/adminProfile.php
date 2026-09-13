<?php

session_start();

require_once "connection.php";

header("Content-Type: application/json");


/* ---------- SESSION VALIDATION ---------- */

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized. Please login."
    ]);
    exit;
}

$adminId = $_SESSION["admin_id"];


/* ---------- VALIDATE ID ---------- */

if (!filter_var($adminId, FILTER_VALIDATE_INT)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid admin ID."
    ]);
    exit;
}


/* ---------- GET ADMIN ---------- */

$sql = "
    SELECT admin_id, name, email
    FROM Admins
    WHERE admin_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $adminId);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {
    echo json_encode([
        "success" => false,
        "message" => "Admin not found."
    ]);
    exit;
}

$admin = $result->fetch_assoc();


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "admin" => $admin
]);

?>