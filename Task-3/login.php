<?php
// we start a session to manage user authentication and store user information across different pages of the application. This allows us to keep track of the user's login status and access their information as needed.
session_start();
require_once "db.php";
$error="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $pwd = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    // we fetch the user's information from the database as an associative array. This allows us to access the user's stored password hash and other relevant information for authentication purposes.
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // we use the password_verify function to compare the provided password with the stored password hash. This function takes the plaintext password and the hashed password as arguments and returns true if they match, indicating that the user has provided the correct password.
    if ($user && password_verify($pwd, $user['pwd'])) {
        // if the user exists and the provided password matches the stored password hash, we set session variables to indicate that the user is logged in and to store their user ID and username. This allows us to maintain the user's authenticated state across different pages of the application and access their information as needed.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo "Login Successful";
        header("Location: dashboard.php");
        exit();
    } else {

        $error="Invalid Username or Password";

    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card auth-card center">
            <h2 class="heading">Login</h2>
            <?php if(!empty($error)): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-grid">
                <div class="form-field">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-field">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="button primary full-width">Login</button>
            </form>
            <div class="actions">
                <a class="link-button secondary full-width" href="register.php">Register an account</a>
            </div>
        </div>
    </div>
</body>
</html>