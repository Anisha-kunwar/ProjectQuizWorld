```php
<?php

session_start();

require_once "../../config/database.php";

header("Content-Type: application/json");


// Get category ID
if (!isset($_GET['category_id'])) {
    echo json_encode([
        "error" => "Category not selected"
    ]);
    exit;
}

$category_id = intval($_GET['category_id']);


// --------------------------------------------------
// CHECK IF THIS IS A NEW QUIZ
// --------------------------------------------------

if (
    !isset($_SESSION['quiz_category']) ||
    $_SESSION['quiz_category'] != $category_id
) {

    // New quiz
    $_SESSION['quiz_category'] = $category_id;

    $_SESSION['quiz_questions'] = [];

    // Find question set
    $sql = "SELECT set_id, timer_sec
            FROM question_sets
            WHERE category_id = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $category_id);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows == 0) {

        echo json_encode([
            "error" => "No question set found"
        ]);

        exit;
    }


    $set = $result->fetch_assoc();

    $set_id = $set['set_id'];

    $_SESSION['quiz_set_id'] = $set_id;

    $_SESSION['quiz_timer'] = $set['timer_sec'];


    // --------------------------------------------------
    // GET 10 RANDOM QUESTIONS
    // --------------------------------------------------

    $sql = "SELECT question_id
            FROM questions
            WHERE set_id = ?
            ORDER BY RAND()
            LIMIT 10";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $set_id);

    $stmt->execute();

    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {

        $_SESSION['quiz_questions'][] =
            $row['question_id'];

    }


    // Check if we got 10 questions
    if (count($_SESSION['quiz_questions']) < 10) {

        echo json_encode([
            "error" => "Not enough questions in this category"
        ]);

        exit;
    }
}


// --------------------------------------------------
// GET QUESTION NUMBER
// --------------------------------------------------

if (!isset($_GET['question_number'])) {

    $question_number = 0;

} else {

    $question_number =
        intval($_GET['question_number']);

}


// --------------------------------------------------
// CHECK QUESTION EXISTS
// --------------------------------------------------

if (
    !isset(
        $_SESSION['quiz_questions'][$question_number]
    )
) {

    echo json_encode([
        "error" => "Question not found"
    ]);

    exit;
}


$question_id =
    $_SESSION['quiz_questions'][$question_number];


// --------------------------------------------------
// GET QUESTION DETAILS
// --------------------------------------------------

$sql = "SELECT
            question_id,
            question_text,
            option_a,
            option_b,
            option_c,
            option_d
        FROM questions
        WHERE question_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $question_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows == 0) {

    echo json_encode([
        "error" => "Question not found in database"
    ]);

    exit;
}


$question = $result->fetch_assoc();


// --------------------------------------------------
// SEND QUESTION
// --------------------------------------------------

echo json_encode($question);

?>
```
