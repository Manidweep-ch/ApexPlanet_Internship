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
$id=$_GET["id"];
$query="DELETE FROM posts WHERE id=:id;";
$stmt=$pdo->prepare($query);
$stmt->bindParam(":id",$id);
$stmt->execute();
header("Location: view_posts.php");
exit();
?>