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
$id=(int)($_GET["id"]??0);
$query="SELECT * FROM posts WHERE id=:id;";
$stmt=$pdo->prepare($query);
$stmt->bindParam(":id",$id);
$stmt->execute();
$post=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$post)
{
    die("<h2>Post Not Found</h2><a href='view_posts.php' class='link-button primary'>Back to View Posts</a>");
}
if($_SESSION['role'] != 'admin' && $post['user_id']!=$_SESSION['user_id'])
{
    die("<h2>Access Denied</h2><a href='view_posts.php' class='link-button primary'>Back to View Posts</a>");
}
$query="DELETE FROM posts WHERE id=:id;";
$stmt=$pdo->prepare($query);
$stmt->bindParam(":id",$id);
$stmt->execute();
header("Location: view_posts.php");
exit();
?>