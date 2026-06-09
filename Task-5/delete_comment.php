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

// we retrieve the comment_id and post_id from the GET parameters and convert them to integers. This allows us to fetch the specific comment for which we want to delete.
$comment_id = (int)($_GET["id"] ?? 0);
$post_id = (int)($_GET["post_id"] ?? 0);

// we execute a SQL query to fetch the comment with the specified comment_id from the database. We then fetch the result as an associative array.
$query = "SELECT * FROM comments WHERE id = :comment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":comment_id", $comment_id);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

// we check if the comment exists. If it does not exist, we display an error message and redirect back to the comments page.
if(!$comment)
{
    die("<h2>Comment Not Found</h2><a href='comments.php?post_id=" . $post_id . "' class='link-button primary'>Back to Comments</a>");
}

// we check if the current user is the comment author or is an admin. If neither condition is true, we display an access denied message and redirect back to the comments page.
if($_SESSION['role'] != 'admin' && $comment['user_id'] != $_SESSION['user_id'])
{
    die("<h2>Access Denied</h2><a href='comments.php?post_id=" . $post_id . "' class='link-button primary'>Back to Comments</a>");
}

// we execute a SQL query to delete the comment from the database using the comment_id
$delete_query = "DELETE FROM comments WHERE id = :comment_id";
$delete_stmt = $pdo->prepare($delete_query);
$delete_stmt->bindParam(":comment_id", $comment_id);
$delete_stmt->execute();

// we redirect the user to the comments page for the post after the comment has been successfully deleted
header("Location: comments.php?post_id=" . $post_id);
exit();
?>
