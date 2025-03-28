<?php   
    
    include 'db_connect.php'; 

    // Fetch purpose data for chart
    $purpose = [];
    $result = $conn->query("SELECT purpose, COUNT(*) as count FROM sit_in_records GROUP BY purpose");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $purpose[$row['purpose']] = $row['count'];
        }
    }

    // Fetch total sit-in count
    $sitInCountResult = $conn->query("SELECT COUNT(*) as totalSitIn FROM sit_in_records");
    $totalSitIn = $sitInCountResult->fetch_assoc()['totalSitIn'];

    // Fetch current sit-in count
    $currentSitInResult = $conn->query("SELECT COUNT(*) as currentSitIn FROM sit_in_records WHERE logout_time IS NULL");
    $currentSitIn = $currentSitInResult->fetch_assoc()['currentSitIn'];

    // Fetch total registered students count
    $registeredStudentsResult = $conn->query("SELECT COUNT(*) as totalStudents FROM student");
    $totalStudents = $registeredStudentsResult->fetch_assoc()['totalStudents'];

    // Fetch announcements
    $announcementResult = $conn->query("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC");
?>
<?php
    include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            height: 135vh;
        }
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #112D4E;
            color: #fff;
            padding: 20px 30px;
            text-align: center;
            font-size: 24px;
            border-radius: 5px;
        }
        .announcement {
            margin-top: 10px;
        }
        #sitInChart {
            width: 100%;
            height: 100px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 50%;
            margin-top: -0px;
        }
        .modal-content form {
            display: grid;
            gap: 15px;
        }
        .modal-content label {
            font-weight: bold;
        }
        .modal-content input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 20px);
        }
        .modal-content button {
            padding: 10px;
            border-radius: 5px;
        }
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close-btn:hover {
            color: #000;
            cursor: pointer;
        }

        form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    label {
        font-weight: bold;
        font-size: 16px;
        color: #112D4E;
    }

    input[type="text"],
    textarea {
        width: 96%;
        padding: 10px;
        border: 2px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        transition: border 0.3s ease;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #112D4E;
        box-shadow: 0 0 5px rgba(90, 55, 114, 0.3);
    }

    textarea {
        resize: vertical;
    }

    button[type="submit"] {
        background: #112D4E;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
        background: #3F72AF;
    }
    </style>
</head>
<body>
    <?php renderSidebar(); ?>
    
    <div class="container" >
        <div class="header">COLLEGE OF COMPUTER STUDIES ADMIN</div>
                <a href="#" style="margin: 5px;">
                    <button type="submit" onclick="openSearchModal()" title="Search Student to Sit-in">Search Student</button>
                </a>
        <div style="display: flex; gap: 20px;">
            <div id="sitInStats" class="card" style="flex: 1;">
                <h2>📊 Statistics</h2>
                <p><strong>Students Registered:</strong> <span><?php echo $totalStudents; ?></span></p>
                <p><strong>Currently Sit-in:</strong> <span id="currentSitInCount"><?php echo $currentSitIn; ?></span></p>
                <p><strong>Total Sit-in:</strong> <span><?php echo $totalSitIn; ?></span></p>
                <canvas id="sitInChart"></canvas>
            </div>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_announcement'])) {
                $title = $conn->real_escape_string($_POST['title']);
                $content = $conn->real_escape_string($_POST['content']);
                
                // Set author as 'Admin'
                $sql = "INSERT INTO announcements (title, content, author) VALUES ('$title', '$content', 'Admin')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Announcement posted successfully!');</script>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            ?>

            <div id="announcements" class="card" style="flex: 1;">
                <h2>📢 Post New Announcement</h2>
                <form method="POST">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                    
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="3" required></textarea>

                    <button type="submit" name="post_announcement" style="margin-top: 10px; background: #112D4E; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer;" title="Post Announcement">
                        Post Announcement
                    </button>
                </form>

                <h2>📢 Announcements</h2>
                <?php
                if ($announcementResult->num_rows > 0) {
                    while ($row = $announcementResult->fetch_assoc()) {
                        echo "<div class='announcement'>";
                        echo "<strong>" . $row['title'] . " | " . date('F j, Y', strtotime($row['created_at'])) . "</strong>";
                        echo "<p>" . $row['content'] . "</p>";
                        echo "<hr>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No announcements yet.</p>";
                }
                ?>
            </div>

        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $searchId = $conn->real_escape_string($_POST['searchId']);
            $sql = "SELECT idno, firstname, lastname, middlename, sessions FROM student WHERE idno = '$searchId'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $fullName = $row['firstname'] . " " . $row['middlename'] . " " . $row['lastname'];
                echo "<script>document.addEventListener('DOMContentLoaded', () => { closeSearchModal(); openStudentInfoModal(); });</script>";
                ?>
                
                <div id="studentInfoModal" class="modal" style="display: block;">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeStudentInfoModal()">&times;</span>
                        <h3>Sit-in Form</h3>
                        <form id="sitInForm" method="POST" action="sit_in_action.php">
                            <label>ID Number:</label>
                            <input type="text" name="idno" value="<?php echo $row['idno']; ?>" readonly>
                            <label>Student Name:</label>
                            <input type="text" value="<?php echo $fullName; ?>" readonly>
                            <label>Purpose:</label>
                            <select name="purpose" required>
                                <option value="c programming">C Programming</option>
                                <option value="c# programming">C# Programming</option>
                                <option value="java programming">Java Programming</option>
                                <option value="php programming">PHP Programming</option>
                                <option value="asp.net programming">ASP.Net Programming</option>
                            </select>
                            <label>Lab:</label>
                            <select name="lab" required>
                                <option value="524">524</option>
                                <option value="526">526</option>
                                <option value="528">528</option>
                                <option value="530">530</option>
                                <option value="542">542</option>
                                <option value="Mac Laboratory">Mac Laboratory</option>
                            </select>
                            <label>Remaining Sessions:</label>
                            <input type="text" value="<?php echo $row['sessions']; ?>" readonly>
                            <div class="button-group">
                                <button type="button" style="border: 1px solid #112D4E; color: #112D4E;" onclick="closeStudentInfoModal()">Close</button>
                                <button type="submit" style="background: #112D4E; color: #fff;">Sit In</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
            } else {
                echo "<script>alert('Student ID not found!'); openSearchModal();</script>";
            }
        }
        ?>

        <div id="searchModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeSearchModal()">&times;</span>
                <h2>🔍 Search Student</h2>
                <form id="searchForm" method="POST" action="">
                    <label for="searchId">ID Number:</label>
                    <input type="text" id="searchId" name="searchId" required>
                    <button type="submit" style="background: #112D4E; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Search</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('sitInChart').getContext('2d');
        const sitInChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($purpose)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($purpose)); ?>,
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#8BC34A', '#009688'],
                }]
            }
        });

        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'block';
        }
        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }
        function openStudentInfoModal() {
            document.getElementById('studentInfoModal').style.display = 'block';
        }
        function closeStudentInfoModal() {
            document.getElementById('studentInfoModal').style.display = 'none';
        }

    </script>
</body>
</html>
