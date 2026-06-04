<?php
// we start a session to manage user authentication and store user information across different pages of the application. This allows us to keep track of the user's login status and access their information as needed.
session_start();
require_once "db.php";
if(!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }
if($_SERVER["REQUEST_METHOD"]=="POST")
    {
       $title=$_POST["title"];
       $content=$_POST["content"];
       $query="INSERT INTO posts(title,content) VALUES (:title,:content);";
       $stmt=$pdo->prepare($query);
       $stmt->bindParam(":title",$title);
       $stmt->bindParam(":content",$content);
       $stmt->execute();
        echo "Post Created Sucessfully!";
        header("Location: dashboard.php");
        exit();

    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="heading center">Create Post</h2>
            <form action="create_posts.php" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-field">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <button type="submit" class="button primary full-width">Create Post</button>
                <div class="center">
                    <a class="link-button secondary" href="dashboard.php">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>