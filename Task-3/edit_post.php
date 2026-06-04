<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }
$id=$_GET["id"];
$query="SELECT * FROM posts Where id=:id;";
$stmt=$pdo->prepare($query);
$stmt->bindParam(":id",$id);
$stmt->execute();
$post=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$post)
    {
        die("Post Not Found");
    }

if($_SERVER["REQUEST_METHOD"]=='POST')
    {
        $title=$_POST["title"];
        $content=$_POST["content"];
        $query="UPDATE posts SET title=:title,content=:content WHERE id=:id;";
        $stmt=$pdo->prepare($query);
        $stmt->bindParam(":title",$title);
        $stmt->bindParam(":content",$content);
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        echo "Post Updated Sucessfully!";
        header("Location: view_posts.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="heading center">Edit Post</h2>
            <form action="edit_post.php?id=<?php echo $id; ?>" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                <div class="form-field">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <button type="submit" class="button success full-width">Update Post</button>
                <div class="center">
                    <a class="link-button secondary" href="view_posts.php">Back to View Posts</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
