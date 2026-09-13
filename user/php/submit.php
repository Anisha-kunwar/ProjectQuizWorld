<?php

session_start();

require_once "../../config/database.php";

header("Content-Type: application/json");


// ------------------------------------
// CHECK LOGIN
// ------------------------------------

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "User is not logged in."
    ]);

    exit;
}


// ------------------------------------
// ONLY POST REQUEST
// ------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


// ------------------------------------
// CHECK QUIZ SESSION
// ------------------------------------

if (
    !isset($_SESSION["quiz_questions"]) ||
    !is_array($_SESSION["quiz_questions"]) ||
    count($_SESSION["quiz_questions"]) === 0
) {

    echo json_encode([
        "success" => false,
        "message" => "Quiz session expired. Please start the quiz again."
    ]);

    exit;
}


$question_ids = $_SESSION["quiz_questions"];


// ------------------------------------
// GET USER ANSWERS
// ------------------------------------

$user_answers = $_POST["answer"] ?? [];

if (!is_array($user_answers)) {
    $user_answers = [];
}


// ------------------------------------
// FIND CORRECT ANSWER COLUMN
// ------------------------------------

$possible_columns = [
    "correct_option",
    "correct_answer",
    "correct",
    "answer",
    "correct_ans"
];

$columns_result = $conn->query("SHOW COLUMNS FROM questions");

if (!$columns_result) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to read questions table."
    ]);

    exit;
}


$available_columns = [];

while ($column = $columns_result->fetch_assoc()) {

    $available_columns[] = $column["Field"];

}


$correct_column = null;


foreach ($possible_columns as $column) {

    if (in_array($column, $available_columns, true)) {

        $correct_column = $column;

        break;

    }

}


// ------------------------------------
// CORRECT ANSWER COLUMN NOT FOUND
// ------------------------------------

if ($correct_column === null) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Could not find the correct-answer column in the questions table. " .
            "Available columns: " .
            implode(", ", $available_columns)
    ]);

    exit;
}


// ------------------------------------
// NORMALIZE ANSWERS
// ------------------------------------

function normalizeAnswer($answer)
{
    $answer = strtoupper(trim((string)$answer));

    switch ($answer) {

        case "1":
        case "A":
            return "A";

        case "2":
        case "B":
            return "B";

        case "3":
        case "C":
            return "C";

        case "4":
        case "D":
            return "D";

        default:
            return $answer;
    }
}


// ------------------------------------
// CALCULATE SCORE
// ------------------------------------

$score = 0;
$total = count($question_ids);


foreach ($question_ids as $question_id) {

    $question_id = (int)$question_id;


    $sql = "SELECT `$correct_column`
            FROM questions
            WHERE question_id = ?
            LIMIT 1";


    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $conn->error
        ]);

        exit;
    }


    $stmt->bind_param("i", $question_id);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();

        $correct_answer =
            normalizeAnswer($row[$correct_column]);


        $user_answer =
            normalizeAnswer(
                $user_answers[$question_id] ?? ""
            );


        if (
            $user_answer !== "" &&
            $user_answer === $correct_answer
        ) {

            $score++;

        }

    }


    $stmt->close();

}


// ------------------------------------
// CALCULATE PERCENTAGE
// ------------------------------------

$percentage = 0;

if ($total > 0) {

    $percentage =
        round(($score / $total) * 100, 2);

}


// ------------------------------------
// SAVE RESULT IN SESSION
// ------------------------------------

$_SESSION["quiz_score"] = $score;

$_SESSION["quiz_total"] = $total;

$_SESSION["quiz_percentage"] = $percentage;


// ------------------------------------
// CLEAR QUIZ QUESTIONS
// ------------------------------------

unset($_SESSION["quiz_questions"]);

unset($_SESSION["quiz_set_id"]);

unset($_SESSION["quiz_category_id"]);

unset($_SESSION["quiz_timer"]);


// ------------------------------------
// SEND RESULT
// ------------------------------------

echo json_encode([

    "success" => true,

    "score" => $score,

    "total" => $total,

    "percentage" => $percentage

]);

exit;

?>