# Task 3 - Simple Blog Application

## Project Summary

This project is a blog app built with PHP and MySQL. Task 3 is an improved version of Task 2, with new features and cleaner styling.

The app now supports:

- Registering a new account
- Logging in
- Creating posts
- Viewing posts
- Searching posts
- Paginating posts
- Editing posts
- Deleting posts
- Logging out

I used PHP sessions for authentication and PDO for secure database queries.

## Technologies Used

- PHP
- MySQL
- PDO
- HTML
- CSS
- XAMPP

## Files in the Project

- `db.php` - database connection.
- `register.php` - user registration.
- `login.php` - user login.
- `dashboard.php` - main page after login.
- `logout.php` - ends the session.
- `create_posts.php` - create new posts.
- `view_posts.php` - view posts with search and pagination.
- `edit_post.php` - edit posts.
- `delete_post.php` - delete posts.
- `search.php` - handles search input.
- `pagination.php` - handles page navigation.
- `styles.css` - shared page styling.
- `README.md` - project documentation.

## What Changed in Task 3

### Search Functionality

- Added a search box to `view_posts.php`.
- Users can search posts by title or content.
- Search works using `GET` so the search query stays in the URL.
- If search is cleared, the page returns to displaying all posts.

### Pagination

- Added pagination so posts show in smaller pages.
- Pagination is handled in `pagination.php`.
- Users can move between pages with Previous and Next buttons.
- The search filter stays active while paging through results.

### Shared Styling

- Added `styles.css` for shared styling across all pages.
- Replaced inline styles with a cleaner design.
- Made buttons, cards, forms, and layout consistent.
- Added responsive styling for smaller screens.

## How the App Works

### Database Setup

Create the MySQL database and tables.

SQL for `users` table:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL
);
```

SQL for `posts` table:

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

If your database name is different, update the DSN in `db.php`.

### Database Connection

`db.php` connects to MySQL using PDO.

### Register Page

`register.php` allows new users to register.

- Collects username and password.
- Uses `password_hash()` to store the password safely.
- Saves the user to the database.
- Redirects to `login.php`.

### Login Page

`login.php` allows users to sign in.

- Finds the user in the database.
- Uses `password_verify()` to check the password.
- Starts a session and saves `user_id` and `username`.
- Redirects to `dashboard.php`.

### Session Control

Protected pages check for a logged-in user:

```php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
```

This keeps pages private for only logged-in users.

### Dashboard

`dashboard.php` shows the welcome screen and links to:

- Create Post
- View Posts
- Logout

### Create Post

`create_posts.php` lets a logged-in user add a post.

- Saves title and content to the `posts` table.
- Redirects to the dashboard or posts page.

### View Posts

`view_posts.php` displays posts with search and page navigation.

- Shows title, content, and date.
- Includes Edit and Delete actions.
- Supports search and pagination.

### Edit Post

`edit_post.php` allows users to update existing posts.

- Loads a post by `id`.
- Shows the current title and content.
- Updates the post when submitted.

### Delete Post

`delete_post.php` removes a post by `id`.

- Deletes the post from the database.
- Redirects back to `view_posts.php`.

### Logout

`logout.php` ends the user session.

- Uses `session_unset()` and `session_destroy()`.
- Redirects to `login.php`.

## Styling

This task adds shared styling with `styles.css`.

- Consistent layout and page design.
- Clear buttons and forms.
- Card-style post display.
- Responsive design for smaller screens.

## How to Run

1. Start Apache and MySQL with XAMPP.
2. Create the database and tables.
3. Put the `Task-3` folder in `C:\xampp\htdocs`.
4. Open `http://localhost/Task-3/register.php`.
5. Register a user and log in.

## Author

Manidweep

ApexPlanet Web Development Internship - Task 3

