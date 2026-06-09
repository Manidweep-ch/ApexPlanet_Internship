# Task 5 - Blog Application with Comments System & User Management

## Project Summary

This project is a PHP/MySQL blog application updated for Task 5. It now includes a complete comments system, user discovery, admin user management panel, and user profile pages. All Task 4 features are preserved and enhanced.

The app now supports:

- Registering accounts with roles: `user`, `admin`
- Logging in with secure password verification
- Creating posts linked to the logged-in user
- Viewing all posts or just the current user's posts
- Searching posts by title or content
- Paginating posts with preserved search state
- **[NEW] Commenting on posts** with full CRUD operations
- **[NEW] Admin can manage all comments**; users manage only their own
- **[NEW] Finding and browsing other users** with pagination
- **[NEW] Viewing user profiles** with all their posts
- **[NEW] Admin user management panel** to change roles and delete users
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

### Core Files (Task 1-4)
- `db.php` - secure PDO database connection.
- `register.php` - new account registration with role selection.
- `login.php` - user authentication and session creation.
- `dashboard.php` - protected user dashboard with links to all features.
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

### New Files (Task 5)

#### Comments System
- `comments.php` - view post details with all comments and add new comments.
- `edit_comment.php` - edit comments (admin or comment author only).
- `delete_comment.php` - delete comments (admin or comment author only).

#### User Discovery & Management
- `find_users.php` - search and browse all users with pagination (available to all logged-in users).
- `user_posts.php` - view a specific user's profile and all their posts with search and pagination.
- `manage_users.php` - admin-only panel to manage users, change roles, and delete users.

## Task 5 Improvements

### 1. Comments System

**New Database Table:**
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
```

**Features:**
- All logged-in users can add comments to any post
- Comments display author username and creation timestamp
- Admin can edit/delete any comment
- Regular users can edit/delete only their own comments
- Comment validation prevents empty submissions
- All operations use PDO prepared statements to prevent SQL injection

**User Flow:**
1. View any post → Click "+ Comments" button
2. See all existing comments with author and date
3. Add new comment (validation required)
4. Edit/Delete own comments (or any comment if admin)

### 2. User Discovery & Public Profiles

**New Pages:**

**find_users.php** - Available to all logged-in users
- Search users by username with real-time filtering
- Pagination (10 users per page)
- Display username, role, and total post count for each user
- Click any username or "View Posts" button to view their posts

**user_posts.php** - User profile page
- Display user's profile summary (username, role, total posts)
- Show all posts created by that user
- Search user's posts by title or content
- Pagination for user's posts (3 posts per page)
- Access comments from this page too

### 3. Admin User Management Panel

**New Page:** `manage_users.php` - Admin-only

**Features:**
- Search users by username with pagination (10 users per page)
- Display username, role, and post count in table format
- Change user roles (User ↔ Admin)
- Delete users (respects ON DELETE CASCADE for posts/comments)
- Cannot self-modify (admin cannot change their own role or delete themselves)
- Confirmation dialogs before destructive actions
- All operations use PDO prepared statements

**Access Control:**
- Only admins can access this page
- Non-admin users receive "Access Denied" if they try to access directly

### 4. Dashboard Enhancements

**Updated dashboard.php** now includes:
- **Find Users** button - Available to all users
- **Manage Users** button - Visible only to admin users

### 5. Comments Button on View Posts

**Updated view_posts.php:**
- Added "+ Comments" button on every post
- Button appears for all logged-in users (not conditional)
- Separate from Edit/Delete buttons (which remain conditional for admins/post authors)

## Database Schema Changes (Task 5)

### New `comments` Table

```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
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
- Links to: Create Post, View Posts, Find Users, Edit Profile, Logout
- Admin users also see: Manage Users link

### Create Post (`create_posts.php`)

- Only available to logged-in users.
- Saves the new post with the current user as `user_id`.
- Uses server-side validation for title and content.

### View Posts (`view_posts.php`)

- Displays all posts with search and pagination.
- Shows edit/delete buttons only when the current user is allowed.
- Admin users can manage any post.
- Other users can manage only their own posts.
- **NEW:** All users can click "+ Comments" button on every post.

