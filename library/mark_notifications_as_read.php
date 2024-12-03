<?php
session_start();
include('includes/config.php');

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Update the notifications to mark them as read
$query = "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0";
mysqli_query($con, $query);

exit();  // Ensure the script finishes here without further output
?>
