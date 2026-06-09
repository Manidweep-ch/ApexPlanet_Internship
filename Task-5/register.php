<?php
// we include the db.php file to establish a connection to the database. This allows us to use the $pdo variable, which is the PDO object representing the database connection, in our registration logic.
require_once "db.php";
//var_dump is a built-in PHP function that displays structured information about a variable, including its type and value. In this case, we are using var_dump to display the contents of the $pdo variable, which is the PDO object representing the database connection. This can be useful for debugging purposes to ensure that the connection was established successfully and to see the details of the PDO object.
//var_dump($pdo);
$error="";
if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        // we use the trim function to remove any leading or trailing whitespace from the username and password inputs.
        $username = trim((string)($_POST["username"] ?? ""));
        $password = trim((string)($_POST["password"] ?? ""));
        $role = trim((string)($_POST["role"] ?? "user"));
        $allowedRoles = ["admin", "user"];
        //validation part
        if(empty($username) || empty($password))
        {
            $error="Please fill in all fields.";
        }
        else if(!in_array($role, $allowedRoles, true))
        {
            $error="Invalid role selected.";
        }
        else if(strlen($username) < 3 || strlen($username) > 20)
        {
            $error="Username must be between 3 and 20 characters.";
        }
        else if(strlen($password) < 6)
        {
            $error="Password must be at least 6 characters long.";
        }
        if(empty($error))
        {
            $query="SELECT COUNT(id) AS total FROM users WHERE username = :username";
            $stmt=$pdo->prepare($query);
            $stmt->bindParam(":username",$username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $check = (int) ($row['total'] ?? 0);
            if($check != 0)
            {
                $error="Username already exists.";
            }
        }

        if(empty($error))
        {
            $hashedpwd=password_hash($password,PASSWORD_DEFAULT);
            // insert the selected role so user access levels are recorded correctly.
            $sql="INSERT INTO users (username,pwd,role) VALUES (:username,:pwd,:role)";
            $stmt=$pdo->prepare($sql);
            $stmt->execute(["username"=>$username,"pwd"=>$hashedpwd,"role"=>$role]);
            header("Location: login.php");
            exit();
        }
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
                <?php
                if(!(empty($error)))
                {
                ?>
                <div class='error'> 
                    * <?php echo htmlspecialchars($error);?>
                </div>
                <?php
                }?>
                <div class="form-field">
                    <label>Username:</label>
                    <input type="text" name="username" required minlength="3" maxlength="20" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                </div>
                <div class="form-field">
                    <label>Password:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="form-field">

                    <!-- roles For the User -->
                     
                    <label>Role</label>
                    <select name="role" id="role" required>
                        <option value="admin" <?php echo (isset($role) && $role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo (isset($role) && $role === 'user') ? 'selected' : ''; ?>>User</option>
                    </select>
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