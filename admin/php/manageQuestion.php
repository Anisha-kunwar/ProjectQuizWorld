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


$action = $_GET["action"] ?? "";


/* =====================================================
   LOAD SUBJECTS
   ===================================================== */

if ($action === "subjects") {

    $sql = "
        SELECT category_id, category_name
        FROM Categories
        ORDER BY category_name ASC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to load subjects."
        ]);
        exit;
    }

    $subjects = [];

    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    echo json_encode([
        "success" => true,
        "subjects" => $subjects
    ]);

    exit;
}


/* =====================================================
   LOAD SETS
   ===================================================== */

if ($action === "sets") {

    $categoryId = $_GET["category_id"] ?? "";


    /* VALIDATE CATEGORY ID */

    if (!filter_var($categoryId, FILTER_VALIDATE_INT)) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid subject ID."
        ]);

        exit;
    }


    $sql = "
        SELECT set_id, set_name
        FROM Question_sets
        WHERE category_id = ?
        ORDER BY set_name ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $categoryId);

    $stmt->execute();

    $result = $stmt->get_result();


    $sets = [];

    while ($row = $result->fetch_assoc()) {
        $sets[] = $row;
    }


    echo json_encode([
        "success" => true,
        "sets" => $sets
    ]);

    exit;
}


/* ---------- INVALID ACTION ---------- */

echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>