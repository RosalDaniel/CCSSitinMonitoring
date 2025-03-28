<?php
session_start();
include 'sidebar.php';
include 'db_connect.php'; 

// List of foul words (expand as needed)
$foul_words = ['fuck you', 'yawa', 'pisti']; // Add more as required

// Fetch feedback records with student details
$sql = "
    SELECT 
        f.id AS feedback_id,
        s.idno AS student_id,
        s.lastname, s.firstname, s.middlename,
        r.lab, s.course,
        DATE(r.date_time) AS date,
        TIME(r.date_time) AS login_time,
        TIME(r.logout_time) AS logout_time,
        f.feedback_text
    FROM feedback f
    JOIN sit_in_records r ON f.record_id = r.id
    JOIN student s ON r.idno = s.idno
    ORDER BY r.date_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
        .feedback-cell {
            max-width: 300px; 
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .feedback-cell:hover {
            white-space: normal;
            overflow: visible;
            background: #f8f9fa;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    <div class="content">
        <div class="header">FEEDBACK REPORTS</div>
        <table id="feedbackTable" class="display">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Lab</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Date</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                <?php 
                    $feedback = htmlspecialchars($row['feedback_text']);
                    $contains_foul_word = false;

                    // Check if feedback contains foul words
                    foreach ($foul_words as $word) {
                        if (stripos($feedback, $word) !== false) {
                            $contains_foul_word = true;
                            break;
                        }
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['lastname'] . ", " . $row['firstname'] . " " . $row['middlename']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td><?php echo htmlspecialchars($row['lab']); ?></td>
                    <td><?php echo htmlspecialchars($row['login_time']); ?></td>
                    <td><?php echo $row['logout_time'] ? htmlspecialchars($row['logout_time']) : "Still Logged In"; ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td class="feedback-cell" style="color: <?php echo $contains_foul_word ? 'red' : 'black'; ?>">
                        <?php echo nl2br($feedback); ?>
                        <?php if ($contains_foul_word) : ?>
                            <span style="color: red; font-weight: bold;"> ⚠ Contains inappropriate language</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#feedbackTable').DataTable({
                responsive: true,
                ordering: true,
                search: true
            });
        });
    </script>
</body>
</html>
