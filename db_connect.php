<?php
    // db_connect.php — Database connection
    $conn = new mysqli('localhost', 'root', '', 'student');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>