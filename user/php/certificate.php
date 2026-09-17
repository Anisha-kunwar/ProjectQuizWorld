<?php

session_start();

require_once "../../config/database.php";

/* --------------------------------
   CHECK LOGIN
-------------------------------- */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

/* --------------------------------
   CHECK ATTEMPT ID
-------------------------------- */
if (
    !isset($_GET["attempt_id"]) ||
    !ctype_digit($_GET["attempt_id"])
) {
    die("Invalid certificate request.");
}

$attempt_id = (int) $_GET["attempt_id"];
$user_id = (int) $_SESSION["user_id"];

/* --------------------------------
   GET QUIZ + USER + CATEGORY
-------------------------------- */
$sql = "
    SELECT
        qa.attempt_id,
        qa.score,
        qa.total_questions,
        qa.date_attempted,

        u.user_id,
        u.name,
        u.email,

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

    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ii",
    $attempt_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Certificate not found.");
}

$data = $result->fetch_assoc();

$stmt->close();

/* --------------------------------
   SCORE
-------------------------------- */
$score = (int) $data["score"];
$total = (int) $data["total_questions"];

if ($total <= 0) {
    die("Invalid quiz result.");
}

$percentage = round(
    ($score / $total) * 100,
    2
);

/* --------------------------------
   CERTIFICATE ELIGIBILITY
-------------------------------- */

if ($total != 10 || $score < 8) {
    die(
        "Certificate is available only for scores of 8/10, 9/10, or 10/10."
    );
}

/* --------------------------------
   CERTIFICATE ID
-------------------------------- */

$certificateID = "QW-" . str_pad(
    $data["attempt_id"],
    6,
    "0",
    STR_PAD_LEFT
);

/* --------------------------------
   DATE
-------------------------------- */

$certificateDate = date(
    "F d, Y",
    strtotime($data["date_attempted"])
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

    <title>
        QuizWorld Certificate
    </title>

    <link
        rel="stylesheet"
        href="../css/certificate.css"
    >

</head>

<body>

<div class="page">

    <div class="certificate">

        <!-- TOP -->

        <div class="certificate-top">

            <div class="logo">
                QUIZ<span>WORLD</span>
            </div>

            <div class="certificate-label">
                CERTIFICATE
            </div>

        </div>


        <!-- TITLE -->

        <h1>
            Certificate of Achievement
        </h1>

        <p class="presented">
            This certificate is proudly presented to
        </p>


        <!-- USER NAME -->

        <h2 class="student-name">
            <?= htmlspecialchars($data["name"]) ?>
        </h2>


        <div class="line"></div>


        <!-- DESCRIPTION -->

        <p class="description">

            For successfully completing the

            <strong>
                <?= htmlspecialchars($data["subject_name"]) ?>
            </strong>

            quiz on QuizWorld with an outstanding performance.

        </p>


        <!-- SCORE -->

        <div class="score-section">

            <div class="score-box">

                <span class="score-number">
                    <?= $score ?>/<?= $total ?>
                </span>

                <span class="score-label">
                    SCORE
                </span>

            </div>


            <div class="score-box">

                <span class="score-number">
                    <?= $percentage ?>%
                </span>

                <span class="score-label">
                    PERCENTAGE
                </span>

            </div>

        </div>


        <!-- DETAILS -->

        <div class="details">

            <div>

                <span>
                    CATEGORY
                </span>

                <strong>
                    <?= htmlspecialchars($data["subject_name"]) ?>
                </strong>

            </div>


            <div>

                <span>
                    DATE
                </span>

                <strong>
                    <?= htmlspecialchars($certificateDate) ?>
                </strong>

            </div>


            <div>

                <span>
                    CERTIFICATE ID
                </span>

                <strong>
                    <?= htmlspecialchars($certificateID) ?>
                </strong>

            </div>

        </div>


        <!-- FOOTER -->

        <div class="certificate-footer">

            <div class="signature">

                <div class="signature-line"></div>

                <span>
                    QuizWorld
                </span>

                <small>
                    Authorized Certificate
                </small>

            </div>


            <div class="seal">
                QW
            </div>

        </div>

    </div>


    <!-- BUTTONS -->

    <div class="certificate-actions">

        <button
            type="button"
            onclick="window.print()"
        >
            🖨 Print Certificate
        </button>


        <a href="../html/dashboard.html">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>

</html>