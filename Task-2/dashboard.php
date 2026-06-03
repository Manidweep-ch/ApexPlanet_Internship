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
    <title>Dashboard</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; background:#f4f4f7; color:#333;">
    <div style="max-width:640px; margin:0 auto; background:#fff; padding:28px; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,0.08); text-align:center;">
        <h2 style="margin-top:0; font-size:28px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <div style="display:flex; flex-direction:column; gap:14px; align-items:center; margin-top:28px;">
            <a href="create_posts.php" style="width:100%; max-width:320px; padding:12px 16px; background:#007bff; color:#fff; text-decoration:none; border-radius:10px;">Create Post</a>
            <a href="view_posts.php" style="width:100%; max-width:320px; padding:12px 16px; background:#17a2b8; color:#fff; text-decoration:none; border-radius:10px;">View Posts</a>
            <a href="logout.php" style="width:100%; max-width:320px; padding:12px 16px; background:#dc3545; color:#fff; text-decoration:none; border-radius:10px;">Logout</a>
        </div>
    </div>
</body>
</html>