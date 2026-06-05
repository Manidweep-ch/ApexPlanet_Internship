<?php
// we start a session to manage user authentication and store user information across different pages of the application. This allows us to keep track of the user's login status and access their information as needed.
session_start();
require_once "db.php";
$error="";
if(!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }
$user_id=$_SESSION['user_id'];
if($_SERVER["REQUEST_METHOD"]=="POST")
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
       $query="INSERT INTO posts(title,content,user_id) VALUES (:title,:content,:user_id);";
       $stmt=$pdo->prepare($query);
       $stmt->bindParam(":title",$title);
       $stmt->bindParam(":content",$content);
       $stmt->bindParam(":user_id",$user_id);
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
    <title>Create Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="heading center">Create Post</h2>
            <?php
            if(!empty($error))
            {
                echo "<div class='error'>*" . htmlspecialchars($error) . "</div>";
            }
            ?>
            <form action="create_posts.php" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required minlength="3" maxlength="255" value="<?php echo htmlspecialchars($title ?? ''); ?>">
                </div>
                <div class="form-field">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required minlength="10"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
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