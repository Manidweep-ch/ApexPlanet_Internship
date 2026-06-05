# Task 4 - Secure Blog Application with Roles

## Project Summary

This project is a PHP/MySQL blog application updated for Task 4. It now supports secure user registration, role-based access control, server- and client-side validation, post ownership, search, and pagination.

The app now supports:

- Registering accounts with roles: `user`, `editor`, `admin`
- Logging in with secure password verification
- Creating posts linked to the logged-in user
- Viewing all posts or just the current user's posts
- Searching posts by title or content
- Paginating posts with preserved search state
- Editing posts with role-based permissions
- Editing user profile (username and password)
- Deleting posts with role-based permissions
- Logging out

## Technologies Used

- PHP
- MySQL
- PDO
- HTML5 form validation
- CSS
- XAMPP

## Files in the Project

- `db.php` - secure PDO database connection.
- `register.php` - new account registration with role selection.
- `login.php` - user authentication and session creation.
- `dashboard.php` - protected user dashboard.
- `logout.php` - session cleanup and logout.
- `create_posts.php` - create a new post.
- `view_posts.php` - view posts with search and pagination.
- `my_posts.php` - view only the current user's posts.
- `edit_post.php` - edit a post if permitted.
- `edit_profile.php` - edit current username and password.
- `delete_post.php` - delete a post if permitted.
- `search.php` - search query preparation.
- `pagination.php` - post pagination logic.
- `styles.css` - shared styling across pages.
- `README.md` - project documentation.

## Task 4 Improvements

### Role-Based Access Control

- `users` now include a `role` field.
- `admin` users can edit and delete any post.
- `editor` and `user` accounts can edit or delete only their own posts.
- Session data stores `user_id`, `username`, and `role` for RBAC checks.

### Security Enhancements

- All database queries use prepared statements to prevent SQL injection.
- Passwords are stored using `password_hash()` and validated with `password_verify()`.
- Access control prevents unauthorized editing and deletion.
- Server-side validation enforces required fields and length limits.
- Client-side HTML validation improves user experience.

### Validation Improvements

- `register.php` validates username, password, and role.
- `login.php` validates required fields and minimum lengths.
- `create_posts.php` and `edit_post.php` validate title and content lengths.
- HTML5 `required`, `minlength`, and `maxlength` are used on inputs.
- Forms preserve user input when validation fails.

## Database Schema Changes

### `users` table

The `users` table now includes roles:

```sql
ALTER TABLE users
ADD role VARCHAR(20) NOT NULL DEFAULT 'user';
```

Example admin promotion:

```sql
UPDATE users SET role = 'admin' WHERE username = 'Manidweep';
```

### `posts` table

Posts are now linked to the user who created them:

```sql
ALTER TABLE posts
ADD user_id INT NOT NULL DEFAULT 1,
ADD CONSTRAINT user_post
FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE;
```

Example post ownership fix:

```sql
UPDATE posts SET user_id = 3 WHERE id = 3;
```

### Recommended Table Definitions

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## How the Application Works

### Register Page (`register.php`)

- Collects username, password, and role.
- Validates input on the server and browser.
- Checks username uniqueness.
- Stores hashed password and role in the database.
- Redirects to `login.php` after successful registration.

### Login Page (`login.php`)

- Validates login form input.
- Loads user by username.
- Verifies password with `password_verify()`.
- Stores session values for `user_id`, `username`, and `role`.
- Redirects to `dashboard.php`.

### Dashboard (`dashboard.php`)

- Protected route that requires login.
- Shows the current username and role.
- Links to create posts, view posts, edit profile, and logout.

### Edit Profile (`edit_profile.php`)

- Allows the logged-in user to change their username.
- Allows the logged-in user to update their password.
- Validates username length and optional password length.
- Updates the session username after a successful change.

### Create Post (`create_posts.php`)

- Only available to logged-in users.
- Saves the new post with the current user as `user_id`.
- Uses server-side validation for title and content.

### View Posts (`view_posts.php`)

- Displays posts with search and pagination.
- Shows edit/delete buttons only when the current user is allowed.
- Admin users can manage any post.
- Other users can manage only their own posts.

### My Posts (`my_posts.php`)

- Displays only the currently logged-in user's posts.
- Uses the same pagination logic as `view_posts.php`.

### Edit Post (`edit_post.php`)

- Verifies post ownership or admin access.
- Validates updated title and content.

### Delete Post (`delete_post.php`)

- Verifies post ownership or admin access.
- Deletes the post and redirects back to viewing posts.

### Search and Pagination

- `search.php` builds the query filter for title/content search.
- `pagination.php` runs count and page queries safely.
- Search state is preserved across page links.

## How to Run

1. Start Apache and MySQL in XAMPP.
2. Create or update the database tables using the SQL above.
3. Place the `Task-4` folder in `C:\xampp\htdocs`.
4. Open `http://localhost/Task-4/register.php`.
5. Create a user and log in.

## Author

Manidweep

ApexPlanet Web Development Internship - Task 4

