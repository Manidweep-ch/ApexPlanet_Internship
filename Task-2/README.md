# Task 2 - Simple Blog Application

## Project Summary

This project is a small blog app built with PHP and MySQL. The app lets users:

- Register a new account
- Login
- Create posts
- View posts
- Edit posts
- Delete posts
- Logout

I used PHP sessions for login and PDO for database access.

## Technologies Used

- PHP
- MySQL
- PDO
- HTML
- CSS
- XAMPP
- Git
- GitHub


## Files in the Project

- `db.php` - connects to the database.
- `register.php` - register page for new users.
- `login.php` - login page for users.
- `dashboard.php` - main page after login.
- `logout.php` - logs the user out.
- `create_posts.php` - page to create a post.
- `view_posts.php` - page to see all posts.
- `edit_post.php` - page to edit a post.
- `delete_post.php` - removes a post.
- `README.md` - this file.

## How the App Works

### Database Setup

I created the database in `localhost/phpmyadmin`.

- Open `localhost/phpmyadmin` in your browser.
- Create a database named `blog`.
- Then create the tables `users` and `posts` using SQL queries.

SQL for `users` table:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
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

If you created the database with a different name like `blogs`, update the DSN in `db.php`.

### Database Connection

`db.php` connects to the MySQL database using PDO.

### Register Page

`register.php` lets a user create an account.

- Takes username and password.
- Hashes the password with `password_hash()`.
- Saves the user to the database.
- Redirects to `login.php`.

### Login Page

`login.php` lets the user sign in.

- Checks the username in the database.
- Uses `password_verify()` to check the password.
- Starts a session with `session_start()`.
- Saves `$_SESSION['user_id']` and `$_SESSION['username']`.
- Redirects to `dashboard.php`.

### Session Control

Protected pages use session checks like this:

```php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
```

This keeps pages safe from guests.

### Dashboard

`dashboard.php` shows a welcome message and links to:

- Create Post
- View Posts
- Logout

### Create Post

`create_posts.php` lets a logged-in user add a post.

- The form sends a title and content.
- The post is saved in the `posts` table.

### View Posts

`view_posts.php` shows all posts.

- Finds posts from the database.
- Shows title, content, and date.
- Adds Edit and Delete buttons.

### Edit Post

`edit_post.php` lets the user update a post.

- Loads the post using `id`.
- Shows the current title and content.
- Saves the changes when the form is submitted.

### Delete Post

`delete_post.php` removes a post by `id`.

- Deletes the post from the database.
- Redirects back to `view_posts.php`.

### Logout

`logout.php` ends the session.

- `session_unset()` clears session data.
- `session_destroy()` ends the session.
- Redirects the user to `login.php`.

## Styling

I used simple inline CSS for the pages.

The styling includes:

- centered layout
- styled buttons
- input spacing
- cards for posts
- clean form design

## What I Learned

I learned how to use:

- PHP forms
- sessions
- PDO and prepared statements
- password hashing
- page access control
- basic inline styling

## How to Run

1. Start Apache and MySQL with XAMPP.
2. Create a database called `blog`.
3. Create the `users` and `posts` tables.
4. Put the `Task-2` folder in `C:\xampp\htdocs`.
5. Open this in your browser: `http://localhost/Task-2/register.php`.
6. Register a user and login.

## AI Help Note

I used AI assistance for styling suggestions, debugging support, and improving documentation. I understood the application logic and implemented the project myself as part of the learning process.

## Author

Manidweep

ApexPlanet Web Development Internship - Task 2

