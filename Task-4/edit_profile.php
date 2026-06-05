<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";
$user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim((string)($_POST["username"] ?? ""));
    $new_password = trim((string)($_POST["new_password"] ?? ""));

    if (empty($username)) {
        $error = "Please enter a username.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Username must be between 3 and 20 characters.";
    } elseif ($new_password !== '' && strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    }

    if (empty($error) && $username !== $current_username) {
        $stmt = $pdo->prepare("SELECT COUNT(id) AS total FROM users WHERE username = :username AND id != :id");
        $stmt->execute([
            ':username' => $username,
            ':id' => $user_id,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ((int) ($row['total'] ?? 0) !== 0) {
            $error = "Username already exists.";
        }
    }

    if (empty($error)) {
        $updateQuery = "UPDATE users SET username = :username";
        $params = [
            ':username' => $username,
            ':id' => $user_id,
        ];

        if ($new_password !== '') {
            $updateQuery .= ", pwd = :pwd";
            $params[':pwd'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $updateQuery .= " WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute($params);

        $_SESSION['username'] = $username;
        $success = "Profile updated successfully.";
        $current_username = $username;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="heading center">Edit Profile</h2>
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="error">*<?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="edit_profile.php" method="POST" class="form-grid">
                <div class="form-field">
                    <label for="username">New Username:</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="20" value="<?php echo htmlspecialchars($current_username); ?>">
                </div>
                <div class="form-field">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" minlength="6" autocomplete="new-password" placeholder="Leave blank to keep current password">
                </div>
                <button type="submit" class="button primary full-width">Save Changes</button>
                <div class="center">
                    <a class="link-button secondary" href="dashboard.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
