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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $education = $_POST['education'];
    $philosophy = $_POST['philosophy'];
    $photo_url = $_POST['photo_url'];

    // Check if about_me data exists
    $result = $conn->query("SELECT id FROM about_me LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $stmt = $conn->prepare("UPDATE about_me SET name = ?, bio = ?, education = ?, philosophy = ?, photo_url = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $bio, $education, $philosophy, $photo_url, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO about_me (name, bio, education, philosophy, photo_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $bio, $education, $philosophy, $photo_url);
    }

    if ($stmt->execute()) {
        $message = "About Me section updated successfully.";
    } else {
        $error = "Error updating About Me section: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing data
$result = $conn->query("SELECT * FROM about_me LIMIT 1");
$about_me = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Me</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: auto; padding: 2rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        input, textarea { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .message { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage About Me</h1>
        <a href="dashboard.php">Back to Dashboard</a>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="about_me.php" method="post">
            <input type="text" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($about_me['name'] ?? ''); ?>" required>
            <input type="text" name="photo_url" placeholder="Photo URL" value="<?php echo htmlspecialchars($about_me['photo_url'] ?? ''); ?>">
            <textarea name="bio" placeholder="Biography" rows="5" required><?php echo htmlspecialchars($about_me['bio'] ?? ''); ?></textarea>
            <textarea name="education" placeholder="Education" rows="3" required><?php echo htmlspecialchars($about_me['education'] ?? ''); ?></textarea>
            <textarea name="philosophy" placeholder="Teaching Philosophy" rows="5" required><?php echo htmlspecialchars($about_me['philosophy'] ?? ''); ?></textarea>
            <button type="submit">Save</button>
        </form>
    </div>
</body>
</html>