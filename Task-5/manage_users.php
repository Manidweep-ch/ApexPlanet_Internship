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

// we check if the user is an admin. If the user is not an admin, we display an access denied message and exit the script to prevent unauthorized access.
if($_SESSION['role'] != 'admin')
{
    die("Access Denied");
}

$error = "";
$success = "";

// we retrieve the search term from the GET parameters and trim any whitespace. We then construct a SQL WHERE clause based on whether the search term is empty or not. If the search term is not empty, we create a WHERE clause that filters users based on whether their username contains the search term using the LIKE operator.
$search = trim((string)($_GET["search"] ?? ""));

// we check if the user has clicked the "Clear Search" button, which is indicated by the presence of the "clear_search" parameter in the GET request. If this parameter is set, we reset the search term to an empty string, effectively clearing any search filters and allowing all users to be displayed.
if(isset($_GET["clear_search"]))
{
    $search = "";
}

// we handle POST requests for changing user roles or deleting users
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // we check if the action is to change the user role
    if(isset($_POST['action']) && $_POST['action'] == 'change_role')
    {
        $user_id_to_update = (int)($_POST['user_id'] ?? 0);
        $new_role = trim((string)($_POST['new_role'] ?? 'user'));
        
        // we validate the new role to ensure it is either 'admin' or 'user'
        $allowed_roles = ['admin', 'user'];
        if(!in_array($new_role, $allowed_roles, true))
        {
            $error = "Invalid role selected.";
        }
        
        // we prevent an admin from changing their own role
        if(empty($error) && $user_id_to_update == $_SESSION['user_id'])
        {
            $error = "You cannot change your own role.";
        }
        
        // if there are no errors, we update the user's role in the database using a prepared statement
        if(empty($error))
        {
            $update_query = "UPDATE users SET role = :role WHERE id = :user_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(":role", $new_role);
            $update_stmt->bindParam(":user_id", $user_id_to_update);
            $update_stmt->execute();
            $success = "User role updated successfully.";
        }
    }
    
    // we check if the action is to delete a user
    if(isset($_POST['action']) && $_POST['action'] == 'delete_user')
    {
        $user_id_to_delete = (int)($_POST['user_id'] ?? 0);
        
        // we prevent an admin from deleting their own account
        if($user_id_to_delete == $_SESSION['user_id'])
        {
            $error = "You cannot delete your own account.";
        }
        
        // if there are no errors, we delete the user from the database using a prepared statement. The ON DELETE CASCADE constraint in the database will automatically delete all posts and comments associated with the user.
        if(empty($error))
        {
            $delete_query = "DELETE FROM users WHERE id = :user_id";
            $delete_stmt = $pdo->prepare($delete_query);
            $delete_stmt->bindParam(":user_id", $user_id_to_delete);
            $delete_stmt->execute();
            $success = "User deleted successfully.";
        }
    }
}

// we set the number of results to display per page for pagination purposes
$result_per_page = 10;

// we construct a WHERE clause for the count query based on whether the search term is empty or not
$where = "";
if(!empty($search))
{
    $where = "WHERE username LIKE :search_term";
}

