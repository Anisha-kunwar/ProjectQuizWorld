<?php

session_start();

require_once "../../config/database.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT 
            s.student_id,
            s.name,
            SUM(qa.score) AS total_score,
            COUNT(qa.attempt_id) AS quiz_taken
        FROM students s
        INNER JOIN quiz_attempts qa
            ON s.student_id = qa.student_id
        GROUP BY s.student_id, s.name
        ORDER BY total_score DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Unable to load leaderboard.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Leaderboard</title>

    <link rel="stylesheet" href="../css/leaderboard.css">

</head>

<body>

<div class="navigation-arrows">

    <a href="result.php" class="arrow-btn">
        &#8592;
    </a>

</div>

<div class="leaderboard-container">

    <h1>Quiz World Leaderboard</h1>

    <table>

        <thead>

            <tr>
                <th>Rank</th>
                <th>User Name</th>
                <th>Score</th>
                <th>Quiz Taken</th>
            </tr>

        </thead>

        <tbody>

        <?php

        $rank = 1;

        while ($player = $result->fetch_assoc()):

        ?>

            <tr>

                <td>
                    <?php echo $rank; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($player['name']); ?>
                </td>

                <td>
                    <?php echo $player['total_score']; ?>
                </td>

                <td>
                    <?php echo $player['quiz_taken']; ?>
                </td>

            </tr>

        <?php

            $rank++;

        endwhile;

        ?>

        </tbody>

    </table>

</div>

</body>
</html>