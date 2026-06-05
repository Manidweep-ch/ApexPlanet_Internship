<?php
session_start();
require_once "db.php";
$error="";
if(!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }

$id=(int)($_GET["id"]??0);
$query="SELECT * FROM posts Where id=:id;";
$stmt=$pdo->prepare($query);
$stmt->bindParam(":id",$id);
$stmt->execute();
$post=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$post)
    {
        die("Post Not Found");
    }
if($_SESSION['role']!='admin' && $post['user_id']!=$_SESSION['user_id'])
{
    die("Access Denied");
}
if($_SERVER["REQUEST_METHOD"]=='POST')
    {
        $title = trim((string)($_POST["title"] ?? ""));
        $content = trim((string)($_POST["content"] ?? ""));
        if(empty($title))
        {
            $error="Title shouldn't be empty";
        }
        else if(empty($content))
        {
            $error="Content shouldn't be empty";
        }
        else if(strlen($title)<3 || strlen($title)>255)
        {
            $error="Title should be between 3 and 255 characters";
        }
        else if(strlen($content)<10)
        {
            $error="Content should contain at least 10 characters";
        }
        if(empty($error))
        {
        $query="UPDATE posts SET title=:title,content=:content WHERE id=:id;";
        $stmt=$pdo->prepare($query);
        $stmt->bindParam(":title",$title);
        $stmt->bindParam(":content",$content);
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        header("Location: view_posts.php");
        exit();
        }
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
            <?php if(!empty($error)): ?>
                <div class="error">*<?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="edit_post.php?id=<?php echo $id; ?>" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required minlength="3" maxlength="255" value="<?php echo htmlspecialchars($title ?? $post['title']); ?>">
                </div>
                <div class="form-field">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required minlength="10"><?php echo htmlspecialchars($content ?? $post['content']); ?></textarea>
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
