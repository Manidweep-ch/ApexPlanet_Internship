<?php
// Start the session to access session variables.
session_start();
// Check if the user is logged in by verifying if 'user_id' is set in the session.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card center">
            <h2 class="heading">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <div class="actions">
                <a class="link-button primary" href="create_posts.php">Create Post</a>
                <a class="link-button secondary" href="view_posts.php">View Posts</a>
                <a class="link-button danger" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>