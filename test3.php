<?php
session_start();
include 'db_connect.php'; // Database connection
include 'sidebar.php'; // Sidebar navigation

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reward_id'], $_POST['reward_idno'])) {
    $id = $_POST['reward_id'];
    $idno = $_POST['reward_idno'];

    // Check if this record was already rewarded
    $check = $conn->prepare("SELECT rewarded FROM sit_in_records WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($rewarded);
    $check->fetch();
    $check->close();

    if ($rewarded == 0) {
        // Fetch reward_points and sessions
        $stmt = $conn->prepare("SELECT reward_points, sessions FROM student WHERE idno = ?");
        $stmt->bind_param("i", $idno);
        $stmt->execute();
        $stmt->bind_result($currentPoints, $sessions);
        $stmt->fetch();
        $stmt->close();

        $currentPoints += 1;

        if ($currentPoints >= 3) {
            $sessions += 1;
            $currentPoints = 0;

            $update = $conn->prepare("UPDATE student SET sessions = ?, reward_points = ? WHERE idno = ?");
            $update->bind_param("iii", $sessions, $currentPoints, $idno);
        } else {
            $update = $conn->prepare("UPDATE student SET reward_points = ? WHERE idno = ?");
            $update->bind_param("ii", $currentPoints, $idno);
        }

        $update->execute();
        $update->close();

        // Mark the sit-in record as rewarded
        $mark = $conn->prepare("UPDATE sit_in_records SET rewarded = 1 WHERE id = ?");
        $mark->bind_param("i", $id);
        $mark->execute();
        $mark->close();
    }

    // Redirect
    header("Location: test3.php");
    exit();
}

// Fetch sit-in records with student names and rewarded status
$result = $conn->query("SELECT r.id, r.idno, CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name, r.purpose, r.lab, 
                        DATE(r.date_time) as date, TIME(r.date_time) as login_time, TIME(r.logout_time) as logout_time,
                        r.rewarded
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
        button[disabled] {
            background-color: gray !important;
            color: white;
            cursor: not-allowed;
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
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Lab</th>
                <th>Login</th>
                <th>Logout</th>
                <th>Date</th>
                <th>Reward</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['idno']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['purpose']; ?></td>
                <td><?php echo $row['lab']; ?></td>
                <td><?php echo $row['login_time']; ?></td>
                <td><?php echo $row['logout_time'] ?: 'Still Logged In'; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td>
                    <form method="POST" action="test3.php" onsubmit="return confirm('Give reward to this student?');">
                        <input type="hidden" name="reward_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="reward_idno" value="<?php echo $row['idno']; ?>">
                        <button type="submit"
                                <?php echo $row['rewarded'] == 1 ? 'disabled' : ''; ?>
                                style="<?php echo $row['rewarded'] == 1 ? 'background-color: gray;' : 'background-color: #28a745;'; ?> color: white; border: none; padding: 5px 10px; cursor: pointer;">
                            <?php echo $row['rewarded'] == 1 ? 'Rewarded' : 'Give Reward'; ?>
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
            order: [[6, 'desc']],
            search: true
        });
    });

    const purposeData = <?php echo json_encode($purposeData); ?>;
    const labData = <?php echo json_encode($labData); ?>;

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
