<?php
session_start();
include 'sidebar.php';
include 'db_connect.php';

try {
    // Secure SQL query using prepared statements
    $stmt = $conn->prepare("
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
        ORDER BY r.date_time DESC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Foul words filtering using regex
$foul_words = ['fuck', 'yawa', 'pisti', 'boang', 'bogo', 'shit'];
function containsFoulWords($text, $words) {
    $pattern = '/' . implode('|', array_map('preg_quote', $words)) . '/i';
    return preg_match($pattern, $text);
}
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
        :root {
    --primary-color: #112D4E;
    --secondary-color: #F9F9F9;
    --text-color: #1F2937;
    --warning-color: #E63946;
    --border-radius: 8px;
    --transition-speed: 0.3s;
}

body {
    font-family: 'Arial', sans-serif;
    background: var(--secondary-color);
    color: var(--text-color);
    display: flex;
}

.content {
    flex-grow: 1;
    padding: 20px;
}

.header {
    background: var(--primary-color);
    color: white;
    text-align: center;
    font-size: 1.5rem;
    padding: 15px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
}

th {
    background-color: var(--primary-color);
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}
#feedbackTable{
    padding-top: 20px;
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
    background: var(--secondary-color);
    padding: 5px;
    border-radius: var(--border-radius);
}

.flagged {
    color: var(--warning-color);
    font-weight: bold;
}

.warning {
    color: var(--warning-color);
    font-size: 0.85rem;
    font-weight: bold;
    display: block;
}

.status-in {
    font-weight: bold;
    color: green;
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
                        $contains_foul_word = containsFoulWords($feedback, $foul_words);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars("{$row['lastname']}, {$row['firstname']} {$row['middlename']}"); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['lab']); ?></td>
                        <td><?php echo htmlspecialchars($row['login_time']); ?></td>
                        <td><?php echo $row['logout_time'] ? htmlspecialchars($row['logout_time']) : "<span class='status-in'>Still Logged In</span>"; ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td class="feedback-cell <?php echo $contains_foul_word ? 'flagged' : ''; ?>">
                            <?php echo nl2br($feedback); ?>
                            <?php if ($contains_foul_word) : ?>
                                <span class="warning">⚠ Inappropriate Language</span>
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
                search: true,
                order: [[6, 'desc']]
            });
        });
    </script>
</body>
</html>
