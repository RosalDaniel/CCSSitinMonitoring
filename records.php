<?php
session_start();
include 'db_connect.php'; // Database connection
include 'sidebar.php'; // Sidebar navigation

// Fetch sit-in records with student names
$result = $conn->query("SELECT r.id, r.idno, CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name, r.purpose, r.lab, 
                        DATE(r.date_time) as date, TIME(r.date_time) as login_time, TIME(r.logout_time) as logout_time 
                        FROM sit_in_records r 
                        JOIN student s ON r.idno = s.idno");

// Fetch data for pie charts
$purposeData = [];
$labData = [];
$purposeQuery = $conn->query("SELECT purpose, COUNT(*) as count FROM sit_in_records GROUP BY purpose");
while ($row = $purposeQuery->fetch_assoc()) {
    $purposeData[$row['purpose']] = $row['count'];
}
$labQuery = $conn->query("SELECT lab, COUNT(*) as count FROM sit_in_records GROUP BY lab");
while ($row = $labQuery->fetch_assoc()) {
    $labData[$row['lab']] = $row['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        canvas {
            max-width: 400px;
            max-height: 400px;
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
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    <div class="content">
        <div class="header">SIT-IN RECORDS</div>
        <div class="chart-container">
            <canvas id="purposeChart"></canvas>
            <canvas id="labChart"></canvas>
        </div>
        <table id="sitInRecords" class="display">
            <thead>
                <tr>
                    <!-- <th>Sit-in Number</th> -->
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Lab</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <!-- <td><?php echo $row['id']; ?></td> -->
                        <td><?php echo $row['idno']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['purpose']; ?></td>
                        <td><?php echo $row['lab']; ?></td>
                        <td><?php echo $row['login_time']; ?></td>
                        <td><?php echo $row['logout_time'] ? $row['logout_time'] : 'Still Logged In'; ?></td>
                        <td><?php echo $row['date']; ?></td>
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
                order: [[6, 'desc']],
                search: true
            });
        });

        // Pie chart data
        const purposeData = <?php echo json_encode($purposeData); ?>;
        const labData = <?php echo json_encode($labData); ?>;

        // Chart for Purpose
        new Chart(document.getElementById('purposeChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(purposeData),
                datasets: [{
                    data: Object.values(purposeData),
                    backgroundColor: ['#C14600', '#FF9D23', '#E5D0AC', '#8D0B41', '#FCF596', '#102C57', '#C63D2F', '#F5F5DC', '#C70039', '#F94C10', '#C40C0C', '#FF6500', '#6C0345'],
                }]
            }
        });

        // Chart for Lab Usage
        new Chart(document.getElementById('labChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(labData),
                datasets: [{
                    data: Object.values(labData),
                    backgroundColor: ['#10375C', '#FFB22C', '#FEF9E1', '#EB5A3C', '#D98324', '#821131', '#F94C10'],
                }]
            }
        });
    </script>
</body>
</html>