### Find Users (`find_users.php`) - **NEW**

- Available to all logged-in users.
- Search other users by username.
- Browse paginated list of users (10 per page).
- See username, role, and post count for each user.
- Click username or "View Posts" to see that user's profile and posts.

### User Posts (`user_posts.php`) - **NEW**

- Display selected user's profile information.
- Show all posts created by that user.
- Search within that user's posts.
- Pagination support (3 posts per page).
- Access comments from user's posts.
- Can be accessed from find_users.php or manage_users.php.

### Comments (`comments.php`) - **NEW**

- Displays complete post details.
- Shows all comments for the post with author names and timestamps.
- Logged-in users can add new comments.
- Comment validation ensures non-empty submissions.
- Edit/Delete buttons appear for comment authors and admins.

### Edit Comment (`edit_comment.php`) - **NEW**

- Only accessible to comment author or admin.
- Validates comment text (minimum 2 characters).
- Updates comment in database using PDO prepared statements.
- Redirects back to comments page after update.

### Delete Comment (`delete_comment.php`) - **NEW**

- Only accessible to comment author or admin.
- Deletes comment from database.
- Respects ON DELETE CASCADE for referential integrity.
- Redirects back to comments page after deletion.

### Manage Users (`manage_users.php`) - **NEW**

- Admin-only page for user management.
- Search users by username with pagination (10 per page).
- Display table with: Username, Role, Total Posts, Actions.
- Change user roles (Promote to Admin / Demote to User).
- Delete users (with confirmation).
- Cannot self-modify (cannot change own role or delete self).

### My Posts (`my_posts.php`)

- Displays only the currently logged-in user's posts.
- Uses the same pagination logic as `view_posts.php`.

### Edit Post (`edit_post.php`)

- Verifies post ownership or admin access.
- Validates updated title and content.

### Delete Post (`delete_post.php`)

- Verifies post ownership or admin access.
- Deletes the post and redirects back to viewing posts.

### Edit Profile (`edit_profile.php`)

- Allows the logged-in user to change their username.
- Allows the logged-in user to update their password.
- Validates username length and optional password length.
- Updates the session username after a successful change.

### Search and Pagination

- `search.php` builds the query filter for title/content search.
- `pagination.php` runs count and page queries safely.
- Search state is preserved across page links.

## Security Features

- All database queries use PDO prepared statements to prevent SQL injection.
- Passwords are stored using `password_hash()` and validated with `password_verify()`.
- Role-based access control (RBAC) prevents unauthorized editing and deletion.
- Server-side validation enforces required fields and length limits.
- Client-side HTML validation improves user experience.
- XSS prevention via `htmlspecialchars()` on all user output.
- Authorization checks on all protected operations.
- Confirmation dialogs for destructive actions.

## How to Run

1. Start Apache and MySQL in XAMPP.
2. Create or update the database tables using the SQL above.
3. Place the `Task-5` folder in `C:\xampp\htdocs`.
4. Open `http://localhost/Task-5/register.php`.
5. Create a user account and log in.
6. Explore all features: Create posts, find users, comment on posts, manage users (if admin).

## User Roles

### Admin Role
- Create, edit, and delete any post
- Edit and delete any comment
- Access the admin Manage Users panel
- Change user roles and delete users
- View all posts and comments

### User Role
- Create, edit, and delete only their own posts
- Comment on any post
- Edit and delete only their own comments
- View all posts and search users
- Browse user profiles and their posts

## Testing Checklist

- [ ] Register and login with different roles
- [ ] Create and view posts
- [ ] Search posts by title/content
- [ ] Edit and delete own posts (user role)
- [ ] Edit and delete any post (admin role)
- [ ] Comment on posts
- [ ] Edit own comments
- [ ] Delete own comments (or any comment as admin)
- [ ] Find and search users
- [ ] View user profiles and their posts
- [ ] Admin: Manage Users panel - change roles
- [ ] Admin: Manage Users panel - delete users
- [ ] Verify access denied for unauthorized operations
- [ ] Verify pagination works on all pages
- [ ] Verify search state is preserved across pages

## Author

Manidweep

ApexPlanet Web Development Internship - Task 5

