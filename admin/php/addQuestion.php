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


/* ---------- REQUEST VALIDATION ---------- */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* ---------- GET DATA ---------- */

$setId = $_POST["set_id"] ?? "";

$question = trim($_POST["question"] ?? "");

$optionA = trim($_POST["optionA"] ?? "");
$optionB = trim($_POST["optionB"] ?? "");
$optionC = trim($_POST["optionC"] ?? "");
$optionD = trim($_POST["optionD"] ?? "");

$correctAnswer = $_POST["correctAnswer"] ?? "";


/* =====================================================
   VALIDATION
   ===================================================== */


/* SET */

if (!filter_var($setId, FILTER_VALIDATE_INT)) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid question set."
    ]);

    exit;
}


/* QUESTION */

if ($question === "") {

    echo json_encode([
        "success" => false,
        "message" => "Question is required."
    ]);

    exit;
}


if (strlen($question) < 5) {

    echo json_encode([
        "success" => false,
        "message" => "Question must contain at least 5 characters."
    ]);

    exit;
}


if (strlen($question) > 1000) {

    echo json_encode([
        "success" => false,
        "message" => "Question is too long."
    ]);

    exit;
}


/* OPTIONS */

if (
    $optionA === "" ||
    $optionB === "" ||
    $optionC === "" ||
    $optionD === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "All four options are required."
    ]);

    exit;
}


/* CORRECT ANSWER */

if (!in_array(
    $correctAnswer,
    ["0", "1", "2", "3"],
    true
)) {

    echo json_encode([
        "success" => false,
        "message" => "Please select a valid correct answer."
    ]);

    exit;
}


/* =====================================================
   CHECK SET EXISTS
   ===================================================== */

$sql = "
    SELECT set_id
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
        "message" => "Question set does not exist."
    ]);

    exit;
}


/* =====================================================
   INSERT QUESTION
   ===================================================== */

$sql = "
    INSERT INTO Questions
    (
        set_id,
        question_text,
        option_a,
        option_b,
        option_c,
        option_d,
        correct_answer
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "issssss",
    $setId,
    $question,
    $optionA,
    $optionB,
    $optionC,
    $optionD,
    $correctAnswer
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Question added successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to add question."
    ]);
}

?>