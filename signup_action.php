<?php
session_start(); // Start session to store messages

include 'db_connect.php'; 

// Get user input
$idno = $_POST['idno'];
$lastname = $_POST['lastname'];
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$course = $_POST['course'];
$year = $_POST['year'];  
$username = $_POST['username'];
$email = !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
$password = password_hash($_POST['psw-reg'], PASSWORD_DEFAULT); // Hash password
$address = $_POST['address'];
$sessions = 30;

// Check if username or ID number already exists (Secure Prepared Statement)
$check_sql = "SELECT * FROM student WHERE username = ? OR idno = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $username, $idno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Username or ID Number already exists.";
    header("Location: login.php");
    exit();
}

// Insert user data securely
$sql = "INSERT INTO student (idno, lastname, firstname, middlename, course, year, username, email, password, sessions, address) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssis", $idno, $lastname, $firstname, $middlename, $course, $year, $username, $email, $password, $sessions, $address);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registration successful!";
} else {
    $_SESSION['error'] = "Registration failed: " . $stmt->error;
}

    header("Location: login.php");
    exit();

// Close connection
$stmt->close();
$conn->close();
?>
