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
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$selected_user_id = $user_id;
$selected_username = $current_username;
$userOptions = [];

if ($isAdmin) {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
    $userOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET['selected_user_id']) && ctype_digit((string)$_GET['selected_user_id'])) {
            $selected_user_id = (int)$_GET['selected_user_id'];
        } elseif (!empty($_GET['selected_username'])) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute([':username' => trim((string)$_GET['selected_username'])]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userRow) {
                $selected_user_id = (int)$userRow['id'];
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['selected_user_id']) && ctype_digit((string)$_POST['selected_user_id'])) {
            $selected_user_id = (int)$_POST['selected_user_id'];
        } else {
            $selected_user_id = $user_id;
        }
    }

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
    $stmt->execute([':id' => $selected_user_id]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow) {
        $selected_username = $userRow['username'];
    } else {
        $selected_user_id = $user_id;
        $selected_username = $current_username;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$isAdmin || !isset($_POST['selected_user_id']) || !ctype_digit((string)$_POST['selected_user_id'])) {
        $selected_user_id = $user_id;
    } else {
        $selected_user_id = (int)$_POST['selected_user_id'];
    }

    $username = trim((string)($_POST["username"] ?? ""));
    $new_password = trim((string)($_POST["new_password"] ?? ""));

    if (empty($username)) {
        $error = "Please enter a username.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Username must be between 3 and 20 characters.";
    } elseif ($new_password !== '' && strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    }

    if (empty($error) && $username !== $selected_username) {
        $stmt = $pdo->prepare("SELECT COUNT(id) AS total FROM users WHERE username = :username AND id != :id");
        $stmt->execute([
            ':username' => $username,
            ':id' => $selected_user_id,
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
            ':id' => $selected_user_id,
        ];

        if ($new_password !== '') {
            $updateQuery .= ", pwd = :pwd";
            $params[':pwd'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $updateQuery .= " WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute($params);

        if ($selected_user_id === $user_id) {
            $_SESSION['username'] = $username;
            $current_username = $username;
        }

        $success = "Profile updated successfully.";
        $selected_username = $username;
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
            <?php if ($isAdmin): ?>
                <form action="edit_profile.php" method="GET" class="form-grid">
                    <div class="form-field">
                        <label for="selected_user_id">Select account to edit:</label>
                        <select id="selected_user_id" name="selected_user_id" class="full-width" onchange="this.form.submit()">
                            <?php foreach ($userOptions as $userOption): ?>
                                <option value="<?php echo htmlspecialchars($userOption['id']); ?>" <?php echo $userOption['id'] === $selected_user_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($userOption['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            <?php endif; ?>
            <form action="edit_profile.php" method="POST" class="form-grid">
                <?php if ($isAdmin): ?>
                    <input type="hidden" name="selected_user_id" value="<?php echo htmlspecialchars($selected_user_id); ?>">
                <?php endif; ?>
                <div class="form-field">
                    <label for="username">New Username:</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="20" value="<?php echo htmlspecialchars($selected_username); ?>">
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
