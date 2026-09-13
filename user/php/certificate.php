<?php

session_start();

require_once "../../config/database.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['attempt_id']) || !ctype_digit($_GET['attempt_id'])) {
    die("Invalid certificate request.");
}

$attempt_id = (int) $_GET['attempt_id'];
$student_id = (int) $_SESSION['student_id'];

$sql = "SELECT
            qa.attempt_id,
            qa.score,
            qa.total_questions,
            qa.date_attempted,
            s.name,
            qs.set_name,
            c.subject_name
        FROM quiz_attempts qa
        INNER JOIN students s
            ON qa.student_id = s.student_id
        INNER JOIN question_sets qs
            ON qa.set_id = qs.set_id
        INNER JOIN categories c
            ON qs.category_id = c.category_id
        WHERE qa.attempt_id = ?
        AND qa.student_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $attempt_id, $student_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Certificate not found.");
}

$data = $result->fetch_assoc();

$percentage = $data['total_questions'] > 0
    ? round(($data['score'] / $data['total_questions']) * 100, 2)
    : 0;

$certificateID = "QW-" . str_pad($data['attempt_id'], 6, "0", STR_PAD_LEFT);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QuizWorld Certificate</title>

    <link rel="stylesheet" href="../css/certificate.css">

</head>

<body>

<div class="certificate-container">

    <img
        src="../images/certificate2.png"
        class="certificate-img"
        alt="QuizWorld Certificate"
    >

    <h1>
        <?php echo htmlspecialchars($data['name']); ?>
    </h1>

    <h2>
        <?php echo htmlspecialchars($data['subject_name']); ?>
    </h2>

    <p>
        Score:
        <?php echo $data['score']; ?>
        /
        <?php echo $data['total_questions']; ?>
        (<?php echo $percentage; ?>%)
    </p>

    <p>
        Date:
        <?php echo date("F d, Y", strtotime($data['date_attempted'])); ?>
    </p>

    <p>
        Certificate ID:
        <?php echo $certificateID; ?>
    </p>

</div>

</body>
</html>