<?php
header('Content-Type: application/json');
require_once '../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$data = [];

// Fetch About Me
$data['about_me'] = $conn->query("SELECT * FROM about_me LIMIT 1")->fetch_assoc();

// Fetch Skills
$data['skills'] = $conn->query("SELECT * FROM skills ORDER BY level DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch Experience
$data['experience'] = $conn->query("SELECT * FROM experience ORDER BY year DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch Projects
$data['projects'] = $conn->query("SELECT * FROM projects ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch Documents
$data['documents'] = $conn->query("SELECT id, file_name FROM documents ORDER BY uploaded_at DESC")->fetch_all(MYSQLI_ASSOC);


echo json_encode($data);

$conn->close();
?>