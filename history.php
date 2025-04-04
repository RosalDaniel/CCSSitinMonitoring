 <?php
    session_start();
    include 'auth_check.php';
    include 'student_sidebar.php';
    include 'db_connect.php'; 

    $username = $conn->real_escape_string($_SESSION['user']);

    // Fetch sit-in records for the logged-in student
    $result = $conn->query("
        SELECT r.id, r.idno, CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name, 
            r.purpose, r.lab, DATE(r.date_time) as date, 
            TIME(r.date_time) as login_time, TIME(r.logout_time) as logout_time 
        FROM sit_in_records r 
        JOIN student s ON r.idno = s.idno
        WHERE s.username = '$username' AND r.logout_time IS NOT NULL ORDER BY r.date_time DESC
    ");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
        .feedback-btn {
            background-color: #112D4E;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .feedback-btn:hover {
            background-color: #3F72AF;
        }
        .disabled-btn {
            background-color: #ccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }
        .given-feedback {
            background-color: #3F72AF !important; /* Light blue */
            color: white !important;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    <div class="content">
        <div class="header">HISTORY INFORMATION</div>
        
        <table id="sitInRecords" class="display">
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Laboratory</th>
                    <th>Date</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) : 
                $record_id = $row['id'];
                $feedback_check = $conn->query("SELECT * FROM feedback WHERE record_id = '$record_id'");
                $feedback_given = $feedback_check->num_rows > 0;
            ?>
                <tr>
                    <td><?php echo $row['idno']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['purpose']; ?></td>
                    <td><?php echo $row['lab']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['login_time']; ?></td>
                    <td><?php echo $row['logout_time'] ?: 'Still Logged In'; ?></td>
                    <td>
                        <form action="feedback.php" method="get">
                            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit" class="feedback-btn <?php echo $feedback_given ? 'given-feedback' : ''; ?>" 
                                    id="btn-<?php echo $record_id; ?>" 
                                    <?php echo $feedback_given ? 'disabled' : ''; ?>>
                                <?php echo $feedback_given ? 'Feedback Given' : 'Give Feedback'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#sitInRecords').DataTable({
                responsive: true,
                ordering: true,
                order: [[4, 'desc']],
                search: true
            });
        });
    </script>
</body>
</html>
