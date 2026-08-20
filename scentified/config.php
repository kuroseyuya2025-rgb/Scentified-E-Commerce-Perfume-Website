<?php
// --- LOCALHOST Database Credentials (Based on provided SQL dump) ---
// IMPORTANT: You may need to change DB_USERNAME and DB_PASSWORD if your local setup is different from the XAMPP/WAMP default (root with no password).
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'scentified_db'); 

// --- Session Security Configuration ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // Session ID regeneration is good practice for security
    session_regenerate_id(true);
}

// DB CONNECTION FUNCTION
function get_db_connection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        // Die immediately on connection failure
        die("ERROR: Could not connect to the database. Check config.php credentials and ensure MySQL is running. Error: " . $conn->connect_error);
    }

    return $conn;
}

// Global connection (Used by many pages)
$conn = get_db_connection();

// Protect private pages
function protect_page() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

// Block login/register when logged in
function block_authenticated_access() {
    if (isset($_SESSION['user_id'])) {
        header('Location: home.php');
        exit;
    }
}
?>