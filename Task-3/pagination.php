<?php
    require_once "db.php";
    require_once "search.php";

// we set the number of results to display per page for pagination purposes. We then execute a SQL query to fetch all posts from the database and display them in a structured format. Each post is displayed with its title, content, creation date, and action buttons for editing and deleting the post.


        $result_per_page=2;
        $query1="SELECT COUNT(id) AS total FROM posts $where;";
        $stmt1=$pdo->prepare($query1);
        //
        if(!empty($where))
            {
                $search_term="%$search%";
                $stmt1->bindParam(":search_term",$search_term);
            }
        $stmt1->execute();
        //
        $total_pages=ceil($stmt1->fetch(PDO::FETCH_ASSOC)["total"]/$result_per_page);
        if(!isset($_GET["page"]))
        {
                $page=1;
        }
        else
        {
            // we retrieve the current page number from the GET parameters. If the "page" parameter is not set, we default to page 1. If it is set, we convert it to an integer and store it in the $page variable for further processing.
            $page=(int)$_GET["page"];
        }


        // we ensure that the current page number is within the valid range of 1 to the total number of pages. If the page number is less than 1, we set it to 1. If it is greater than the total number of pages, we set it to the total number of pages. This prevents users from accessing invalid page numbers and ensures that the pagination works correctly.


        $page=max(1,min($page,$total_pages));


        // we calculate the starting index for the SQL query based on the current page number and the number of results per page. We then execute a SQL query to fetch the posts for the current page, applying any search filters if necessary. The results are ordered by creation date in descending order and limited to the specified number of results per page.

        
        $start=($page-1) * $result_per_page;

        $query2 = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT :start,:result_per_page;";
        $stmt = $pdo->prepare($query2);
        if(!empty($where))
            {
                $search_term="%$search%";
                $stmt->bindParam(":search_term",$search_term);
            }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':result_per_page', $result_per_page, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

