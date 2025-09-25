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

// Handle form submission for adding/editing experience
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $institution = $_POST['institution'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing experience
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE experience SET title = ?, institution = ?, year = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $institution, $year, $description, $id);
    } else {
        // Add new experience
        $stmt = $conn->prepare("INSERT INTO experience (title, institution, year, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $institution, $year, $description);
    }

    if ($stmt->execute()) {
        $message = "Experience saved successfully.";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM experience WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Experience deleted successfully.";
    } else {
        $error = "Error deleting experience: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all experiences
$result = $conn->query("SELECT * FROM experience ORDER BY year DESC");
$experiences = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience</title>
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
        <h1>Manage Experience</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if (isset($message)): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>

        <h2>Add/Edit Experience</h2>
        <form action="experience.php" method="post" id="experience_form">
            <input type="hidden" name="id" id="exp_id">
            <input type="text" name="title" id="exp_title" placeholder="Job Title" required>
            <input type="text" name="institution" id="exp_institution" placeholder="Institution/Company" required>
            <input type="text" name="year" id="exp_year" placeholder="Year (e.g., 2020-Present)" required>
            <textarea name="description" id="exp_description" placeholder="Description" rows="4" required></textarea>
            <button type="submit">Save Experience</button>
        </form>

        <h2>Existing Experience</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Institution</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($experiences as $exp): ?>
                <tr>
                    <td><?php echo htmlspecialchars($exp['title']); ?></td>
                    <td><?php echo htmlspecialchars($exp['institution']); ?></td>
                    <td><?php echo htmlspecialchars($exp['year']); ?></td>
                    <td class="actions">
                        <a href="#" onclick='editExperience(<?php echo json_encode($exp); ?>); return false;'>Edit</a>
                        <a href="experience.php?delete=<?php echo $exp['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editExperience(exp) {
            document.getElementById('exp_id').value = exp.id;
            document.getElementById('exp_title').value = exp.title;
            document.getElementById('exp_institution').value = exp.institution;
            document.getElementById('exp_year').value = exp.year;
            document.getElementById('exp_description').value = exp.description;
            document.getElementById('experience_form').scrollIntoView();
        }
    </script>
</body>
</html>