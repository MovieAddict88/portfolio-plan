<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>This is the admin dashboard. From here you can manage your portfolio content.</p>

    <nav>
        <ul>
            <li><a href="about_me.php">Manage About Me</a></li>
            <li><a href="skills.php">Manage Skills</a></li>
            <li><a href="experience.php">Manage Experience</a></li>
            <li><a href="projects.php">Manage Projects</a></li>
            <li><a href="documents.php">Manage Documents</a></li>
            <li><a href="change_password.php">Change Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>
</body>
</html>