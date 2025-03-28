<?php
session_start();
include 'db_connect.php'; 

$record_id = trim($_POST['record_id']);
$feedback_text = trim($_POST['feedback_text']);

if (empty($record_id) || empty($feedback_text)) {
    die("Error: All fields are required.");
}

// Check if feedback already exists
$check_feedback = $conn->query("SELECT * FROM feedback WHERE record_id = '$record_id'");
if ($check_feedback->num_rows > 0) {
    die("Error: Feedback already submitted.");
}

// Insert feedback
$sql = "INSERT INTO feedback (record_id, feedback_text) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $record_id, $feedback_text);

if ($stmt->execute()) {
    $_SESSION["feedback_$record_id"] = true; // Track submitted feedback
    echo "Feedback submitted successfully!";
    header("refresh:2; url=history.php"); 
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
