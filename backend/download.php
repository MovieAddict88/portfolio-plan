<?php
require_once 'config.php';

if (!isset($_GET['doc_id']) || !isset($_GET['password'])) {
    die('Invalid request.');
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$document_id = intval($_GET['doc_id']);
$code = $_GET['password'];

// Find an active password that matches the code and document_id and is not expired
$stmt = $conn->prepare("SELECT p.id, d.file_path, d.file_name FROM passwords p
                        JOIN documents d ON p.document_id = d.id
                        WHERE p.document_id = ? AND p.code = ? AND p.is_active = 1 AND p.expiration_date > NOW()");
$stmt->bind_param("is", $document_id, $code);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($pid, $file_path, $file_name);
    $stmt->fetch();

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        die('File not found.');
    }
} else {
    die('Invalid or expired password.');
}

$stmt->close();
$conn->close();
?>