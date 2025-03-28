<?php
session_start();
include 'auth_check.php';
include 'db_connect.php'; 
include 'student_sidebar.php';


$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : '';
if (!$record_id) {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
</head>
<body>
    <div>
        <h2>Give Feedback</h2>
        <form action="feedback_action.php" method="post">
            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record_id); ?>">
            <textarea name="feedback_text" required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>
