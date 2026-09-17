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

$user_id = (int)$_SESSION["user_id"];

// ------------------------------------
// GET ATTEMPT ID
// ------------------------------------

if (
    !isset($_GET["attempt_id"]) ||
    !ctype_digit($_GET["attempt_id"])
) {
    die("Invalid quiz attempt.");
}

$attempt_id = (int)$_GET["attempt_id"];

// ------------------------------------
// GET RESULT
// ------------------------------------

$sql = "SELECT
            qa.attempt_id,
            qa.score,
            qa.total_questions,
            qa.time_taken_seconds,
            qa.date_attempted,
            u.name,
            qs.set_name,
            c.subject_name
        FROM quiz_attempts qa

        INNER JOIN users u
            ON qa.user_id = u.user_id

        INNER JOIN question_sets qs
            ON qa.set_id = qs.set_id

        INNER JOIN categories c
            ON qs.category_id = c.category_id

        WHERE qa.attempt_id = ?
        AND qa.user_id = ?

        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

// Both are INT
$stmt->bind_param(
    "ii",
    $attempt_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid quiz attempt.");
}

$data = $result->fetch_assoc();

$stmt->close();

// ------------------------------------
// RESULT DATA
// ------------------------------------

$score = (int)$data["score"];

$total = (int)$data["total_questions"];

$percentage = 0;

if ($total > 0) {
    $percentage = round(
        ($score / $total) * 100,
        2
    );
}

// ------------------------------------
// CERTIFICATE ELIGIBILITY
// ------------------------------------

$certificateEligible = (
    $total == 10 &&
    $score >= 8
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

    <title>QuizWorld Result</title>

    <link
        rel="stylesheet"
        href="../css/result.css">
    >

</head>

<body>

<div class="result-container">

    <div class="result-box">

        <h1>QUIZ COMPLETED</h1>

        <h2>
            <?= htmlspecialchars($data["name"]) ?>
        </h2>

        <p>
            Category:
            <?= htmlspecialchars($data["subject_name"]) ?>
        </p>

        <div class="score">

            <?= $score ?>

            /

            <?= $total ?>

        </div>

        <div class="percentage">

            <?= $percentage ?>%

        </div>

        <?php if ($percentage >= 80): ?>

            <p>
                🎉 Excellent work!
            </p>

        <?php elseif ($percentage >= 60): ?>

            <p>
                👍 Good job! Keep practicing.
            </p>

        <?php else: ?>

            <p>
                Keep practicing and try again!
            </p>

        <?php endif; ?>


        <!-- CERTIFICATE -->

        <?php if ($certificateEligible): ?>

            <a
                href="certificate.php?attempt_id=<?= $attempt_id ?>"
                class="certificate-button"
            >
                🏆 View Certificate
            </a>

        <?php endif; ?>


        <!-- TAKE ANOTHER QUIZ -->

        <a
            href="../php/categories.php"
            class="quiz-button"
        >
            Take Another Quiz
        </a>


        <!-- DASHBOARD -->

        <a
            href="../php/dashboard.php"
            class="dashboard-button"
        >
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>