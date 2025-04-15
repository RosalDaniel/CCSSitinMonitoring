<?php
session_start();
include 'db_connect.php';
include 'sidebar.php';

// Fetch top 5 active students along with their reward points
$query = $conn->query("
    SELECT 
        s.idno, 
        CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name,
        COUNT(r.id) AS sitin_count,
        s.reward_points,
        IFNULL(SUM(r.reward_points), 0) AS accumulated_reward_points
    FROM student s
    LEFT JOIN sit_in_records r ON s.idno = r.idno
    GROUP BY s.idno
    ORDER BY sitin_count DESC
    LIMIT 5
");


// Prepare data for chart
$names = [];
$sitins = [];
$accumulatedPoints = [];
while ($row = $query->fetch_assoc()) {
    $students[] = $row;
    $names[] = $row['name'];
    $sitins[] = $row['sitin_count'];
    $accumulatedPoints[] = $row['accumulated_reward_points'] ? $row['accumulated_reward_points'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            background: #112D4E;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #112D4E;
            color: white;
        }
        .rank-icon {
            font-size: 18px;
        }
        .first { color: gold; }
        .second { color: silver; }
        .third { color: #cd7f32; } /* Bronze */
        .chart-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="content">
    <div class="header">🏆 Top 5 Active Students</div>

    <table>
        <thead>
        <tr>
            <th>Rank</th>
            <th>ID Number</th>
            <th>Name</th>
            <th>Total Sit-ins</th>
            <th>Accumulated Reward Points</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        $rank = 1;
        foreach ($students as $student):
            $rankIcon = '';
            $rankClass = '';
            if ($rank == 1) { $rankIcon = '🥇'; $rankClass = 'first'; }
            else if ($rank == 2) { $rankIcon = '🥈'; $rankClass = 'second'; }
            else if ($rank == 3) { $rankIcon = '🥉'; $rankClass = 'third'; }
        ?>
            <tr>
                <td class="rank-icon <?php echo $rankClass; ?>"><?php echo $rankIcon ?: $rank; ?></td>
                <td><?php echo $student['idno']; ?></td>
                <td><?php echo $student['name']; ?></td>
                <td><?php echo $student['sitin_count']; ?></td>
                <td><?php echo $student['accumulated_reward_points']; ?></td>
            </tr>
        <?php $rank++; endforeach; ?>
        </tbody>
    </table>

    <div class="chart-container">
        <canvas id="sitinChart"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('sitinChart').getContext('2d');
    const sitinChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($names); ?>,
            datasets: [{
                label: 'Total Sit-ins',
                data: <?php echo json_encode($sitins); ?>,
                backgroundColor: '#3E92CC'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
</body>
</html>
