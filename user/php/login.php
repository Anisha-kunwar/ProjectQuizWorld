<?php

session_start();

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/login.html");
    exit;
}

$user_id = trim($_POST["user_id"] ?? "");
$password = $_POST["password"] ?? "";

if ($user_id === "" || $password === "") {
    die("ERROR: User ID and password are required.");
}

$stmt = $conn->prepare(
    "SELECT user_id, name, email, password
     FROM users
     WHERE user_id = ?
     LIMIT 1"
);

if (!$stmt) {
    die("PREPARE ERROR: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid User ID or password.");
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    die("Invalid User ID or password.");
}

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["name"] = $user["name"];
$_SESSION["email"] = $user["email"];

header("Location: ../php/dashboard.php");
exit;

?>