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


/* ---------- STUDENT ID ---------- */

$studentId = $_GET["student_id"] ?? "";


if (!filter_var($studentId, FILTER_VALIDATE_INT)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid student ID."
    ]);

    exit;
}


/* ---------- CHECK STUDENT ---------- */

$sql = "
    SELECT student_id, name, email
    FROM Students
    WHERE student_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $studentId);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    echo json_encode([
        "success" => false,
        "message" => "Student not found."
    ]);

    exit;
}


/* ---------- GET ATTEMPTS ---------- */

$sql = "
    SELECT
        qa.score,
        qa.time_taken,
        qa.attempt_date,
        c.category_name,
        qs.set_name

    FROM Quiz_Attempts qa

    INNER JOIN Question_sets qs
        ON qa.set_id = qs.set_id

    INNER JOIN Categories c
        ON qs.category_id = c.category_id

    WHERE qa.student_id = ?

    ORDER BY qa.attempt_date DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $studentId);

$stmt->execute();

$result = $stmt->get_result();


$attempts = [];

while ($row = $result->fetch_assoc()) {

    $attempts[] = $row;
}


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "attempts" => $attempts
]);

?>