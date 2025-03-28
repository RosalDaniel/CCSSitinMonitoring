<?php
session_start(); // Start the session
session_destroy(); // Destroy all session data
//echo "<script>alert('You have logged out successfully!'); window.location.href='login.php';</script>"; // Redirect to login page
header("Location: login.php"); 
exit();
?>