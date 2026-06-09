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

// we retrieve the search term from the GET parameters and trim any whitespace. We then construct a SQL WHERE clause based on whether the search term is empty or not. If the search term is not empty, we create a WHERE clause that filters users based on whether their username contains the search term using the LIKE operator.
$search = trim((string)($_GET["search"] ?? ""));

// we check if the user has clicked the "Clear Search" button, which is indicated by the presence of the "clear_search" parameter in the GET request. If this parameter is set, we reset the search term to an empty string, effectively clearing any search filters and allowing all users to be displayed.
if(isset($_GET["clear_search"]))
{
    $search = "";
}

// we set the $where variable to an empty string by default. If the search term is not empty, we update the $where variable to include a SQL WHERE clause that filters users based on whether their username contains the search term using the LIKE operator. This allows us to retrieve only the users that match the search criteria when we execute our SQL queries later in the code.
$where = "";
if(!empty($search))
{
    $where = "WHERE username LIKE :search_term";
}

// we set the number of results to display per page for pagination purposes
$result_per_page = 3;

// we execute a SQL query to fetch the total count of users that match the search criteria
$count_query = "SELECT COUNT(id) AS total FROM users $where";
$count_stmt = $pdo->prepare($count_query);
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

// we execute a SQL query to fetch the users for the current page, applying any search filters if necessary. We also fetch the count of posts created by each user. The results are ordered by username in ascending order and limited to the specified number of results per page.
$query = "SELECT u.id, u.username, u.role, COUNT(p.id) AS post_count FROM users u LEFT JOIN posts p ON u.id = p.user_id $where GROUP BY u.id, u.username, u.role ORDER BY u.username ASC LIMIT :start, :result_per_page";
$stmt = $pdo->prepare($query);
if(!empty($search))
{
    $search_term = "%$search%";
    $stmt->bindParam(":search_term", $search_term);
}
$stmt->bindValue(":start", $start, PDO::PARAM_INT);
$stmt->bindValue(":result_per_page", $result_per_page, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="center" style="margin-bottom: 20px;">
            <h2 class="heading">Find Users</h2>
            <div class="actions" style="justify-content:center; margin-top:0; margin-bottom:10px;">
                <a class="link-button secondary" href="dashboard.php">Back to Dashboard</a>
            </div>

            <!-- we create a search form that allows users to search for other users based on their username. The form submits a GET request to the same page (find_users.php) with the search query as a parameter. If there is an active search query, we also provide a "Clear Search" button that allows users to reset the search and view all users again. -->

            <form action="find_users.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by username..." value="<?php echo htmlspecialchars($search); ?>" minlength="1">
                <button type="submit" class="button primary">Search</button>
                <?php
                if(!empty($search))
                {
                ?>
                <a class="link-button secondary" href="find_users.php">Clear Search</a>
                <?php
                }
                ?>
            </form>
        </div>

        <?php
        // we display a message if no users are found
        if(empty($users))
        {
            echo "<p class='center'> No users found </p>";
        }
        else
        {
            // we display each user in a card format with their username, role, post count, and a link to view their posts
            foreach($users as $user)
            {
                echo '<div class="post-card">';
                echo '<h3><a href="user_posts.php?id=' . $user['id'] . '" style="color: #2563eb; text-decoration: none;">' . htmlspecialchars($user['username']) . '</a></h3>';
                echo '<p><strong>Role:</strong> ' . htmlspecialchars($user['role']) . '</p>';
                echo '<p><strong>Total Posts:</strong> ' . $user['post_count'] . '</p>';
                echo '<div class="post-actions">';
                echo '<a class="link-button primary" href="user_posts.php?id=' . $user['id'] . '">View Posts</a>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>

        <!-- we implement pagination controls to allow users to navigate through multiple pages of users. The pagination controls include "Previous" and "Next" buttons that allow users to navigate to the previous and next pages of users, respectively. We also display the current page number and the total number of pages to provide users with context about their position within the list of users. The pagination links include the current search query as a parameter to maintain the search state when navigating between pages. -->

        <div class="pagination center" style="margin-top: 30px;">
            <?php
            if($page > 1)
            {
                echo '<a class="link-button primary" href="find_users.php?page=' . ($page - 1) . '&search=' . urlencode($search) . '">Previous</a>';
            }
            ?>
            <span style="margin: 0 10px;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <?php
            if($page < $total_pages)
            {
                echo '<a class="link-button primary" href="find_users.php?page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
