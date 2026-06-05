<?php
// session_start is used to start a new session or resume an existing session.
session_start();
// session_unset is used to free all session variables.
session_unset();
// session_destroy is used to destroy all data registered to a session.
session_destroy();
// After destroying the session, we redirect the user to the login page.

//In this code we use session_start() to start the session whose configuration is defined in the login.php
// we use session_unset() to clear all session variables, and session_destroy() to destroy the session. Finally, we redirect the user to the login page using header("Location: login.php") and exit the script.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="styles.css">
    <meta http-equiv="refresh" content="5;url=login.php">
</head>
<body>
    <div class="container">
        <div class="card auth-card center">
            <h2 class="heading">Logged out</h2>
            <p>Your session has been ended. You will return to the login page in a few seconds.</p>
        </div>
    </div>
</body>
</html>