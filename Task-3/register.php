<?php
// we include the db.php file to establish a connection to the database. This allows us to use the $pdo variable, which is the PDO object representing the database connection, in our registration logic.
require_once "db.php";
//var_dump is a built-in PHP function that displays structured information about a variable, including its type and value. In this case, we are using var_dump to display the contents of the $pdo variable, which is the PDO object representing the database connection. This can be useful for debugging purposes to ensure that the connection was established successfully and to see the details of the PDO object.
//var_dump($pdo);
if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        // we use the trim function to remove any leading or trailing whitespace from the username input. This helps to ensure that the username is stored in a consistent format and prevents issues with accidental spaces.
        $username=trim($_POST["username"]);
        // we use the password_hash function to securely hash the user's password before storing it in the database. This helps to protect the user's password in case of a data breach, as the hashed password cannot be easily reversed back to the original plaintext password.
        $pwd=$_POST["password"];
        $hashedpwd=password_hash($pwd,PASSWORD_DEFAULT);
        // we prepare an SQL statement to insert the new user's username and hashed password into the "users" table in the database. We use prepared statements to prevent SQL injection attacks, which can occur when user input is directly included in an SQL query without proper sanitization.
        //prepare statement sanitaizes the input and prevents malicious code from being executed in the database. It also allows us to use placeholders (like :username and :pwd) in the SQL query, which are then replaced with the actual values when we execute the statement.
        //how prepared statements work: when we prepare the SQL statement, the database server parses and compiles the query, creating a template for the execution. When we execute the statement with the provided parameters, the database server fills in the placeholders with the actual values and executes the query. This separation of query preparation and execution helps to prevent SQL injection attacks, as the user input is treated as data rather than executable code.
        $sql="INSERT INTO users (username,pwd) VALUES (:username,:pwd)";
        // we execute the prepared statement with the provided parameters, which inserts the new user's information into the database. If the execution is successful, we display a success message to the user.
        $stmt=$pdo->prepare($sql);
        $stmt->execute(["username"=>$username,"pwd"=>$hashedpwd]);
        echo "Registration successful!";
        header("Location: login.php");
        exit();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card auth-card center">
            <h2 class="heading">Register</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-grid">
                <div class="form-field">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-field">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="button primary full-width">Register</button>
            </form>
            <div class="actions">
                <a class="link-button secondary full-width" href="login.php">Already have an account? Login here</a>
            </div>
        </div>
    </div>
</body>
</html>