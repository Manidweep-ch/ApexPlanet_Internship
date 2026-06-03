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
<html>
<head>
    <title>Create Post</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; background:#f4f4f7; color:#333;">
    <div style="max-width:680px; margin:0 auto; background:#fff; padding:28px; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,0.08);">
        <h2 style="margin-top:0; text-align:center;">Create Post</h2>
        <form action="create_posts.php" method="POST" style="display:flex; flex-direction:column; gap:14px;">
            <label for="title" style="font-weight:600;">Title:</label>
            <input type="text" id="title" name="title" required style="width:100%; padding:12px 14px; border:1px solid #ccc; border-radius:10px; font-size:16px;" />
            <label for="content" style="font-weight:600;">Content:</label>
            <textarea id="content" name="content" required style="width:100%; min-height:140px; padding:12px 14px; border:1px solid #ccc; border-radius:10px; font-size:16px;"></textarea>
            <button type="submit" style="padding:12px 18px; background:#007bff; color:#fff; border:none; border-radius:10px; font-size:16px; cursor:pointer;">Create Post</button>
            <div style="text-align:center; margin-top:8px;"><a href="dashboard.php" style="color:#007bff; text-decoration:none;">Back to Dashboard</a></div>
        </form>
    </div>
</body>
</html>