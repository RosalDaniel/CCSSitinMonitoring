
<?php
    include 'sidebar.php';
    include 'db_connect.php'; 

    // Fetch student data
    $sql = "SELECT idno, firstname, middlename, lastname, year, course, sessions, profile_image FROM student";
    $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #112D4E;
            color: white;
        }
        .actions {
            text-align: center; /* Centers horizontally */
        }
        .actions button {
            display: inline-block; 
            margin: 5px;
            padding: 8px 15px; /* Ensures uniform size */
            font-size: 14px; /* Makes text size consistent */
            border: none;
            border-radius: 5px; /* Rounded edges for consistency */
            cursor: pointer;
            width: 80px; /* Forces buttons to have the same width */
            text-align: center;
        }
        .header-btn {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .edit-btn {border: none; background: none; color: #112D4E; }
        .delete-btn, .reset-btn { background-color: #f44336; color: white; }
        .add-btn, .reset-btn {
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin: 10px;
        }
        .add-btn:hover, .edit-btn:hover {
            background: #3F72AF;
        }
        .add-btn {
            background: #112D4E;
        }
        .reset-btn:hover, .delete-btn:hover {
            background:rgb(244, 172, 156);
        }
        .image-cell {
            text-align: center; /* Centers content horizontally */
            vertical-align: middle; /* Centers content vertically */
        }

        .student-img {
            display: block; /* Ensures it's treated as a block element */
            margin: 0 auto; /* Centers it inside the cell */
            width: 50px; /* Adjust as needed */
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php renderSidebar(); ?>

<div class="content">
    <div class="header">STUDENTS INFORMATION</div>
    <div class="header-btn">
        <button onclick="window.location.href='add_student.php'" class="add-btn" title="Add student">Add Student</button>
        <button onclick="window.location.href='reset_sessions.php'" class="reset-btn" title="Reset sessions">Reset All Session</button>
    </div>
    <table id="studentsInfo" class="display">
        <thead>
            <tr>
                <th>Image</th> <!-- New Image Column -->
                <th>ID Number</th>
                <th>Name</th>
                <th>Year</th>
                <th>Course</th>
                <th>Sessions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='image-cell'>";
                    
                    // Check if the student has an image, else show a default
                    $imagePath = !empty($row['profile_image']) ? "uploads/{$row['profile_image']}" : "image/default-image.png";
                    echo "<img src='$imagePath' alt='Profile' class='student-img'>";
                    echo "</td>";
                    echo "<td>{$row['idno']}</td>";
                    echo "<td>{$row['firstname']} {$row['middlename']} {$row['lastname']}</td>";
                    echo "<td>{$row['year']}</td>";
                    echo "<td>{$row['course']}</td>";
                    echo "<td>{$row['sessions']}</td>";
                    echo "<td class='actions'>
                            <a href='editstudent.php?id={$row['idno']}'><button class='edit-btn' title='Edit student'>Edit</button></a>
                            <button class='delete-btn' onclick=\"if(confirm('Are you sure you want to delete this student?')) window.location.href='deletestudent.php?id={$row['idno']}'\" title='Delete student'>Delete</button>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No students found</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>

</div>
    <script>
        $(document).ready(function() {
            $('#studentsInfo').DataTable({
                responsive: true,
                ordering: true
            });
        });
    </script>

</body>
</html>
