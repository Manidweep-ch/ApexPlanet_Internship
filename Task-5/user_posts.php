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

// we retrieve the user_id from the GET parameters and convert it to an integer. This allows us to fetch the specific user whose posts we want to display.
$profile_user_id = (int)($_GET["id"] ?? 0);

// we execute a SQL query to fetch the user information from the database including the count of their posts
$user_query = "SELECT u.id, u.username, u.role, COUNT(p.id) AS post_count FROM users u LEFT JOIN posts p ON u.id = p.user_id WHERE u.id = :user_id GROUP BY u.id, u.username, u.role";
$user_stmt = $pdo->prepare($user_query);
$user_stmt->bindParam(":user_id", $profile_user_id);
$user_stmt->execute();
$profile_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// we check if the user exists. If the user does not exist, we display an error message and exit the script to prevent further execution.
if(!$profile_user)
{
    die("User Not Found");
}

// we retrieve the search term from the GET parameters and trim any whitespace. We then construct a SQL WHERE clause based on whether the search term is empty or not. If the search term is not empty, we create a WHERE clause that filters posts based on whether their title or content contains the search term using the LIKE operator.
$search = trim((string)($_GET["search"] ?? ""));

// we check if the user has clicked the "Clear Search" button, which is indicated by the presence of the "clear_search" parameter in the GET request. If this parameter is set, we reset the search term to an empty string, effectively clearing any search filters and allowing all posts by the user to be displayed.
if(isset($_GET["clear_search"]))
{
    $search = "";
}

// we set the $where variable to an empty string by default. If the search term is not empty, we update the $where variable to include a SQL WHERE clause that filters posts based on whether their title or content contains the search term using the LIKE operator. This allows us to retrieve only the posts by this user that match the search criteria when we execute our SQL queries later in the code.
$where = "";
if(!empty($search))
{
    $where = "AND (title LIKE :search_term OR content LIKE :search_term)";
}

// we set the number of results to display per page for pagination purposes
$result_per_page = 3;

// we execute a SQL query to fetch the total count of posts created by the user that match the search criteria
$count_query = "SELECT COUNT(id) AS total FROM posts WHERE user_id = :user_id $where";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->bindParam(":user_id", $profile_user_id);
if(!empty($search))
{
    $search_term = "%$search%";
    $count_stmt->bindParam(":search_term", $search_term);
}
$count_stmt->execute();
$total_results = (int)$count_stmt->fetch(PDO::FETCH_ASSOC)["total"];
$total_pages = max(1, ceil($total_results / $result_per_page));

// we retrieve the current page number from the GET parameters. If the "page" parameter is not set, we default to page 1. If it is set, we convert it to an integer and store it in the $page variable for further processing.
if(!isset($_GET["page"]))
{
    $page = 1;
}
else
{
    $page = (int)$_GET["page"];
}

// we ensure that the current page number is within the valid range of 1 to the total number of pages. If the page number is less than 1, we set it to 1. If it is greater than the total number of pages, we set it to the total number of pages. This prevents users from accessing invalid page numbers and ensures that the pagination works correctly.
$page = max(1, min($page, $total_pages));

// we calculate the starting index for the SQL query based on the current page number and the number of results per page
$start = ($page - 1) * $result_per_page;

// we execute a SQL query to fetch the posts for the current page created by the user, applying any search filters if necessary. The results are ordered by creation date in descending order and limited to the specified number of results per page.
$posts_query = "SELECT * FROM posts WHERE user_id = :user_id $where ORDER BY created_at DESC LIMIT :start, :result_per_page";
$posts_stmt = $pdo->prepare($posts_query);
$posts_stmt->bindParam(":user_id", $profile_user_id);
if(!empty($search))
{
    $search_term = "%$search%";
    $posts_stmt->bindParam(":search_term", $search_term);
}
$posts_stmt->bindValue(":start", $start, PDO::PARAM_INT);
$posts_stmt->bindValue(":result_per_page", $result_per_page, PDO::PARAM_INT);
$posts_stmt->execute();
$posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['username']); ?>'s Posts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- we display the user's profile information including their username, role, and total post count -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="center">
                <h2 class="heading"><?php echo htmlspecialchars($profile_user['username']); ?>'s Profile</h2>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($profile_user['role']); ?></p>
                <p><strong>Total Posts:</strong> <?php echo $profile_user['post_count']; ?></p>
                <div class="actions" style="justify-content:center; margin-top:15px; margin-bottom:0;">
                    <a class="link-button secondary" href="dashboard.php">Back to Dashboard</a>
                </div>
            </div>
        </div>

        <!-- we display a heading for the posts section along with search functionality -->
        <div class="center" style="margin-bottom: 20px;">
            <h2 class="heading">Posts by <?php echo htmlspecialchars($profile_user['username']); ?></h2>

            <!-- we create a search form that allows users to search for posts by this user based on their title or content. The form submits a GET request to the same page (user_posts.php) with the search query as a parameter. If there is an active search query, we also provide a "Clear Search" button that allows users to reset the search and view all posts by this user again. -->

            <form action="user_posts.php" method="GET" class="search-form">
                <input type="hidden" name="id" value="<?php echo $profile_user_id; ?>">
                <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>" minlength="1">
                <button type="submit" class="button primary">Search</button>
                <?php
                if(!empty($search))
                {
                ?>
                <a class="link-button secondary" href="user_posts.php?id=<?php echo $profile_user_id; ?>">Clear Search</a>
                <?php
                }
                ?>
            </form>
        </div>

        <?php
        // we display each post created by the user in a card format with the title, content, creation date, and a button to view comments
        if(empty($posts))
        {
            echo "<p class='center'> No posts found </p>";
        }
        else
        {
            foreach($posts as $post)
            {
                echo '<div class="post-card">';
                echo '<h3>' . htmlspecialchars($post["title"]) . '</h3>';
                
                // we use the nl2br function to convert newlines in the post content to HTML line breaks, and the htmlspecialchars function to prevent XSS attacks by escaping special characters. This allows us to safely display the post content while preserving the formatting of the original text.
                
                echo '<p>' . nl2br(htmlspecialchars($post["content"])) . '</p>';
                echo '<p class="post-meta">Posted on: ' . date('Y-m-d H:i:s', strtotime($post["created_at"])) . '</p>';
                echo '<div class="post-actions">';
                echo '<a class="link-button primary" href="comments.php?post_id=' . $post['id'] . '">+ Comments</a>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>

        <!-- we implement pagination controls to allow users to navigate through multiple pages of posts by this user. The pagination controls include "Previous" and "Next" buttons that allow users to navigate to the previous and next pages of posts, respectively. We also display the current page number and the total number of pages to provide users with context about their position within the list of posts. The pagination links include the current search query and user_id as parameters to maintain the search state and user context when navigating between pages. -->

        <div class="pagination center">
            <?php
            if($page > 1)
            {
                echo '<a class="link-button primary" href="user_posts.php?id=' . $profile_user_id . '&page=' . ($page - 1) . '&search=' . urlencode($search) . '">Previous</a>';
            }
            ?>
            <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <?php
            if($page < $total_pages)
            {
                echo '<a class="link-button primary" href="user_posts.php?id=' . $profile_user_id . '&page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
