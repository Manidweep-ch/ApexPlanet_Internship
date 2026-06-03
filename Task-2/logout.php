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
<html>
<head>
    <title>Logout</title>
    <meta http-equiv="refresh" content="5;url=login.php">
</head>
<body style="
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    text-align: center;
">
<div style="
    border: 2px solid black;
    padding: 25px;
    border-radius: 10px;
">
    <h2>You have been logged out.</h2>
    <p>You will be redirected to the login page in 5 seconds.</p>
</div>
</body>
</html>