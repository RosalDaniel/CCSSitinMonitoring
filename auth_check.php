<?php

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
