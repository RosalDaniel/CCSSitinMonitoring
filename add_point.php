<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idno = $_POST['idno'];

    // Example logic: Insert or update reward points
    $existing = $conn->query("SELECT * FROM reward_points WHERE idno = '$idno'");
    
    if ($existing->num_rows > 0) {
        $conn->query("UPDATE reward_points SET points = points + 1 WHERE idno = '$idno'");
        $_SESSION['reward_message'] = "✅ 1 point added to student $idno.";
    } else {
        $conn->query("INSERT INTO reward_points (idno, points) VALUES ('$idno', 1)");
        $_SESSION['reward_message'] = "🎉 First reward point given to student $idno!";
    }

    header("Location: test2.php");
    exit;
}
?>
