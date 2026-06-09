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

$User_id = $_SESSION['user_id'];
$error = "";
$success = "";

// we retrieve the post_id from the GET parameters and convert it to an integer. This allows us to fetch the specific post for which we want to display comments.
$post_id = (int)($_GET["post_id"] ?? 0);

// we execute a SQL query to fetch the post with the specified post_id from the database. We then fetch the result as an associative array.
$query = "SELECT * FROM posts WHERE id = :post_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":post_id", $post_id);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// we check if the post exists. If it does not exist, we display an error message and exit the script to prevent further execution.
if(!$post)
{
    die("Post Not Found");
}

// we handle the POST request when the user submits a new comment. We retrieve the comment text from the POST parameters and trim any whitespace. We then validate the comment to ensure it is not empty and meets the minimum length requirement.
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $comment_text = trim((string)($_POST["comment"] ?? ""));
    
    // we validate the comment text to ensure it is not empty and contains at least a few characters
    if(empty($comment_text))
    {
        $error = "Comment cannot be empty";
    }
    else if(strlen($comment_text) < 2)
    {
        $error = "Comment must contain at least 2 characters";
    }
    
    // if there are no errors, we insert the comment into the database using a prepared statement
    if(empty($error))
    {
        $insert_query = "INSERT INTO comments (comment, user_id, post_id) VALUES (:comment, :user_id, :post_id)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->bindParam(":comment", $comment_text);
        $insert_stmt->bindParam(":user_id", $User_id);
        $insert_stmt->bindParam(":post_id", $post_id);
        $insert_stmt->execute();
        $success = "Comment added successfully";
    }
}

// we execute a SQL query to fetch all comments for the specified post, ordered by creation date in descending order
$comments_query = "SELECT c.id, c.comment, c.user_id, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = :post_id ORDER BY c.created_at DESC";
$comments_stmt = $pdo->prepare($comments_query);
$comments_stmt->bindParam(":post_id", $post_id);
$comments_stmt->execute();
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

// we fetch the username of the post creator so we can display it
$post_creator_query = "SELECT username FROM users WHERE id = :user_id";
$post_creator_stmt = $pdo->prepare($post_creator_query);
$post_creator_stmt->bindParam(":user_id", $post['user_id']);
$post_creator_stmt->execute();
$post_creator = $post_creator_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- we display the post details including the title, content, creation date, and author -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="center" style="margin-bottom: 20px;">
                <a class="link-button secondary" href="view_posts.php">Back to Posts</a>
            </div>
            <h2><?php echo htmlspecialchars($post["title"]); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($post["content"])); ?></p>
            <p class="post-meta">Posted by: <?php echo htmlspecialchars($post_creator['username']); ?> on <?php echo date('Y-m-d H:i:s', strtotime($post["created_at"])); ?></p>
        </div>

        <!-- we display the comments section with a heading and a list of all comments for the post -->
        <div class="card" style="margin-bottom: 30px;">
            <h3 class="center">Comments (<?php echo count($comments); ?>)</h3>
            
            <?php
            // we display all comments for the post, including the comment text, author username, creation date, and action buttons for editing and deleting the comment
            if(empty($comments))
            {
                echo "<p class='center'>No comments yet. Be the first to comment!</p>";
            }
            else
            {
                foreach($comments as $comment)
                {
                    echo '<div class="post-card" style="margin-bottom: 15px;">';
                    echo '<p>' . nl2br(htmlspecialchars($comment["comment"])) . '</p>';
                    echo '<p class="post-meta">By: ' . htmlspecialchars($comment["username"]) . ' on ' . date('Y-m-d H:i:s', strtotime($comment["created_at"])) . '</p>';
                    
                    // we display edit and delete buttons for the comment if the current user is the comment author or is an admin
                    if($_SESSION['role'] == 'admin' || $comment['user_id'] == $User_id)
                    {
                        echo '<div class="post-actions">';
                        echo '<a class="link-button success" href="edit_comment.php?id=' . $comment['id'] . '&post_id=' . $post_id . '">Edit</a>';
                        echo '<a class="link-button danger" href="delete_comment.php?id=' . $comment['id'] . '&post_id=' . $post_id . '" onclick="return confirm(\'Are you sure?\');">Delete</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>

        <!-- we display a form to allow logged-in users to add a new comment to the post -->
        <div class="card">
            <h3 class="center">Add a Comment</h3>
            <?php
            if(!empty($error))
            {
                echo "<div class='error'>*" . htmlspecialchars($error) . "</div>";
            }
            if(!empty($success))
            {
                echo "<div style='color: green; padding: 10px; border: 1px solid green; border-radius: 5px; margin-bottom: 15px;'>✓ " . htmlspecialchars($success) . "</div>";
            }
            ?>
            <form action="comments.php?post_id=<?php echo $post_id; ?>" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="comment">Your Comment:</label>
                    <textarea id="comment" name="comment" required minlength="2" style="min-height: 100px;"></textarea>
                </div>
                <button type="submit" class="button primary full-width">Post Comment</button>
            </form>
        </div>
    </div>
</body>
</html>
