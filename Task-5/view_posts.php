<?php
// we start a session to manage user authentication and store user information across different pages of the application
session_start();
require_once "db.php";
require_once "search.php";
require_once "pagination.php";
// we check if the user is logged in by verifying if 'user_id' is set in the session. If the user is not logged in, we redirect them to the login page using header("Location: login.php") and exit the script to prevent further execution.
if(!isset($_SESSION['user_id']))
    {

        header("Location: login.php");
        exit();
    }
$User_id=$_SESSION['user_id'];
$Type=$_GET['type'] ?? 'all';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Posts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="center" style="margin-bottom: 20px;">
            <h2 class="heading">View Posts</h2>
            <div class="actions" style="justify-content:center; margin-top:0; margin-bottom:10px;">
                <a class="link-button primary" href="create_posts.php">Create new Post</a>
                <?php
                if($Type=='all')
                    {
                ?>
                <a class="link-button primary" href="view_posts.php?type=my">My Posts</a>
                <?php
                    }
                else if($Type=='my')
                    {
                    ?>
                <a class="link-button primary" href="view_posts.php?type=all">View All Posts</a>
                <?php } ?>
                <a class="link-button secondary" href="dashboard.php">Back to Dashboard</a>
            </div>

            <!-- we create a search form that allows users to search for posts based on their title or content. The form submits a GET request to the same page (view_posts.php) with the search query as a parameter. If there is an active search query, we also provide a "Clear Search" button that allows users to reset the search and view all posts again. -->


            <form action="view_posts.php" method="GET" class="search-form">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($Type); ?>">
                <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>" minlength="1">
                <button type="submit" class="button secondary">Search</button>
                <?php

                // we check if there is an active search query by verifying if the $search variable is not empty. If there is an active search query, we display a "Clear Search" button that allows users to reset the search and view all posts again. The button submits a GET request with a parameter named "clear_search" set to 1, which can be handled in the backend to clear the search query and display all posts. 

                if(!empty($search))
                    {
                        echo '<button class="button secondary" type="submit" name="clear_search" value="1">Clear Search</button >';
                    }
                ?>
            </form>
        </div>
        <?php
        if(empty($posts))
        {
            echo "<p class='center'> No such Posts found </p>";
            die();
        }
        foreach ($posts as $post) {
            echo '<div class="post-card">';
            echo '<h3>' . htmlspecialchars($post["title"]) . '</h3>';


            // we use the nl2br function to convert newlines in the post content to HTML line breaks, and the htmlspecialchars function to prevent XSS attacks by escaping special characters. This allows us to safely display the post content while preserving the formatting of the original text.


            echo '<p>' . nl2br(htmlspecialchars($post["content"])) . '</p>';
            echo '<p class="post-meta">Posted on: ' . date('Y-m-d H:i:s', strtotime($post["created_at"])) . '</p>';
            echo '<div class="post-actions">';
            if($_SESSION['role']=='admin' || $post['user_id']==$User_id)
            {
                echo '<a class="link-button success" href="edit_post.php?id=' . $post['id'] . '">Edit</a>';
                echo '<a class="link-button danger" href="delete_post.php?id=' . $post['id'] . '" onclick="return confirm(\'Are you sure?\');">Delete</a>';
            }
            echo '<a class="link-button primary" href="comments.php?post_id=' . $post['id'] . '">+ Comments</a>';
            echo '</div>';
            echo '</div>';
        }
        ?>


        <!-- we implement pagination controls to allow users to navigate through multiple pages of posts. The pagination controls include "Previous" and "Next" buttons that allow users to navigate to the previous and next pages of posts, respectively. We also display the current page number and the total number of pages to provide users with context about their position within the list of posts. The pagination links include the current search query as a parameter to maintain the search state when navigating between pages. -->


        <div class="pagination center">
            <?php
            if($page>1)
                {
                    echo '<a class="link-button secondary" href="?type='. $Type . '&page=' . ($page - 1) . '&search=' . urlencode($search) . '">Previous</a>';
                }
            ?>
            <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <?php
            if($page<$total_pages)
                {
                    // we use the urlencode function to encode the search query parameter in the pagination links. This ensures that any special characters in the search query are properly encoded and do not cause issues in the URL when navigating between pages of posts. 
                    
                    echo '<a class="link-button secondary" href="?type='. $Type . '&page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next</a>';
                }
            ?>
        </div>
    
    </div>
</body>
</html>