<?php
session_start();
include 'auth_check.php';
include 'db_connect.php'; 

// Retrieve student information
$username = $_SESSION['user'];
$stmt = $conn->prepare("SELECT idno, firstname, lastname, middlename, course, year, email, sessions, address FROM student WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Reservation</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.reservation-container {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 3px 3px 15px rgba(0, 0, 0, 0.1);
    width: 400px;
}

h2 {
    text-align: center;
    color: #5a3772;
    margin-bottom: 20px;
    font-size: 24px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
    font-size: 15px;
    color: #333;
}

input, select {
    width: calc(100% - 24px); /* Ensures all fields have the same width */
    padding: 12px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    transition: 0.3s ease-in-out;
    display: block;
    margin: auto;
}

input:disabled {
    background: #e9ecef;
    font-weight: bold;
    color: #555;
}

select {
    cursor: pointer;
}

input:focus, select:focus {
    border-color: #5a3772;
    background: #fff;
    outline: none;
    box-shadow: 0px 0px 5px rgba(90, 55, 114, 0.3);
}

button {
    width: 100%;
    padding: 14px;
    font-size: 18px;
    border-radius: 8px;
    border: none;
    background: #5a3772;
    color: white;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;
}

button:hover {
    background: rgb(165, 99, 209);
}

    </style>
</head>
<body>

    <div class="reservation-container">
        <h2>Lab Reservation</h2>
        <form action="submit_reservation.php" method="POST">
            <!-- ID Number (Pre-filled) -->
            <div class="form-group">
                <label>ID Number:</label>
                <input type="text" name="idno" value="<?= htmlspecialchars($student['idno']); ?>" disabled>
            </div>

            <!-- Student Name (Pre-filled) -->
            <div class="form-group">
                <label>Student Name:</label>
                <input type="text" name="student_name" 
                    value="<?= htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']); ?>" disabled>
            </div>

            <!-- Purpose -->
            <div class="form-group">
                <label>Purpose:</label>
                <select name="purpose" required>
                    <option value="" disabled selected>Select a purpose</option>
                    <option value="C Programming">C Programming</option>
                    <option value="Java Programming">Java Programming</option>
                    <option value="C# Programming">C# Programming</option>
                    <option value="PHP Programming">PHP Programming</option>
                    <option value="ASP.Net Programming">ASP.Net Programming</option>
                </select>
            </div>

            <!-- Lab Selection -->
            <div class="form-group">
                <label for="lab">Lab:</label>
                <input type="text" id="lab" name="lab" required>
            </div>
            <div class="form-group">
                <label for="time-in">Time in:</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="sessions">Remaining Sessions:</label>
                <input type="number" name="sessions" value="<?= htmlspecialchars($student['sessions']); ?>" disabled>
            </div>


            

            <!-- Submit Button -->
            <button type="submit">Reserve Now</button>
        </form>
    </div>

</body>
</html>