// we execute a SQL query to count the total number of users that match the search criteria
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
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="center" style="margin-bottom: 20px;">
            <h2 class="heading">Manage Users</h2>
            <div class="actions" style="justify-content:center; margin-top:0; margin-bottom:10px;">
                <a class="link-button secondary" href="dashboard.php">Back to Dashboard</a>
            </div>

            <!-- we create a search form that allows admins to search for users based on their username. The form submits a GET request to the same page (manage_users.php) with the search query as a parameter. If there is an active search query, we also provide a "Clear Search" button that allows admins to reset the search and view all users again. -->

            <form action="manage_users.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by username..." value="<?php echo htmlspecialchars($search); ?>" minlength="1">
                <button type="submit" class="button primary">Search</button>
                <?php
                if(!empty($search))
                {
                ?>
                <a class="link-button secondary" href="manage_users.php">Clear Search</a>
                <?php
                }
                ?>
            </form>
        </div>

        <?php
        // we display error and success messages if they exist
        if(!empty($error))
        {
            echo "<div class='error' style='margin-bottom: 15px;'>*" . htmlspecialchars($error) . "</div>";
        }
        if(!empty($success))
        {
            echo "<div style='color: green; padding: 10px; border: 1px solid green; border-radius: 5px; margin-bottom: 15px;'>✓ " . htmlspecialchars($success) . "</div>";
        }
        
        // we display a message if no users are found
        if(empty($users))
        {
            echo "<p class='center'> No users found </p>";
        }
        else
        {
            // we display each user in a table format with their username, role, post count, and action buttons for changing role or deleting the user
            echo '<div style="overflow-x: auto;">';
            echo '<table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">';
            echo '<thead>';
            echo '<tr style="background: #f0f4f8; border-bottom: 2px solid #cbd5e1;">';
            echo '<th style="padding: 12px; text-align: left; font-weight: 600;">Username</th>';
            echo '<th style="padding: 12px; text-align: left; font-weight: 600;">Role</th>';
            echo '<th style="padding: 12px; text-align: center; font-weight: 600;">Total Posts</th>';
            echo '<th style="padding: 12px; text-align: center; font-weight: 600;">Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach($users as $user)
            {
                echo '<tr style="border-bottom: 1px solid #e2e8f0;">';
                echo '<td style="padding: 12px;"><a href="user_posts.php?id=' . $user['id'] . '" style="color: #2563eb; text-decoration: none;">' . htmlspecialchars($user['username']) . '</a></td>';
                echo '<td style="padding: 12px;"><span style="background: ' . ($user['role'] == 'admin' ? '#fecaca' : '#bfdbfe') . '; padding: 4px 8px; border-radius: 4px; font-size: 0.9rem;">' . htmlspecialchars($user['role']) . '</span></td>';
                echo '<td style="padding: 12px; text-align: center;">' . $user['post_count'] . '</td>';
                echo '<td style="padding: 12px; text-align: center;">';
                
                // we display the role change button and delete button only if the user is not the current admin
                if($user['id'] != $_SESSION['user_id'])
                {
                    echo '<form method="POST" style="display: inline;">';
                    echo '<input type="hidden" name="action" value="change_role">';
                    echo '<input type="hidden" name="user_id" value="' . $user['id'] . '">';
                    echo '<input type="hidden" name="new_role" value="' . ($user['role'] == 'admin' ? 'user' : 'admin') . '">';
                    echo '<button type="submit" class="button ' . ($user['role'] == 'admin' ? 'danger' : 'success') . '" style="padding: 6px 12px; font-size: 0.9rem;">' . ($user['role'] == 'admin' ? 'Demote to User' : 'Promote to Admin') . '</button>';
                    echo '</form>';
                    
                    echo ' ';
                    
                    echo '<form method="POST" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to delete this user? All their posts and comments will be deleted.\');">';
                    echo '<input type="hidden" name="action" value="delete_user">';
                    echo '<input type="hidden" name="user_id" value="' . $user['id'] . '">';
                    echo '<button type="submit" class="button danger" style="padding: 6px 12px; font-size: 0.9rem;">Delete</button>';
                    echo '</form>';
                }
                else
                {
                    echo '<span style="color: #999;">-</span>';
                }
                
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        ?>

        <!-- we implement pagination controls to allow admins to navigate through multiple pages of users. The pagination controls include "Previous" and "Next" buttons that allow admins to navigate to the previous and next pages of users, respectively. We also display the current page number and the total number of pages to provide admins with context about their position within the list of users. The pagination links include the current search query as a parameter to maintain the search state when navigating between pages. -->

        <div class="pagination center" style="margin-top: 30px;">
            <?php
            if($page > 1)
            {
                echo '<a class="link-button primary" href="manage_users.php?page=' . ($page - 1) . '&search=' . urlencode($search) . '">Previous</a>';
            }
            ?>
            <span style="margin: 0 10px;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <?php
            if($page < $total_pages)
            {
                echo '<a class="link-button primary" href="manage_users.php?page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
