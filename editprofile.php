<?php
    session_start();
    include 'auth_check.php';
    include 'db_connect.php'; 
    include 'student_sidebar.php';

    // Ensure session variable exists
    if (!isset($_SESSION['user'])) {
        die("Error: User not logged in.");
    }
    
    // Retrieve student information
    $username = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT idno, lastname, firstname, middlename, course, year, email, address, profile_image FROM student WHERE username = ?");
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
    <title>Edit Profile</title>
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
        .save-btn {
            background: #112D4E;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .image-container {
            text-align: center;
            margin: 50px 0 50px;
        }
        .student-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%; /* Makes the image circular */
            object-fit: cover;
            border: 1px solid #DBE2EF;
        }
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    <div class="content">
        <div class="header">EDIT PROFILE</div>
        <form id="editProfileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="image-container">
                <?php if (!empty($student['profile_image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($student['profile_image']); ?>" alt="Profile Image" class="student-photo">
                <?php endif; ?>
            </div>

            <label>Profile Image</label>
            <input type="file" name="profile_image" accept="image/*">
            
            <label>ID No</label>
            <input type="text" name="idno" value="<?= htmlspecialchars($student['idno']); ?>" disabled>
            <input type="hidden" name="idno" value="<?= htmlspecialchars($student['idno']); ?>">

            <label>First Name</label>
            <input type="text" name="firstname" value="<?= htmlspecialchars($student['firstname']); ?>" required>

            <label>Middle Name</label>
            <input type="text" name="middlename" value="<?= htmlspecialchars($student['middlename']); ?>">

            <label>Last Name</label>
            <input type="text" name="lastname" value="<?= htmlspecialchars($student['lastname']); ?>" required>

            <label>Course</label>
            <input type="text" name="course" value="<?= htmlspecialchars($student['course']); ?>" required>

            <label>Year</label>
            <input type="text" name="year" value="<?= htmlspecialchars($student['year']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($student['address']); ?>" required>

            <div class="actions">
                <button type="submit" class="save-btn">Save</button>
            </div>
        </form>

    </div>
</body>
</html>
