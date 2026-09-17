<?php

session_start();

require_once "../../config/database.php";

header("Content-Type: application/json");


// ------------------------------------
// HELPER FUNCTION
// ------------------------------------

function sendError($message)
{
    echo json_encode([
        "success" => false,
        "message" => $message
    ]);

    exit;
}


// ------------------------------------
// CHECK LOGIN
// ------------------------------------

if (!isset($_SESSION["user_id"])) {

    sendError(
        "User is not logged in."
    );
}


$user_id =
    (int)$_SESSION["user_id"];


// ------------------------------------
// CHECK REQUEST
// ------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    sendError(
        "Invalid request method."
    );
}


// ------------------------------------
// CHECK QUIZ SESSION
// ------------------------------------

if (
    !isset($_SESSION["quiz_questions"]) ||
    !is_array($_SESSION["quiz_questions"]) ||
    count($_SESSION["quiz_questions"]) === 0
) {

    sendError(
        "Quiz session expired. Please start the quiz again."
    );
}


$question_ids =
    $_SESSION["quiz_questions"];


// ------------------------------------
// GET ANSWERS
// ------------------------------------

$user_answers =
    $_POST["answer"] ?? [];

if (!is_array($user_answers)) {

    $user_answers = [];
}


// ------------------------------------
// CHECK QUIZ SET
// ------------------------------------

if (!isset($_SESSION["quiz_set_id"])) {

    sendError(
        "Quiz set information is missing."
    );
}


$set_id =
    (int)$_SESSION["quiz_set_id"];


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


$columns_result =
    $conn->query(
        "SHOW COLUMNS FROM questions"
    );


if (!$columns_result) {

    sendError(
        "Unable to read questions table: " .
        $conn->error
    );
}


$available_columns = [];


while (
    $column =
    $columns_result->fetch_assoc()
) {

    $available_columns[] =
        $column["Field"];
}


$correct_column = null;


foreach (
    $possible_columns as $column
) {

    if (
        in_array(
            $column,
            $available_columns,
            true
        )
    ) {

        $correct_column =
            $column;

        break;
    }
}


if ($correct_column === null) {

    sendError(
        "Correct-answer column not found in questions table."
    );
}


// ------------------------------------
// NORMALIZE ANSWER
// ------------------------------------

function normalizeAnswer($answer)
{

    $answer =
        strtoupper(
            trim(
                (string)$answer
            )
        );


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

$total =
    count($question_ids);


foreach (
    $question_ids as $question_id
) {

    $question_id =
        (int)$question_id;


    $sql = "SELECT `$correct_column`
            FROM questions
            WHERE question_id = ?
            LIMIT 1";


    $stmt =
        $conn->prepare($sql);


    if (!$stmt) {

        sendError(
            "Database error: " .
            $conn->error
        );
    }


    $stmt->bind_param(
        "i",
        $question_id
    );


    if (!$stmt->execute()) {

        $stmt->close();

        sendError(
            "Unable to check question answer."
        );
    }


    $result =
        $stmt->get_result();


    if (
        $result->num_rows === 1
    ) {

        $row =
            $result->fetch_assoc();


        $correct_answer =
            normalizeAnswer(
                $row[$correct_column]
            );


        $user_answer =
            normalizeAnswer(
                $user_answers[$question_id]
                ?? ""
            );


        if (
            $user_answer !== "" &&
            $user_answer ===
            $correct_answer
        ) {

            $score++;
        }
    }


    $stmt->close();
}


// ------------------------------------
// PERCENTAGE
// ------------------------------------

$percentage = 0;


if ($total > 0) {

    $percentage =
        round(
            ($score / $total) * 100,
            2
        );
}


// ------------------------------------
// TIME TAKEN
// ------------------------------------

$time_taken_seconds = 0;


// ------------------------------------
// SAVE QUIZ ATTEMPT
// ------------------------------------

$sql = "INSERT INTO quiz_attempts
        (
            user_id,
            set_id,
            score,
            total_questions,
            time_taken_seconds,
            date_attempted
        )
        VALUES (?, ?, ?, ?, ?, NOW())";


$stmt =
    $conn->prepare($sql);


if (!$stmt) {

    sendError(
        "Unable to prepare quiz attempt: " .
        $conn->error
    );
}


// user_id = INT
// set_id = INT
// score = INT
// total_questions = INT
// time_taken_seconds = INT

$stmt->bind_param(
    "iiiii",
    $user_id,
    $set_id,
    $score,
    $total,
    $time_taken_seconds
);


if (!$stmt->execute()) {

    $error =
        $stmt->error;

    $stmt->close();

    sendError(
        "Unable to save quiz attempt: " .
        $error
    );
}


// ------------------------------------
// GET ATTEMPT ID
// ------------------------------------

$attempt_id =
    $conn->insert_id;


$stmt->close();


// ------------------------------------
// SAVE RESULT IN SESSION
// ------------------------------------

$_SESSION["quiz_score"] =
    $score;

$_SESSION["quiz_total"] =
    $total;

$_SESSION["quiz_percentage"] =
    $percentage;

$_SESSION["quiz_attempt_id"] =
    $attempt_id;


// ------------------------------------
// CLEAR ACTIVE QUIZ SESSION
// ------------------------------------

unset(
    $_SESSION["quiz_questions"]
);

unset(
    $_SESSION["quiz_set_id"]
);

unset(
    $_SESSION["quiz_category_id"]
);

unset(
    $_SESSION["quiz_timer"]
);


// ------------------------------------
// SEND SUCCESS RESPONSE
// ------------------------------------

echo json_encode([

    "success" => true,

    "message" =>
        "Quiz submitted successfully.",

    "score" =>
        $score,

    "total" =>
        $total,

    "percentage" =>
        $percentage,

    "attempt_id" =>
        $attempt_id

]);

exit;

?>