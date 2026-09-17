<?php

session_start();

require_once "../../config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

// Get categories
$sql = "SELECT category_id, subject_name
        FROM categories
        ORDER BY category_id ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Unable to load categories: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categories - QuizWorld</title>

    <link rel="stylesheet" href="../css/categories.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>

<!-- NAVIGATION -->
<div class="navigation-arrows">

    <a
        href="../php/dashboard.php"
        class="arrow-btn"
        aria-label="Back to Dashboard">
        ←
    </a>

    

</div>


<!-- MAIN CONTAINER -->
<div class="container">

    <h1>Choose Your Category</h1>

    <p class="subtitle">
        Select a category and test your knowledge.
    </p>


    <div class="category-container">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($category = $result->fetch_assoc()): ?>

                <?php
                    $category_id = (int)$category["category_id"];
                    $subject = $category["subject_name"];
                ?>

                <div class="card">

                    <h2>
                        <?= htmlspecialchars($subject) ?>
                    </h2>

                    <p>
                        Test your knowledge in
                        <?= htmlspecialchars($subject) ?>.
                    </p>

                    <button
                        type="button"
                        onclick="window.location.href='quiz.php?category_id=<?= $category_id ?>'">
                        Start Quiz
                    </button>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-categories">
                <p>No categories available.</p>
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>