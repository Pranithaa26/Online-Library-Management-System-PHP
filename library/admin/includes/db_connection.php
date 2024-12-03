<?php
// Database credentials
$host = 'localhost'; // Your database host (usually localhost)
$dbname = 'library'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

// Set the path for the log file
$logFile = 'error_log.txt';

try {
    // Create a new PDO instance
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log the error message to the log file with timestamp
    error_log("[" . date('Y-m-d H:i:s') . "] Database connection error: " . $e->getMessage() . "\n", 3, $logFile);

    // Display a user-friendly error message
    echo "Database connection failed. Please try again later.";

    // Stop execution if the connection fails
    die();
}
?>
