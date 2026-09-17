
<?php

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("ERROR: Invalid request.");
}

$name = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    die("ERROR: All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("ERROR: Invalid email address.");
}

if (strlen($password) < 8) {
    die("ERROR: Password must contain at least 8 characters.");
}

if ($password !== $confirmPassword) {
    die("ERROR: Passwords do not match.");
}

/* Check whether email already exists */
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");

if (!$check) {
    die("PREPARE ERROR: " . $conn->error);
}

$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("ERROR: This email is already registered.");
}

$check->close();

/* Hash password */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* Insert user */
$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    die("PREPARE ERROR: " . $conn->error);
}

$stmt->bind_param("sss", $name, $email, $hashedPassword);

if (!$stmt->execute()) {
    die("INSERT ERROR: " . $stmt->error);
}

$newUserId = $conn->insert_id;

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
</head>

<body>

    <h2>Registration Successful!</h2>

    <p>Name: <?php echo htmlspecialchars($name); ?></p>

    <p>
        Your User ID:
        <strong><?php echo $newUserId; ?></strong>
    </p>

    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <p>Your account has been created successfully.</p>

    <p>
        <a href="../html/login.html">Go to Login</a>
    </p>

</body>
</html>

