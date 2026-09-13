<?php

session_start();

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/login.html");
    exit;
}

$user_id = trim($_POST["user_id"] ?? "");
$password = $_POST["password"] ?? "";

if ($user_id === "") {
    die("User ID is required.");
}

if ($password === "") {
    die("Password is required.");
}

$sql = "SELECT user_id, name, email, password
        FROM users
        WHERE user_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error.");
}

$stmt->bind_param("s", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid User ID or password.");
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    die("Invalid User ID or password.");
}

session_regenerate_id(true);

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];

header("Location: ../dashboard.php");
exit;

?>