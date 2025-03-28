<?php
    include 'sidebar.php';
    include 'db_connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idno = $_POST['idno'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $course = $_POST['course'];
        $year = $_POST['year'];  
        $username = $_POST['username'];
        $email = !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $address = $_POST['address'];
        $sessions = 30;
    
        // Check if username or ID number already exists
        $check_sql = "SELECT * FROM student WHERE username = ? OR idno = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $idno);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            echo "<script>alert('Username or ID Number already exists.'); window.location.href='add_student.php';</script>";
            exit();
        }
    
        // Insert student data
        $sql = "INSERT INTO student (idno, lastname, firstname, middlename, course, year, username, email, password, sessions, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssis", $idno, $lastname, $firstname, $middlename, $course, $year, $username, $email, $password, $sessions, $address);
    
        if ($stmt->execute()) {
            echo "<script>alert('Student added successfully!'); window.location.href='students.php';</script>";
        } else {
            echo "<script>alert('Error adding student: " . $stmt->error . "'); window.location.href='add_student.php';</script>";
        }
    
        $stmt->close();
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
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
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .actions {
            text-align: right;
        }
        .add-btn {
            background: #112D4E;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php renderSidebar(); ?>

<div class="content">
    <div class="header">ADD STUDENT</div>
    <form action="add_student.php" method="POST">
        <label>ID Number</label>
        <input type="text" name="idno" required>

        <label>Last Name</label>
        <input type="text" name="lastname" required>

        <label>First Name</label>
        <input type="text" name="firstname" required>

        <label>Middle Name</label>
        <input type="text" name="middlename">

        <label>Course</label>
        <input type="text" name="course" required>

        <label>Year</label>
        <input type="number" name="year" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Address</label>
        <input type="text" name="address" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>
        
        <div class="actions">
            <button type="submit" class="add-btn">Add Student</button>
        </div>
    </form>
</div>
</body>
</html>
