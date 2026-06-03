<?php
// we start a session to manage user authentication and store user information across different pages of the application
session_start();
require_once "db.php";
// we check if the user is logged in by verifying if 'user_id' is set in the session. If the user is not logged in, we redirect them to the login page using header("Location: login.php") and exit the script to prevent further execution.
if(!isset($_SESSION['user_id']))
    {

        header("Location: login.php");
        exit();
    }

?>
<html>
<head>
    <title>View Posts</title>
</head>
<body style="margin:0; padding:20px; font-family: Arial;background:#f4f4f7; color:#333;">
    <div style="max-width:960px; margin:0 auto;">
        <h2 style="text-align:center; margin-bottom:20px;">View Posts</h2>
        <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-bottom:24px;">
            <a href="create_posts.php" style="padding:10px 18px; background:#007bff; color:#fff; text-decoration:none; border-radius:8px;">Create new Post</a>
            <a href="dashboard.php" style="padding:10px 18px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">Back to Dashboard</a>
        </div>
        <?php
        // we prepare and execute a SQL query to fetch all posts from the 'posts' table in the database. We use PDO to prepare the query and execute it, and then fetch all the results as an associative array.
        $query="SELECT * FROM posts;";
        $stmt=$pdo->prepare($query);
        $stmt->execute();
        $posts=$stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($posts as $post)
            {
                echo '<div style="background:#fff; border-radius:12px; padding:18px; box-shadow:0 4px 16px rgba(0,0,0,0.08); margin-bottom:20px;">';
                echo '<h3 style="margin:0 0 10px; font-size:22px;">' . htmlspecialchars($post["title"]) . '</h3>';
                echo '<p style="margin:0 0 10px; line-height:1.6;">' . htmlspecialchars($post["content"]) . '</p>';
                echo '<p style="margin:0 0 10px; font-size:12px;">Posted on: ' . date('Y-m-d H:i:s',strtotime($post["created_at"])) . '</p>';
                echo '<div style="display:flex; flex-wrap:wrap; gap:10px;">';
                echo '<a href="edit_post.php?id=' . $post['id'] . '" style="padding:8px 14px; background:#28a745; color:#fff; text-decoration:none; border-radius:8px;">Edit</a>';
                echo '<a href="delete_post.php?id=' . $post['id'] . '" onclick="return confirm(\'Are you sure?\');" style="padding:8px 14px; background:#dc3545; color:#fff; text-decoration:none; border-radius:8px;">Delete</a>';
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>
</body>
</html>