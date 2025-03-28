<?php
include 'sidebar.php';
include 'db_connect.php'; 

// Handle session reset request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reset_sql = "UPDATE student SET sessions = 30";
    if ($conn->query($reset_sql) === TRUE) {
        echo "<script>alert('All sessions have been reset to 30 successfully.'); window.location.href='students.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error resetting sessions: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Sessions</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .container {
            max-width: 500px;
            height: 200px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            margin-bottom: 20px;
        }
        .btn-group {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .confirm-btn {
            background: #112D4E;
            color: white;
        }
        .cancel-btn {
            background: #f44336;
            color: white;
        }
        .confirm-btn:hover {
            background: #3F72AF;
        }
        .cancel-btn:hover {
            background: #ff6b6b;
        }
    </style>
</head>
<body>

<?php renderSidebar(); ?>

<div class="container">
    <h2>Confirm Reset</h2>
    <p>Are you sure you want to reset all students' sessions to 30?</p>
    <form method="POST">
        <div class="btn-group">
            <button type="submit" class="confirm-btn">Reset</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='students.php'">Cancel</button>
        </div>
    </form>
</div>

</body>
</html>
