<?php

session_start();

require_once "../../config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Get logged-in user's information
$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["name"] ?? "User";

// Default dashboard values
$total_quizzes = 0;
$quizzes_taken = 0;
$average_score = 0;
$rank = "-";

// Get total question sets
$sql = "SELECT COUNT(*) AS total FROM question_sets";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_quizzes = $row["total"];
}

// Check if quiz_attempts table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'quiz_attempts'");

if ($tableCheck && $tableCheck->num_rows > 0) {

    // Number of quizzes taken
    $sql = "SELECT COUNT(*) AS total
            FROM quiz_attempts
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $quizzes_taken = $row["total"] ?? 0;
        }

        $stmt->close();
    }

    // Calculate average score
    $sql = "SELECT AVG(
                CASE
                    WHEN total_questions > 0
                    THEN (score / total_questions) * 100
                    ELSE 0
                END
            ) AS average
            FROM quiz_attempts
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();

            if ($row["average"] !== null) {
                $average_score = round($row["average"], 1);
            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - QuizWorld</title>

    <link rel="stylesheet" href="../css/dashboard.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <h2>QUIZWORLD</h2>

        <a href="dashboard.php" class="back-btn">
            ← Dashboard
        </a>

        <ul>

            <a href="../php/categories.php">
                <li>Categories</li>
            </a>

            <a href="../php/categories.php">
                <li>Quiz</li>
            </a>

            <a href="leaderboard.html">
                <li>Leaderboard</li>
            </a>

            <a href="profile.html">
                <li>Profile</li>
            </a>

            <a href="certificate.html">
                <li>Certificate</li>
            </a>

            <a href="../php/logout.php">
                <li id="logout">Logout</li>
            </a>

        </ul>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <!-- WELCOME -->

        <section class="welcome">

            <h1>
                Welcome back, <?= htmlspecialchars($user_name) ?>
            </h1>

            <p>
                Ready to test your knowledge?
            </p>

        </section>


        <!-- STATISTICS -->

        <div class="card-container">

            <div class="card total">

                <h3>Total Quizzes</h3>

                <p><?= $total_quizzes ?></p>

            </div>


            <div class="card taken">

                <h3>Quizzes Taken</h3>

                <p><?= $quizzes_taken ?></p>

            </div>


            <div class="card score">

                <h3>Average Score</h3>

                <p><?= $average_score ?>%</p>

            </div>


            <div class="card rank">

                <h3>Your Rank</h3>

                <p><?= htmlspecialchars((string)$rank) ?></p>

            </div>

        </div>


        <!-- CONTINUE QUIZ -->

        <section class="continue-quiz">

            <h2>Ready for your next challenge?</h2>

            <p>
                Choose a category and start your quiz.
            </p>

            <p>
                Test your knowledge and improve your score.
            </p>

            <button
                type="button"
                onclick="window.location.href='../php/categories.php'">

                Continue Quiz

            </button>

        </section>


        <!-- PROGRESS -->

        <section class="progress-section">

            <h2>Your Progress</h2>

            <div class="progress-cards">

                <div class="progress-box">

                    User ID:
                    <?= htmlspecialchars((string)$user_id) ?>

                </div>


                <div class="progress-box">

                    Quizzes Completed:
                    <?= $quizzes_taken ?>

                </div>


                <div class="progress-box">

                    Keep practicing!

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>