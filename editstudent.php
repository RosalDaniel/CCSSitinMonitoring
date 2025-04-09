<?php
session_start();
include 'auth_check.php';
include 'db_connect.php'; 

// Retrieve student information
$student = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT idno, firstname, middlename, lastname, course, year, email, sessions, address FROM student WHERE idno = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if (!$student) {
        echo "Student not found!";
    }
    $stmt->close();
} else {
    echo "No ID provided!";
}

// Update student info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $email = $_POST['email'] ?? ''; // optional handling
    $sessions = $_POST['sessions'];
    $address = $_POST['address'] ?? '';

    $update_stmt = $conn->prepare("UPDATE student SET firstname = ?, middlename = ?, lastname = ?, course = ?, year = ?, email = ?, sessions = ? WHERE idno = ?");
    $update_stmt->bind_param("ssssssii", $firstname, $middlename, $lastname, $course, $year, $email, $sessions, $id);


    if ($update_stmt->execute()) {
        echo "<script>
                alert('Student information updated successfully!');
                window.location.href = 'students.php';
              </script>";
    } else {
        echo "Error updating student: " . $conn->error;
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; }
        label { margin: 10px 0 5px; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 20px; padding: 10px; background-color: #112D4E; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .reset-button { width: 20%; margin-top: -10px;}
        button:hover { background-color: #3F72AF; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit</h2>

    <?php if ($student): ?>
    <form method="POST" action="editstudent.php">
        <!-- ID Number -->
        <label>ID Number</label>
        <input type="hidden" name="idno" value="<?= htmlspecialchars($student['idno']); ?>">
        <input type="text" value="<?= htmlspecialchars($student['idno']); ?>" readonly>

        <!-- Name -->
        <label>Name</label>
        <input type="hidden" name="firstname" value="<?= htmlspecialchars($student['firstname']); ?>">
        <input type="hidden" name="middlename" value="<?= htmlspecialchars($student['middlename']); ?>">
        <input type="hidden" name="lastname" value="<?= htmlspecialchars($student['lastname']); ?>">
        <input type="text" name="fullname" value="<?= htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']); ?>" readonly>

        <!-- Year -->
        <label>Year Level:</label>
        <input type="number" name="year" value="<?= htmlspecialchars($student['year']); ?>" readonly>

        <!-- Course -->
        <label>Course:</label>
        <input type="text" name="course" value="<?= htmlspecialchars($student['course']); ?>" readonly>

        <!-- Email (optional) -->
        <input type="hidden" name="email" value="<?= htmlspecialchars($student['email']); ?>">

        <!-- Sessions -->
        <label>Remaining Sessions:</label>
        <p id="sessionDisplay"><?= htmlspecialchars($student['sessions']); ?></p>
        <input type="hidden" name="sessions" id="sessionInput" value="<?= htmlspecialchars($student['sessions']); ?>">
        <button type="button" onclick="resetSessions()" class="reset-button">Reset Session</button>

        <!-- Address 
        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($student['address']); ?>" readonly>
        -->
        <button type="submit">Update Student</button>
    </form>
    <?php else: ?>
    <p style="color: red; text-align: center;">Student not found!</p>
    <?php endif; ?>
</div>
<script>
    function resetSessions() {
        document.getElementById('sessionInput').value = 30;
        document.getElementById('sessionDisplay').textContent = 30;
    }
</script>

</body>
</html>
