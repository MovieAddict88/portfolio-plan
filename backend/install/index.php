<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        form { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input { display: block; width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <form action="setup.php" method="post">
        <h2>Database Configuration</h2>
        <input type="text" name="db_host" placeholder="Database Host" required>
        <input type="text" name="db_user" placeholder="Database Username" required>
        <input type="password" name="db_pass" placeholder="Database Password">
        <input type="text" name="db_name" placeholder="Database Name" required>
        <button type="submit">Install</button>
    </form>
</body>
</html>