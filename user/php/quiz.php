<?php

session_start();

require_once "../../config/database.php";

// ------------------------------------
// CHECK LOGIN
// ------------------------------------

if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

// ------------------------------------
// CHECK CATEGORY
// ------------------------------------

if (!isset($_GET["category_id"]) || !is_numeric($_GET["category_id"])) {
    die("Category not selected.");
}

$category_id = (int)$_GET["category_id"];

// ------------------------------------
// GET QUIZ SET
// ------------------------------------

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
$timer_sec = (int)$question_set["timer_sec"];

// If timer is missing/invalid, use 5 minutes
if ($timer_sec <= 0) {
    $timer_sec = 300;
}

// ------------------------------------
// GET 10 RANDOM QUESTIONS
// ------------------------------------

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

// ------------------------------------
// CHECK QUESTIONS
// ------------------------------------

if (count($questions) === 0) {
    die("No questions found for this quiz.");
}

// ------------------------------------
// SAVE QUIZ INFORMATION IN SESSION
// ------------------------------------

$_SESSION["quiz_questions"] = array_column(
    $questions,
    "question_id"
);

$_SESSION["quiz_set_id"] = $set_id;

$_SESSION["quiz_category_id"] = $category_id;

$_SESSION["quiz_timer"] = $timer_sec;

// ------------------------------------
// SEND QUESTIONS TO JAVASCRIPT
// ------------------------------------

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
        content="width=device-width, initial-scale=1.0"
    >

    <title>QuizWorld Quiz</title>

    <link
        rel="stylesheet"
        href="../css/quiz.css"
    >

</head>

<body>

<div class="quiz-container">

    <!-- HEADER -->

    <div class="quiz-header">

        <h1>QUIZWORLD</h1>

        <div class="timer">
            Time:
            <span id="timer">00:00</span>
        </div>

    </div>


    <!-- QUESTION AREA -->

    <div class="quiz-box">

        <div class="question-number">
            Question
            <span id="questionNumber">1</span>
            /
            <span id="totalQuestions">
                <?= count($questions) ?>
            </span>
        </div>


        <h2 id="questionText">
            Loading question...
        </h2>


        <!-- OPTIONS -->

        <div class="options">

            <label class="option">
                <input
                    type="radio"
                    name="answer"
                    value="A"
                >
                <span id="optionA"></span>
            </label>


            <label class="option">
                <input
                    type="radio"
                    name="answer"
                    value="B"
                >
                <span id="optionB"></span>
            </label>


            <label class="option">
                <input
                    type="radio"
                    name="answer"
                    value="C"
                >
                <span id="optionC"></span>
            </label>


            <label class="option">
                <input
                    type="radio"
                    name="answer"
                    value="D"
                >
                <span id="optionD"></span>
            </label>

        </div>


        <!-- BUTTONS -->

        <div class="quiz-buttons">

            <button
                type="button"
                id="nextButton"
            >
                Next
            </button>


            <button
                type="button"
                id="submitButton"
                style="display:none;"
            >
                Submit Quiz
            </button>

        </div>

    </div>

</div>


<script>

// ------------------------------------
// QUESTIONS FROM PHP
// ------------------------------------

const questions = <?= $questions_json ?>;


// ------------------------------------
// QUIZ VARIABLES
// ------------------------------------

let currentQuestion = 0;

let selectedAnswer = null;

let answers = {};

let submitted = false;


// ------------------------------------
// TIMER
// ------------------------------------

let timeLeft = <?= $timer_sec ?>;

const timerElement =
    document.getElementById("timer");


function updateTimer() {

    const minutes =
        Math.floor(timeLeft / 60);

    const seconds =
        timeLeft % 60;

    timerElement.textContent =
        String(minutes).padStart(2, "0") +
        ":" +
        String(seconds).padStart(2, "0");

}


const timerInterval = setInterval(() => {

    if (submitted) {
        clearInterval(timerInterval);
        return;
    }

    if (timeLeft <= 0) {

        clearInterval(timerInterval);

        submitQuiz(true);

        return;
    }

    timeLeft--;

    updateTimer();

}, 1000);


// ------------------------------------
// HTML ELEMENTS
// ------------------------------------

const questionNumber =
    document.getElementById("questionNumber");

const totalQuestions =
    document.getElementById("totalQuestions");

const questionText =
    document.getElementById("questionText");

const optionA =
    document.getElementById("optionA");

const optionB =
    document.getElementById("optionB");

const optionC =
    document.getElementById("optionC");

const optionD =
    document.getElementById("optionD");

const nextButton =
    document.getElementById("nextButton");

