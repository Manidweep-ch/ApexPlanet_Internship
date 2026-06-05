<?php
// dsn stands for data source name, it is a string that contains the information required to connect to a database. In this case, we are connecting to a MySQL database hosted on localhost with the name "blog".
// username and password are the credentials used to authenticate the connection to the database. In this case, we are using the default username "root" and an empty password.
$dsn = "mysql:host=localhost;dbname=blog";
$username = "root";
$password = "";
// we use a try-catch block to handle any potential exceptions that may occur during the database connection process. If the connection is successful, we set the error mode to exception. If there is an error, we catch the exception and display an error message.
try {
    //pdo means php data objects, it is a database access layer providing a uniform method of access to multiple databases 
    $pdo = new PDO($dsn, $username, $password);
    //here we set the error mode to exception, which means that if there is an error in the database connection or query execution, it will throw a PDOException that we can catch and handle appropriately.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}