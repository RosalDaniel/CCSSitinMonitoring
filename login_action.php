<?php
session_start();

include 'db_connect.php'; 

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input safely
    $username = trim($_POST['username']);
    $password = trim($_POST['psw']);

    // Validate input
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    // Admin credentials (default)
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user'] = 'admin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_home.php");
        exit();
    }

    // Secure SQL query using prepared statements
    $stmt = $conn->prepare("SELECT username, password FROM student WHERE BINARY username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['username']; // Store session
            echo "<script>
            alert('Login successful!');
            window.location.href = 'studenthome.php';
        </script>";            
            exit();
        } else {
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: login.php?error=user_not_found");
        exit();
    }

    $stmt->close(); // Close statement
}

$conn->close(); // Close connection
?>
