<?php
require_once "db.php";

// we retrieve the search term from the GET parameters and trim any whitespace. We then construct a SQL WHERE clause based on whether the search term is empty or not. If the search term is not empty, we create a WHERE clause that filters posts based on whether their title or content contains the search term using the LIKE operator.


$search = trim($_GET["search"] ?? "");
$where="";


// we also check if the user has clicked the "Clear Search" button, which is indicated by the presence of the "clear_search" parameter in the GET request. If this parameter is set, we reset the search term to an empty string, effectively clearing any search filters and allowing all posts to be displayed.


if(isset($_GET["clear_search"]))
    {
        $search="";
    }


// we set the $where variable to an empty string by default. If the search term is not empty, we update the $where variable to include a SQL WHERE clause that filters posts based on whether their title or content contains the search term using the LIKE operator. This allows us to retrieve only the posts that match the search criteria when we execute our SQL queries later in the code.


if(!empty($search))
    {
    $where = "WHERE title LIKE :search_term OR content LIKE :search_term ";
}