<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file_name = basename($_FILES['document']['name']);
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
        $stmt = $conn->prepare("INSERT INTO documents (file_name, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $file_name, $file_path);
        if ($stmt->execute()) {
            $message = "Document uploaded successfully.";
        } else {
            $error = "Error saving to database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error uploading file.";
    }
}

// Handle password generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_password'])) {
    $document_id = $_POST['document_id'];
    $expiration_date = $_POST['expiration_date'];
    $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

    $stmt = $conn->prepare("INSERT INTO passwords (code, document_id, expiration_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $code, $document_id, $expiration_date);
    if ($stmt->execute()) {
        $message = "Password generated successfully: <strong>$code</strong>";
    } else {
        $error = "Error generating password: " . $stmt->error;
    }
    $stmt->close();
}

// Handle deletion
if (isset($_GET['delete_doc'])) {
    $id = $_GET['delete_doc'];
    // First, get file path to delete the file
    $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    // Delete from DB (passwords will be cascaded)
    $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "Document and associated passwords deleted.";
}

// Fetch documents and passwords
$documents = $conn->query("SELECT * FROM documents ORDER BY uploaded_at DESC")->fetch_all(MYSQLI_ASSOC);
$passwords = $conn->query("SELECT p.*, d.file_name FROM passwords p JOIN documents d ON p.document_id = d.id ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents & Passwords</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 900px; margin: auto; padding: 2rem; }
        form { margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem; border-radius: 4px; }
        input, select, button { padding: 0.5rem; margin-right: 0.5rem; }
        button { cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        .message { padding: 1rem; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 1rem; }
        .error { padding: 1rem; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Documents & Passwords</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if (isset($message)): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
    <?php if (isset($error)): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

    <h2>Upload Document</h2>
    <form action="documents.php" method="post" enctype="multipart/form-data">
        <input type="file" name="document" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Generate Download Password</h2>
    <form action="documents.php" method="post">
        <select name="document_id" required>
            <option value="">-- Select Document --</option>
            <?php foreach ($documents as $doc): ?>
                <option value="<?php echo $doc['id']; ?>"><?php echo htmlspecialchars($doc['file_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="datetime-local" name="expiration_date" required>
        <button type="submit" name="generate_password">Generate</button>
    </form>

    <h2>Uploaded Documents</h2>
    <table>
        <thead><tr><th>File Name</th><th>Uploaded At</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($documents as $doc): ?>
            <tr>
                <td><?php echo htmlspecialchars($doc['file_name']); ?></td>
                <td><?php echo $doc['uploaded_at']; ?></td>
                <td><a href="documents.php?delete_doc=<?php echo $doc['id']; ?>" onclick="return confirm('Are you sure? This will also delete all associated passwords.')">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Generated Passwords</h2>
    <table>
        <thead><tr><th>Code</th><th>Document</th><th>Expires At</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($passwords as $pass): ?>
            <tr>
                <td><?php echo htmlspecialchars($pass['code']); ?></td>
                <td><?php echo htmlspecialchars($pass['file_name']); ?></td>
                <td><?php echo $pass['expiration_date']; ?></td>
                <td><?php echo $pass['is_active'] ? 'Active' : 'Inactive'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>