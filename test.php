<?php
session_start();
include 'auth_check.php';
include 'student_sidebar.php';
include 'db_connect.php';

$username = $conn->real_escape_string($_SESSION['user']);
$studentData = $conn->query("SELECT idno, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM student WHERE username = '$username'")->fetch_assoc();

$message = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idno = $conn->real_escape_string($_POST['idno']);
    $datetime = $conn->real_escape_string($_POST['date_time']);
    $lab = $conn->real_escape_string($_POST['lab']);
    $purpose = $conn->real_escape_string($_POST['purpose']);

    // Conflict checking
    $check = $conn->query("SELECT * FROM sit_in_records WHERE lab = '$lab' AND date_time = '$datetime'");
    if ($check->num_rows > 0) {
        $error = "A reservation already exists for that date/time and lab.";
    } else {
        $insert = $conn->query("INSERT INTO sit_in_records (idno, purpose, lab, date_time) VALUES ('$idno', '$purpose', '$lab', '$datetime')");
        if ($insert) {
            $message = "Reservation submitted successfully!";
        } else {
            $error = "Failed to submit reservation. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
        }
        .sidebar {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
            flex-shrink: 0;
            background-color: #112D4E;
            color: white;
            min-height: 100vh;
            height: auto;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 10px 0;
        }
        .sidebar a i {
            width: 20px;
            text-align: center;
        }
        .sidebar a:hover {
            background-color: #3F72AF;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            background: #112D4E;
            color: #fff;
            padding: 20px 30px;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }
        form input[type="text"],
        form input[type="datetime-local"],
        form select,
        form textarea {
            width: 100%;
            box-sizing: border-box; /* ensures padding doesn't exceed container */
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-btn {
            background-color: #112D4E;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #3F72AF;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="content">
    <div class="header">SIT-IN RESERVATION</div>

    <?php if ($message): ?>
        <div class="alert success"><?php echo $message; ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="idno" value="<?php echo htmlspecialchars($studentData['idno']); ?>">

        <div class="form-group">
            <label>Student Name</label>
            <input type="text" value="<?php echo htmlspecialchars($studentData['name']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="date_time">Date & Time</label>
            <input type="datetime-local" name="date_time" id="date_time" required>
        </div>

        <div class="form-group">
            <label for="lab">Preferred Seat / Area</label>
            <select name="lab" id="lab" required>
                <option value="">-- Select Lab --</option>
                <option value="Lab A">Lab A</option>
                <option value="Lab B">Lab B</option>
                <option value="Lab C">Lab C</option>
            </select>
        </div>

        <div class="form-group">
            <label for="purpose">Purpose (max 200 characters)</label>
            <textarea name="purpose" id="purpose" maxlength="200" required></textarea>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">Submit Reservation</button>
    </form>
</div>

<script>
    function validateForm() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerText = "Submitting...";
        return true;
    }
</script>
</body>
</html>
