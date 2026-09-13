<?php
session_start();

require_once "../../config/database.php";


// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}


// Check category
if (!isset($_GET["category_id"]) || !is_numeric($_GET["category_id"])) {
    die("Category not selected.");
}

$category_id = (int)$_GET["category_id"];


// Find question set for this category
$sql = "SELECT set_id, timer_sec
        FROM question_sets
        WHERE category_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No quiz found for this category.");
}

$question_set = $result->fetch_assoc();

$stmt->close();

$set_id = (int)$question_set["set_id"];


// Timer
$timer_sec = (int)$question_set["timer_sec"];

if ($timer_sec <= 0) {
    $timer_sec = 300;
}


// Get 10 random questions
$sql = "SELECT
            question_id,
            question_text,
            option_a,
            option_b,
            option_c,
            option_d
        FROM questions
        WHERE set_id = ?
        ORDER BY RAND()
        LIMIT 10";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $set_id);
$stmt->execute();

$result = $stmt->get_result();

$questions = [];

while ($row = $result->fetch_assoc()) {

    $questions[] = [

        "question_id" => (int)$row["question_id"],

        "question_text" => $row["question_text"],

        "option_a" => $row["option_a"],

        "option_b" => $row["option_b"],

        "option_c" => $row["option_c"],

        "option_d" => $row["option_d"]

    ];
}

$stmt->close();


// Make sure questions exist
if (count($questions) === 0) {
    die("No questions available for this quiz.");
}


// Store quiz information in session
$_SESSION["quiz_questions"] = array_column(
    $questions,
    "question_id"
);

$_SESSION["quiz_set_id"] = $set_id;

$_SESSION["quiz_category_id"] = $category_id;

$_SESSION["quiz_timer"] = $timer_sec;


// Convert questions to JSON
$questions_json = json_encode(
    $questions,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_AMP |
    JSON_HEX_QUOT
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Quiz - QuizWorld</title>

    <link rel="stylesheet" href="../css/quiz.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

</head>


<body>


<div class="quiz-box">

    <h1>QuizWorld</h1>


    <!-- TIMER -->

    <div class="timer">

        Time Left:

        <span id="time">
            00:00
        </span>

    </div>


    <!-- PROGRESS -->

    <div class="progress-container">

        <div
            class="progress-bar"
            id="progressBar">
        </div>

    </div>


    <!-- QUESTION NUMBER -->

    <div id="questionNumber">
        Question 1 of <?= count($questions) ?>
    </div>


    <!-- QUESTION -->

    <div class="question-card">

        <div id="question"></div>


        <div class="options">

            <button
                type="button"
                class="option"
                data-option="A">
            </button>

            <button
                type="button"
                class="option"
                data-option="B">
            </button>

            <button
                type="button"
                class="option"
                data-option="C">
            </button>

            <button
                type="button"
                class="option"
                data-option="D">
            </button>

        </div>

    </div>


    <!-- BUTTONS -->

    <div class="buttons">

        <button
            type="button"
            id="next">
            Next
        </button>

        <button
            type="button"
            id="submit">
            Submit
        </button>

    </div>

</div>


<script>

const questions = <?= $questions_json ?>;

let currentQuestion = 0;

let selectedAnswer = null;

let answers = {};

let submitted = false;


// Timer
let timeLeft = <?= $timer_sec ?>;


// HTML elements
const questionElement =
    document.getElementById("question");

const questionNumberElement =
    document.getElementById("questionNumber");

const timeElement =
    document.getElementById("time");

const progressBar =
    document.getElementById("progressBar");

const optionButtons =
    document.querySelectorAll(".option");

const nextButton =
    document.getElementById("next");

const submitButton =
    document.getElementById("submit");


// ------------------------------------
// LOAD QUESTION
// ------------------------------------

function loadQuestion() {

    const q = questions[currentQuestion];

    questionElement.textContent =
        q.question_text;


    optionButtons[0].textContent =
        q.option_a;

    optionButtons[1].textContent =
        q.option_b;

    optionButtons[2].textContent =
        q.option_c;

    optionButtons[3].textContent =
        q.option_d;


    questionNumberElement.textContent =
        "Question " +
        (currentQuestion + 1) +
        " of " +
        questions.length;


    const progress =
        ((currentQuestion + 1) / questions.length) * 100;

    progressBar.style.width =
        progress + "%";


    // Remove previous selection
    optionButtons.forEach(function(button) {

        button.classList.remove("selected");

    });


    selectedAnswer = null;


    // Restore previously selected answer
    const questionId = q.question_id;

    if (answers[questionId]) {

        selectedAnswer =
            answers[questionId];

        optionButtons.forEach(function(button) {

            if (
                button.dataset.option ===
                selectedAnswer
            ) {

                button.classList.add("selected");

            }

        });

    }


    // Show / hide buttons
    if (currentQuestion === questions.length - 1) {

        nextButton.style.display = "none";

        submitButton.style.display = "block";

    } else {

        nextButton.style.display = "block";

        submitButton.style.display = "block";

    }

}


// ------------------------------------
// SELECT OPTION
// ------------------------------------

optionButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        optionButtons.forEach(function(btn) {

            btn.classList.remove("selected");

        });


        button.classList.add("selected");


        selectedAnswer =
            button.dataset.option;


        const questionId =
            questions[currentQuestion].question_id;


        answers[questionId] =
            selectedAnswer;

    });

});


// ------------------------------------
// NEXT QUESTION
// ------------------------------------

nextButton.addEventListener("click", function() {

    if (!selectedAnswer) {

        alert("Please select an answer.");

        return;

    }


    const questionId =
        questions[currentQuestion].question_id;


    answers[questionId] =
        selectedAnswer;


    currentQuestion++;


    loadQuestion();

});


// ------------------------------------
// SUBMIT QUIZ
// ------------------------------------

function submitQuiz() {

    if (submitted) {
        return;
    }

    submitted = true;


    const formData =
        new FormData();


    Object.keys(answers).forEach(function(questionId) {

        formData.append(
            "answer[" + questionId + "]",
            answers[questionId]
        );

    });


    fetch("submit.php", {

        method: "POST",

        body: formData

    })

    .then(function(response) {

        return response.json();

    })

    .then(function(data) {

        if (data.success) {

            window.location.href =
                "result.php?score=" +
                encodeURIComponent(data.score) +
                "&total=" +
                encodeURIComponent(data.total) +
                "&percentage=" +
                encodeURIComponent(data.percentage);

        } else {

            submitted = false;

            alert(
                data.message ||
                "Unable to submit quiz."
            );

        }

    })

    .catch(function(error) {

        submitted = false;

        console.error(error);

        alert(
            "Something went wrong while submitting the quiz."
        );

    });

}


submitButton.addEventListener(
    "click",
    function() {

        if (!selectedAnswer) {

            alert("Please select an answer.");

            return;

        }


        const questionId =
            questions[currentQuestion].question_id;


        answers[questionId] =
            selectedAnswer;


        submitQuiz();

    }
);


// ------------------------------------
// TIMER
// ------------------------------------

function updateTimer() {

    const minutes =
        Math.floor(timeLeft / 60);

    const seconds =
        timeLeft % 60;


    timeElement.textContent =

        String(minutes).padStart(2, "0") +
        ":" +
        String(seconds).padStart(2, "0");


    if (timeLeft <= 0) {

        clearInterval(timer);

        submitQuiz();

        return;

    }


    timeLeft--;

}


const timer =
    setInterval(updateTimer, 1000);


// ------------------------------------
// START
// ------------------------------------

submitButton.style.display = "none";

loadQuestion();

updateTimer();

</script>


</body>
</html>