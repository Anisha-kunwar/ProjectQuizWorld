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


/* ---------- SET ID ---------- */

$setId = $_GET["set_id"] ?? "";


if (!filter_var($setId, FILTER_VALIDATE_INT)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid set ID."
    ]);

    exit;
}


/* ---------- CHECK SET ---------- */

$sql = "
    SELECT set_id, set_name
    FROM Question_sets
    WHERE set_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $setId);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    echo json_encode([
        "success" => false,
        "message" => "Question set not found."
    ]);

    exit;
}


/* ---------- GET QUESTIONS ---------- */

$sql = "
    SELECT
        question_id,
        question_text,
        option_a,
        option_b,
        option_c,
        option_d,
        correct_answer

    FROM Questions

    WHERE set_id = ?

    ORDER BY question_id ASC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $setId);

$stmt->execute();

$result = $stmt->get_result();


$questions = [];

while ($row = $result->fetch_assoc()) {

    $questions[] = $row;
}


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "questions" => $questions
]);

?>