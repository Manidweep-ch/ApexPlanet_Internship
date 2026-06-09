<?php
session_start();
require_once "db.php";

// we check if the user is logged in by verifying if 'user_id' is set in the session. If the user is not logged in, we redirect them to the login page using header("Location: login.php") and exit the script to prevent further execution.
if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

$error = "";
$User_id = $_SESSION['user_id'];

// we retrieve the comment_id and post_id from the GET parameters and convert them to integers. This allows us to fetch the specific comment for which we want to edit.
$comment_id = (int)($_GET["id"] ?? 0);
$post_id = (int)($_GET["post_id"] ?? 0);

// we execute a SQL query to fetch the comment with the specified comment_id from the database. We then fetch the result as an associative array.
$query = "SELECT * FROM comments WHERE id = :comment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":comment_id", $comment_id);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

// we check if the comment exists. If it does not exist, we display an error message and exit the script to prevent further execution.
if(!$comment)
{
    die("Comment Not Found");
}

// we check if the current user is the comment author or is an admin. If neither condition is true, we display an access denied message and exit the script to prevent unauthorized editing.
if($_SESSION['role'] != 'admin' && $comment['user_id'] != $User_id)
{
    die("Access Denied");
}

// we handle the POST request when the user submits the edited comment. We retrieve the comment text from the POST parameters and trim any whitespace. We then validate the comment to ensure it is not empty and meets the minimum length requirement.
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
    
    // if there are no errors, we update the comment in the database using a prepared statement
    if(empty($error))
    {
        $update_query = "UPDATE comments SET comment = :comment WHERE id = :comment_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(":comment", $comment_text);
        $update_stmt->bindParam(":comment_id", $comment_id);
        $update_stmt->execute();
        
        // we redirect the user to the comments page for the post after the comment has been successfully updated
        header("Location: comments.php?post_id=" . $post_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="heading center">Edit Comment</h2>
            <?php if(!empty($error)): ?>
                <div class="error">*<?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="edit_comment.php?id=<?php echo $comment_id; ?>&post_id=<?php echo $post_id; ?>" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment" required minlength="2" style="min-height: 100px;"><?php echo htmlspecialchars($comment_text ?? $comment['comment']); ?></textarea>
                </div>
                <button type="submit" class="button success full-width">Update Comment</button>
                <div class="center">
                    <a class="link-button secondary" href="comments.php?post_id=<?php echo $post_id; ?>">Back to Comments</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
