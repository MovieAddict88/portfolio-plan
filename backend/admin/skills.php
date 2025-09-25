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

// Handle form submission for adding/editing skills
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skill_name = $_POST['skill_name'];
    $level = $_POST['level'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing skill
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE skills SET skill_name = ?, level = ? WHERE id = ?");
        $stmt->bind_param("sii", $skill_name, $level, $id);
    } else {
        // Add new skill
        $stmt = $conn->prepare("INSERT INTO skills (skill_name, level) VALUES (?, ?)");
        $stmt->bind_param("si", $skill_name, $level);
    }

    if ($stmt->execute()) {
        $message = "Skill saved successfully.";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Skill deleted successfully.";
    } else {
        $error = "Error deleting skill: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all skills
$result = $conn->query("SELECT * FROM skills ORDER BY level DESC");
$skills = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: auto; padding: 2rem; }
        form { display: flex; gap: 1rem; margin-bottom: 2rem; }
        input { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.5rem 1rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        .actions a { margin-right: 0.5rem; }
        .message { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Skills</h1>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if (isset($message)): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>

        <h2>Add/Edit Skill</h2>
        <form action="skills.php" method="post">
            <input type="hidden" name="id" id="skill_id">
            <input type="text" name="skill_name" id="skill_name" placeholder="Skill Name" required>
            <input type="number" name="level" id="skill_level" placeholder="Level (1-100)" min="1" max="100" required>
            <button type="submit">Save Skill</button>
        </form>

        <h2>Existing Skills</h2>
        <table>
            <thead>
                <tr>
                    <th>Skill Name</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill): ?>
                <tr>
                    <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                    <td><?php echo htmlspecialchars($skill['level']); ?>%</td>
                    <td class="actions">
                        <a href="#" onclick="editSkill(<?php echo htmlspecialchars(json_encode($skill)); ?>); return false;">Edit</a>
                        <a href="skills.php?delete=<?php echo $skill['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editSkill(skill) {
            document.getElementById('skill_id').value = skill.id;
            document.getElementById('skill_name').value = skill.skill_name;
            document.getElementById('skill_level').value = skill.level;
            window.scrollTo(0, 0);
        }
    </script>
</body>
</html>