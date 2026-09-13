<?php

$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;
$percentage = isset($_GET['percentage']) ? floatval($_GET['percentage']) : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Quiz Result</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #090A0F;
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .result-container {
            width: 500px;
            max-width: 90%;
            background: #11131C;
            padding: 40px;
            text-align: center;
            border-radius: 15px;
            border: 2px solid #2555FF;
            box-shadow: 0 0 25px rgba(37, 85, 255, 0.25);
        }

        h1 {
            color: #FFFFFF;
            margin-bottom: 25px;
        }

        .score {
            font-size: 50px;
            font-weight: bold;
            color: #FF2B4A;
            margin: 20px 0;
        }

        .percentage {
            font-size: 28px;
            margin-bottom: 25px;
            color: #FFFFFF;
        }

        .message {
            font-size: 18px;
            color: #A0A5B5;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #FF2B4A;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn:hover {
            background: #FF3B5C;
        }

    </style>

</head>

<body>

    <div class="result-container">

        <h1>🎉 Quiz Completed!</h1>

        <div class="score">
            <?= $score ?> / <?= $total ?>
        </div>

        <div class="percentage">
            <?= $percentage ?>%
        </div>

        <div class="message">

            <?php

            if ($percentage >= 80) {

                echo "Excellent work! 🔥";

            } elseif ($percentage >= 60) {

                echo "Great job! Keep improving! 💪";

            } elseif ($percentage >= 40) {

                echo "Good effort! Keep practicing! 📚";

            } else {

                echo "Don't give up! Try again and improve! 🌟";

            }

            ?>

        </div>

        <a href="categories.php" class="btn">
            Take Another Quiz
        </a>

    </div>

</body>

</html>