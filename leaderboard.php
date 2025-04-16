<?php
session_start();
include 'db_connect.php';
include 'sidebar.php';

// Fetch top 5 active students along with total sit-in duration
$query = $conn->query("
    SELECT 
        s.idno, 
        CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name,
        COUNT(r.id) AS sitin_count,
        SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, r.date_time, r.logout_time))) AS total_duration,
        s.profile_image
    FROM student s
    LEFT JOIN sit_in_records r ON s.idno = r.idno
    GROUP BY s.idno
    ORDER BY sitin_count DESC
    LIMIT 10
");

// Prepare data for chart
$names = [];
$sitins = [];
$students = [];
while ($row = $query->fetch_assoc()) {
    $students[] = $row;
    $names[] = $row['name'];
    $sitins[] = $row['sitin_count'];
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
        body {
            font-family: Arial, sans-serif;
            background: #081524;
            color: #f1f5f9;
        }

        .leaderboard-wrapper {
            padding: 40px; 
            max-width: 1200px;
            margin: auto;
            margin-top: -1px;
        }

        .leaderboard-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .top-three {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .top-card {
            background: #1e293b;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            width: 220px;
            position: relative;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        .top-card .profile-pic {
            width: 80px;
            height: 80px;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            font-size: 36px;
            line-height: 80px;
            margin: 0 auto 10px;
        }

        .top-card .name {
            font-size: 18px;
            margin: 5px 0;
        }

        .top-card .idno {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .top-card .score, .top-card .duration {
            font-weight: bold;
            margin: 4px 0;
        }

        .top-card .rank-label {
            margin-top: 10px;
            font-size: 14px;
            color: #facc15;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            background: #1e293b;
            border-radius: 10px;
            overflow: hidden;
        }

        .leaderboard-table th, .leaderboard-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #334155;
        }

        .leaderboard-table th {
            background-color: #0f172a;
            color: #facc15;
        }

        .leaderboard-table tr:hover {
            background-color: #334155;
        }

    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="leaderboard-wrapper">
    <h1 class="leaderboard-title">🏆 Top Active Students</h1>

    <div class="top-three">
        <?php for ($i = 0; $i < min(3, count($students)); $i++): 
            $student = $students[$i];
            $rank = $i + 1;
        ?>
        <div class="top-card rank-<?php echo $rank; ?>">
            <div class="profile-pic"><img src="uploads/<?= htmlspecialchars($student['profile_image']); ?>" alt="Profile Image" class="profile-pic"></div>
            <h2 class="name"><?php echo $student['name']; ?></h2>
            <p class="idno">@<?php echo $student['idno']; ?></p>
            <p class="score">Sit-ins: <?php echo $student['sitin_count']; ?></p>
            <p class="duration">Duration: <?php echo $student['total_duration'] ?? '00:00:00'; ?></p>
            <p class="rank-label">Rank #<?php echo $rank; ?></p>
        </div>
        <?php endfor; ?>
    </div>

    <div class="table-wrapper">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>RANK</th>
                    <th>NAME</th>
                    <th>ID NUMBER</th>
                    <th>TOTAL SIT-INS</th>
                    <th>TOTAL DURATION</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 3; $i < count($students); $i++): 
                    $student = $students[$i];
                ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo $student['name']; ?></td>
                    <td>@<?php echo $student['idno']; ?></td>
                    <td><?php echo $student['sitin_count']; ?></td>
                    <td><?php echo $student['total_duration'] ?? '00:00:00'; ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
