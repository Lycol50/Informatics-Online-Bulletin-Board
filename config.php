<?php
// env function
function loadEnv($path = __DIR__)
{
    $envFile = $path . '/.env';

    if (!file_exists($envFile)) {
        return false;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        } else {
            $_ENV[$line] = null;
        }
    }

    return true;
}

// Load environment variables from .env file
// if loadEnv is already loaded, don't load it again
if (!isset($_ENV['DB_HOST'])) {
    loadEnv();
}

// if anyone visit this page redirect to 404 page
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("location: 404.php");
    exit;
}

// mkdir and change permission upload folder in windows
$cmd = "mkdir uploads && icacls uploads /grant Everyone:F /t";
exec($cmd);

// mkdir and change permission upload folder in linux
$cmd = "mkdir uploads && chmod 777 uploads";
exec($cmd);

date_default_timezone_set('Asia/Manila');

// Database connection
define('DB_SERVER', $_ENV['DB_HOST']);
define('DB_USERNAME', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);

// Get connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// create table for users
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    user_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    student_id TEXT NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    grade_level TEXT NOT NULL,
    section INT(2) NOT NULL,
    password TEXT NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    interest TEXT,
    image_user TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_users_table);

// create table for announcements post
$create_announcements_table = "CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    image TEXT,
    user_id INT(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

mysqli_query($conn, $create_announcements_table);

// create table for events
$create_events_table = "CREATE TABLE IF NOT EXISTS events (
    event_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    event_title TEXT NOT NULL,
    event_description TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location TEXT NOT NULL,
    user_id INT(255) NOT NULL,
    announcement_id INT(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id)
)";

mysqli_query($conn, $create_events_table);

// create table for comments
$create_comments_table = "CREATE TABLE IF NOT EXISTS comments (
    comment_id INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    comment TEXT NOT NULL,
    user_id INT(255) NOT NULL,
    ratings INT(1) NOT NULL,
    announcement_id INT(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id)
)";

mysqli_query($conn, $create_comments_table);

// create user data for admin if not exists
$check_admin = "SELECT * FROM users WHERE role = 'admin'";
$admin_result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($admin_result) == 0) {
    $admin_password = password_hash('infoAdmin!', PASSWORD_DEFAULT);
    $create_admin = "INSERT INTO users (student_id, first_name, last_name, grade_level, section, password, role) VALUES ('admin', 'Informatics', 'Administrator', 'Corporate', 'Corporate', '$admin_password', 'admin')";
    mysqli_query($conn, $create_admin);
}