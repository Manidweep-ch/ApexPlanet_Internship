<?php
// Start the session to access session variables.
session_start();
// Check if the user is logged in by verifying if 'user_id' is set in the session.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$Role=$_SESSION['role'];

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
        <div class="card auth-card center">
            <h2 class="heading">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <p>Role: <?php echo $Role; ?></p> 
            <div class="dashboard-actions">
                <div><a class="link-button primary" href="create_posts.php">Create Post</a></div>
                <div><a class="link-button secondary" href="view_posts.php">View Posts</a></div>
                <div><a class="link-button success" href="edit_profile.php">Edit Profile</a></div>
                <div><a class="link-button danger" href="logout.php">Logout</a></div>
            </div>
        </div>
    </div>
</body>
</html>