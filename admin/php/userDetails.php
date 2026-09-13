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


/* ---------- GET STUDENTS ---------- */

$sql = "
    SELECT
        s.student_id,
        s.name,
        s.email,
        COUNT(qa.attempt_id) AS quiz_attempts

    FROM Students s

    LEFT JOIN Quiz_Attempts qa
        ON s.student_id = qa.student_id

    GROUP BY
        s.student_id,
        s.name,
        s.email

    ORDER BY s.student_id ASC
";


$result = $conn->query($sql);


if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to load students."
    ]);

    exit;
}


$students = [];

while ($row = $result->fetch_assoc()) {

    $students[] = $row;
}


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "students" => $students
]);

?>