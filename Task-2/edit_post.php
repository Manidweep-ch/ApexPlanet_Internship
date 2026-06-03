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
<html>
<head>
    <title>Edit Post</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; background:#f4f4f7; color:#333;">
    <div style="max-width:680px; margin:0 auto; background:#fff; padding:28px; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,0.08);">
        <h2 style="margin-top:0; text-align:center;">Edit Post</h2>
        <form action="edit_post.php?id=<?php echo $id; ?>" method="POST" style="display:flex; flex-direction:column; gap:14px;">
            <label for="title" style="font-weight:600;">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required style="width:100%; padding:12px 14px; border:1px solid #ccc; border-radius:10px; font-size:16px;" />
            <label for="content" style="font-weight:600;">Content:</label>
            <textarea id="content" name="content" required style="width:100%; min-height:140px; padding:12px 14px; border:1px solid #ccc; border-radius:10px; font-size:16px;"><?php echo htmlspecialchars($post['content']); ?></textarea>
            <button type="submit" style="padding:12px 18px; background:#28a745; color:#fff; border:none; border-radius:10px; font-size:16px; cursor:pointer;">Update Post</button>
            <div style="text-align:center; margin-top:8px;"><a href="view_posts.php" style="color:#007bff; text-decoration:none;">Back to View Posts</a></div>
        </form>
    </div>
</body>
</html>
