# ApexPlanet Internship - Task 1

This task focuses on setting up a local PHP and MySQL development environment and configuring Git and GitHub for version control.

## Tools Used

- XAMPP
- PHP
- MySQL
- Git
- GitHub
- VS Code

## Step 1 - Install XAMPP

1. Downloaded XAMPP from the official website.
2. Installed XAMPP with Apache, MySQL, and PHP components.
3. Opened the XAMPP Control Panel.

## Step 2 - Start Apache and MySQL

1. Started Apache service.
2. Started MySQL service.
3. Verified that both services were running successfully.

## Step 3 - Verify Local Server

1. Opened browser.
2. Visited:

http://localhost

3. Verified that the XAMPP dashboard opened successfully.

## Step 4 - Create Project Folder

Created a new folder inside:

C:\xampp\htdocs

Folder name:

apexplanet

## Step 5 - Create First PHP File

Created:

index.php

Added a simple PHP program to verify that PHP was working correctly.

Example:

```php
<?php
echo "ApexPlanet Internship Task 1";
?>
```

## Step 6 - Run the Project

Opened:

http://localhost/apexplanet

Verified that the PHP output was displayed successfully.

## Step 7 - Install Git

1. Installed Git.
2. Verified installation using:

```bash
git --version
```

## Step 8 - Create Git Repository

Initialized Git inside the project folder:

```bash
git init
```

Added project files:

```bash
git add .
```

Created first commit:

```bash
git commit -m "Initial commit"
```

## Step 9 - Connect GitHub Repository

Created a GitHub repository.

Connected local repository:

```bash
git remote add origin <repository-url>
```

Pushed project files to GitHub.

## Project Structure

```text
apexplanet/
│
├── index.php
└── README.md
```

## Learning Outcomes

Through this task I learned:

- Local server setup using XAMPP
- Basic PHP project structure
- Understanding htdocs and localhost
- Git fundamentals
- GitHub workflow
- Creating and managing repositories

## Author

Manidweep