const submitButton =
    document.getElementById("submitButton");

const optionInputs =
    document.querySelectorAll(
        'input[name="answer"]'
    );


// ------------------------------------
// LOAD QUESTION
// ------------------------------------

function loadQuestion() {

    const question =
        questions[currentQuestion];

    questionNumber.textContent =
        currentQuestion + 1;

    totalQuestions.textContent =
        questions.length;

    questionText.textContent =
        question.question_text;

    optionA.textContent =
        question.option_a;

    optionB.textContent =
        question.option_b;

    optionC.textContent =
        question.option_c;

    optionD.textContent =
        question.option_d;


    // Clear previous selection

    optionInputs.forEach(input => {

        input.checked = false;

    });


    selectedAnswer = null;


    // Restore answer if already selected

    const questionId =
        question.question_id;

    if (answers[questionId]) {

        optionInputs.forEach(input => {

            if (
                input.value ===
                answers[questionId]
            ) {

                input.checked = true;

                selectedAnswer =
                    input.value;
            }

        });

    }


    // --------------------------------
    // BUTTON VISIBILITY
    // --------------------------------

    if (
        currentQuestion ===
        questions.length - 1
    ) {

        nextButton.style.display =
            "none";

        submitButton.style.display =
            "block";

    } else {

        nextButton.style.display =
            "block";

        submitButton.style.display =
            "none";
    }

}


// ------------------------------------
// OPTION SELECTION
// ------------------------------------

optionInputs.forEach(input => {

    input.addEventListener(
        "change",
        function () {

            selectedAnswer =
                this.value;

            const questionId =
                questions[currentQuestion]
                    .question_id;

            answers[questionId] =
                this.value;

        }
    );

});


// ------------------------------------
// NEXT BUTTON
// ------------------------------------

nextButton.addEventListener(
    "click",
    function () {

        const questionId =
            questions[currentQuestion]
                .question_id;

        if (!answers[questionId]) {

            alert(
                "Please select an answer first."
            );

            return;
        }


        if (
            currentQuestion <
            questions.length - 1
        ) {

            currentQuestion++;

            loadQuestion();
        }

    }
);


// ------------------------------------
// SUBMIT BUTTON
// ------------------------------------

submitButton.addEventListener(
    "click",
    function () {

        const questionId =
            questions[currentQuestion]
                .question_id;

        if (!answers[questionId]) {

            alert(
                "Please select an answer first."
            );

            return;
        }

        submitQuiz(false);

    }
);


// ------------------------------------
// SUBMIT QUIZ
// ------------------------------------

async function submitQuiz(autoSubmit = false) {

    if (submitted) {
        return;
    }

    submitted = true;

    clearInterval(timerInterval);


    if (autoSubmit) {

        alert(
            "Time is over! Your quiz will be submitted."
        );

    }


    const formData =
        new FormData();


    // --------------------------------
    // SEND ALL ANSWERS
    // --------------------------------

    Object.keys(answers).forEach(
        questionId => {

            formData.append(
                "answer[" + questionId + "]",
                answers[questionId]
            );

        }
    );


    try {

        submitButton.disabled = true;

        nextButton.disabled = true;


        const response =
            await fetch("../php/submit.php", {

                method: "POST",

                body: formData

            });


        const text =
            await response.text();


        console.log(
            "submit.php response:",
            text
        );


        let data;

        try {

            data = JSON.parse(text);

        } catch (error) {

            console.error(
                "Invalid JSON:",
                text
            );

            alert(
                "Server returned an invalid response. Check submit.php."
            );

            submitted = false;

            submitButton.disabled = false;

            nextButton.disabled = false;

            return;
        }


        // --------------------------------
        // SUCCESS
        // --------------------------------

        if (data.success) {

            if (!data.attempt_id) {

                alert(
                    "Quiz submitted, but attempt ID is missing."
                );

                return;
            }


            window.location.href =
                "result.php?attempt_id=" +
                encodeURIComponent(
                    data.attempt_id
                );

        }

        // --------------------------------
        // ERROR
        // --------------------------------

        else {

            alert(
                data.message ||
                "Unable to submit quiz."
            );

            submitted = false;

            submitButton.disabled = false;

            nextButton.disabled = false;

        }

    } catch (error) {

        console.error(error);

        alert(
            "Something went wrong while submitting the quiz."
        );

        submitted = false;

        submitButton.disabled = false;

        nextButton.disabled = false;
    }

}


// ------------------------------------
// START QUIZ
// ------------------------------------

loadQuestion();

updateTimer();

</script>

</body>
</html>