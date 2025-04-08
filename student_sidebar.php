<?php
function renderSidebar() {
    echo '
    <div class="sidebar">
        <h2>Student</h2>
        <a href="studenthome.php"><i class="fas fa-home"></i> <span>HOME</span></a>
        <a href="resource_student.php"><i class="fa-solid fa-file"></i> <span>FILES</span></a>
        <a href="editprofile.php"><i class="fas fa-user-edit"></i> <span>EDIT PROFILE</span></a>
        <a href="history.php"><i class="fas fa-history"></i> <span>HISTORY</span></a>
        <a href="#"><i class="fas fa-calendar-alt"></i> <span>RESERVATION</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>LOGOUT</span></a>
    </div>';
}
?>
