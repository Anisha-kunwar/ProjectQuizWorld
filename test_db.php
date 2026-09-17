<?php

require_once "config/database.php";

echo "<h1>QuizWorld Database Test</h1>";

if ($conn->connect_error) {
    echo "❌ Database connection failed";
} else {
    echo "✅ Database connected successfully!<br>";

    $result = $conn->query("SELECT COUNT(*) AS total FROM questions");

    if ($result) {
        $row = $result->fetch_assoc();
        echo "Questions in database: " . $row["total"];
    }
}
?>