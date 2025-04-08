<?php
function renderSidebar() {
    echo '
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_home.php" title="Home"><i class="fas fa-home"></i> <span>HOME</span></a>
        <a href="students.php" title="View Students"><i class="fa-solid fa-user"></i><span>STUDENTS</span></a>
        <a href="sit_in.php" title="View Current Sit-in"><i class="fa-solid fa-pen-to-square"></i><span>SIT-IN</span></a>
        <a href="sit_in_reports.php" title="View Sit-in Reports"><i class="fa-solid fa-clipboard-user"></i><span>REPORTS</span</a>
        <a href="records.php" title="View Sit-in Records"><i class="fa-solid fa-file-lines"></i><span>RECORDS</span></a>
        <a href="feedback_report.php" title="View Feedback Report"><i class="fa-solid fa-comments"></i><span>FEEDBACK</span></a>
        <a href="file_resources.php" title="Upload File Resources"><i class="fa-solid fa-file-arrow-up"></i><span>FILES</span></a>
        <a href="#" title="View Reservation"><i class="fa-solid fa-calendar-days"></i><span>RESERVATION</span></a>
        <a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i><span>LOGOUT</span></a>
    </div>';
}
?>
