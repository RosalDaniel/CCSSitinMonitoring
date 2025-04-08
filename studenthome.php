<?php
    session_start();
    include 'auth_check.php';
    include 'db_connect.php'; 

    // Fetch announcements
    $announcementResult = $conn->query("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC");

    // Retrieve student information
    $username = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT idno, firstname, lastname, middlename, course, year, email, sessions, address, profile_image FROM student WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            height: 100%;
        }
        .sidebar {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
            flex-shrink: 0;
            background-color: #112D4E;
            color: white;
            min-height: 100vh;
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

        .main-content {
            width: calc(100% - 200px); /* subtract sidebar width */
            display: flex;
            flex-direction: column;
        }

        .header {
            width: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 15px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-sizing: border-box;
        }

        .header .notification {
            font-size: 1.4rem;
            color: #112D4E;
            cursor: pointer;
            position: relative;
        }

        .header .notification:hover {
            color: #3F72AF;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            margin-top: 10px;
            display: grid;
            grid-template-columns: .7fr 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: #ffffff;
            height: 700px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .rules-card, .announcement-card {
            height: 730px; 
            overflow-y: auto;
            padding: 20px; 
            box-sizing: border-box; 
        }

        .image-container {
            text-align: center;
            margin: 50px 0 50px;
        }

        .student-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #DBE2EF;
        }
    </style>
</head>
<body>

    <?php include 'student_sidebar.php'; renderSidebar(); ?>

    <div class="main-content">
        <div class="header">
            <div class="notification">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

        <div class="content">
            <div class="card student-card">
                <h3>Student Information</h3>
                <hr>
                <div class="image-container">
                    <?php if (!empty($student['profile_image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($student['profile_image']); ?>" alt="Profile Image" class="student-photo">
                    <?php else: ?>
                        <img src="./image/default-image.png" alt="Default Profile" class="student-photo">
                    <?php endif; ?>
                </div>

                <p><strong>ID No:</strong> <?= htmlspecialchars($student['idno']); ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']); ?></p>
                <p><strong>Course:</strong> <?= htmlspecialchars($student['course']); ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($student['year']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student['email']); ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($student['address']); ?></p>
                <p><strong>Session:</strong> <?= htmlspecialchars($student['sessions']); ?></p>
            </div>
            
            <div class="card rules-card">
                <h3>Rules and Regulations</h3>
                <hr>
                <h4>University of Cebu</h4>
                <h5>COLLEGE OF INFORMATION & COMPUTER STUDIES</h5>
                <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
                <p>1. Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal equipment must be switched off.</p>
                <p>2. Games are not allowed inside the lab. This includes computer-related games, card games, and other games that may disturb the operation of the lab.</p>
                <p>3. Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing software are strictly prohibited.</p>
                <p>4. Accessing websites unrelated to the course (especially pornographic and illicit sites) is strictly prohibited.</p>
                <p>5. Deleting computer files and changing computer settings is a major offense.</p>
                <p>6. Observe computer time usage carefully. A 15-minute allowance is given for each use; otherwise, the unit will be given to those who wish to "sit-in".</p>
                <p>7. Do not enter the lab unless the instructor is present.</p>
                <p>8. All bags, knapsacks, and similar items must be placed at the designated counter.</p>
                <p>9. Follow the instructor's seating arrangement.</p>
                <p>10. At the end of class, close all software programs and return chairs to their proper places.</p>
                <p>11. Chewing gum, eating, drinking, smoking, and vandalism are prohibited in the lab.</p>
                <p>12. Anyone causing a disturbance will be asked to leave the lab.</p>
                <p>13. Public displays of physical intimacy are not tolerated.</p>
                <p>14. Hostile or threatening behavior, such as yelling, swearing, or ignoring requests from lab personnel, will not be tolerated.</p>
                <p>15. The lab personnel may call the Civil Security Office (CSU) for assistance in serious offenses.</p>
                <p>16. Any technical problems should be reported to the lab supervisor, student assistant, or instructor.</p>

                <p><strong>DISCIPLINARY ACTION</strong></p>
                <p><strong>First Offense:</strong> The Head, Dean, or OIC may recommend a suspension from classes.</p>
                <p><strong>Second and Subsequent Offenses:</strong> A recommendation for a heavier sanction will be endorsed to the Guidance Center.</p>
            </div>

            <div class="card announcement-card">
                <h3>Announcement</h3>
                <hr>
                <?php
                    if ($announcementResult->num_rows > 0) {
                        while ($row = $announcementResult->fetch_assoc()) {
                            echo "<div class='announcement'>";
                            echo "<strong>" . htmlspecialchars($row['title']) . " | " . date('F j, Y', strtotime($row['created_at'])) . "</strong>";
                            echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                            echo "<hr>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No announcements yet.</p>";
                    }
                ?>
            </div>
        </div>
    </div>

</body>
</html>
