<?php

session_start();

require_once "connection.php";

header("Content-Type: application/json");


/* ---------- ADMIN VALIDATION ---------- */

if (!isset($_SESSION["admin_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized. Please login."
    ]);
    exit;
}


/* ---------- GET CATEGORIES ---------- */

$sql = "
    SELECT category_id, category_name
    FROM Categories
    ORDER BY category_name ASC
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to load categories."
    ]);
    exit;
}


$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}


/* ---------- RESPONSE ---------- */

echo json_encode([
    "success" => true,
    "categories" => $categories
]);

?>