
<?php
    include 'sidebar.php';
    include 'db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit In</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        .container {
            padding: 20px;
            flex-grow: 1;
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
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    
    <div class="container">
        <div class="header">CURRENT SIT-IN</div>
        <table id="sitInTable" class="display">
            <thead>
                <tr>
                    <th>Sit ID Number</th>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Sit Lab</th>
                    <th>Date/Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $result = $conn->query("SELECT sit_in_records.id AS sit_id, sit_in_records.idno, student.firstname, student.middlename, student.lastname, sit_in_records.purpose, sit_in_records.lab, sit_in_records.date_time FROM sit_in_records JOIN student ON sit_in_records.idno = student.idno WHERE sit_in_records.logout_time IS NULL");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fullName = $row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname'];
                            echo "<tr>
                                <td>{$row['sit_id']}</td>
                                <td>{$row['idno']}</td>
                                <td>{$fullName}</td>
                                <td>{$row['purpose']}</td>
                                <td>{$row['lab']}</td>
                                <td>{$row['date_time']}</td>
                                <td><button class='logout-btn' onclick='logoutStudent({$row['sit_id']})'>Time Out</button></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#sitInTable').DataTable();
        });

        function logoutStudent(sitId) {
            if (confirm("Are you sure you want to log out this student?")) {
                window.location.href = `logout_student.php?sit_id=${sitId}`;      
            }
        }
    </script>
</body>
</html>

<?php
    $conn->close();
?>
