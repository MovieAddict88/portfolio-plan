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

// Handle form submission for adding/editing projects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $media_url = $_POST['media_url']; // Can be empty

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing project
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, media_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $media_url, $id);
    } else {
        // Add new project
        $stmt = $conn->prepare("INSERT INTO projects (title, description, media_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $media_url);
    }

    if ($stmt->execute()) {
        $message = "Project saved successfully.";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Project deleted successfully.";
    } else {
        $error = "Error deleting project: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all projects
$result = $conn->query("SELECT * FROM projects ORDER BY id DESC");
$projects = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: auto; padding: 2rem; }
        form { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
        input, textarea { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        .actions a { margin-right: 0.5rem; }
        .message { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Projects</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if (isset($message)): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>

        <h2>Add/Edit Project</h2>
        <form action="projects.php" method="post" id="project_form">
            <input type="hidden" name="id" id="proj_id">
            <input type="text" name="title" id="proj_title" placeholder="Project Title" required>
            <textarea name="description" id="proj_description" placeholder="Description" rows="4" required></textarea>
            <input type="text" name="media_url" id="proj_media_url" placeholder="Media URL (e.g., image or video link)">
            <button type="submit">Save Project</button>
        </form>

        <h2>Existing Projects</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Media URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $proj): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proj['title']); ?></td>
                    <td><?php echo htmlspecialchars($proj['media_url']); ?></td>
                    <td class="actions">
                        <a href="#" onclick='editProject(<?php echo json_encode($proj); ?>); return false;'>Edit</a>
                        <a href="projects.php?delete=<?php echo $proj['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editProject(proj) {
            document.getElementById('proj_id').value = proj.id;
            document.getElementById('proj_title').value = proj.title;
            document.getElementById('proj_description').value = proj.description;
            document.getElementById('proj_media_url').value = proj.media_url;
            document.getElementById('project_form').scrollIntoView();
        }
    </script>
</body>
</html>