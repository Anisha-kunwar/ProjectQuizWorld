<?php

session_start();

require_once "connection.php";

header("Content-Type: application/json");


/* ---------- ADMIN VALIDATION ---------- */

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized. Please login."
    ]);
    exit;
}


/* ---------- TOTAL ATTEMPTS ---------- */

$sql = "SELECT COUNT(*) AS total FROM Quiz_Attempts";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to get quiz attempts."
    ]);
    exit;
}

$attempts = $result->fetch_assoc()["total"];


/* ---------- TOTAL STUDENTS ---------- */

$sql = "SELECT COUNT(*) AS total FROM Students";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to get students."
    ]);
    exit;
}

$students = $result->fetch_assoc()["total"];


/* ---------- PASS RATE ---------- */

$sql = "
    SELECT
        COUNT(*) AS total_attempts,
        SUM(
            CASE
                WHEN score >= 50 THEN 1
                ELSE 0
            END
        ) AS passed
    FROM Quiz_Attempts
";

$result = $conn->query($sql);

$data = $result->fetch_assoc();

$passRate = 0;

if ($data["total_attempts"] > 0) {
    $passRate =
        ($data["passed"] / $data["total_attempts"]) * 100;
}


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "attempts" => (int)$attempts,
    "students" => (int)$students,
    "passRate" => round($passRate, 2)
]);

?>