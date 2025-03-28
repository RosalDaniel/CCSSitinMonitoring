<?php
session_start();
include 'auth_check.php';
include 'db_connect.php'; 

// Fetch announcements
$announcementResult = $conn->query("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC");

// Retrieve student information
$username = $_SESSION['user'];
$stmt = $conn->prepare("SELECT idno, firstname, lastname, middlename, course, year, email, sessions, address FROM student WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();
$conn->close();

?>

<!DOCTYPE html><html class="menu">
<html>

<head>

<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content=="IE=edge"/>
<meta name="google" value="notranslate"/>
<title>Home</title>

<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

</head>
<style>
    body
{
  margin:0px;
  padding:0px;
	font-family: "Open Sans", arial;
	background:rgb(216, 216, 216);
	color:#fff;
	font-weight:300;

}


@import url(//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css);
@import url(https://fonts.googleapis.com/css?family=Titillium+Web:300);

.logo{
  
}

.settings {
  
    height:73px; 
    float:left;
    background:url( https://s3.postimg.org/bqfooag4z/startific.jpg);
    background-repeat:no-repeat;
    width:250px;
    margin:0px;
    text-align: center;
    font-size:20px;
    font-family: 'Strait', sans-serif;
}





/* ScrolBar  */
.scrollbar
{
    height: 90%;
    width: 100%;
    overflow-y: hidden;
    overflow-x: hidden;
}

.scrollbar:hover
{

    height: 90%;
    width: 100%;
    overflow-y: scroll;
    overflow-x: hidden;
}

/* Scrollbar Style */ 



#style-1::-webkit-scrollbar-track
{
    border-radius: 2px;
    }

#style-1::-webkit-scrollbar
{
    width: 5px;
    background-color: #F7F7F7;
}

#style-1::-webkit-scrollbar-thumb
{
    border-radius: 10px;
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #BFBFBF;
}
/* Scrollbar End */ 




.fa-lg {
font-size: 1em;
  
}
.fa {
    position: relative;
    display: table-cell;
    width: 55px;
    height: 36px;
    text-align: center;
    top:12px; 
    font-size:20px;

}



.main-menu:hover, nav.main-menu.expanded {
    width:250px;
    overflow:hidden;
    opacity:1;

}

.main-menu {
    background:#F7F7F7;
    position:absolute;
    top:-5%;
    bottom:0;
    height:100%;
    left:0;
    width:55px;
    overflow:hidden;
    -webkit-transition:width .2s linear;
    transition:width .2s linear;
    -webkit-transform:translateZ(0) scale(1,1);
    box-shadow: 1px 0 15px rgba(0, 0, 0, 0.07);
    opacity:1;
}

.main-menu>ul {
    margin:7px 0;
}

.main-menu li {
    position:relative;
    display:block;
    width:250px;
}

.main-menu li>a {
    position:relative;
    width:255px;
    display:table;
    border-collapse:collapse;
    border-spacing:0;
    color:#8a8a8a;
    font-size: 13px;
    text-decoration:none;
    -webkit-transform:translateZ(0) scale(1,1);
    -webkit-transition:all .14s linear;
    transition:all .14s linear;
    font-family: 'Strait', sans-serif;
    border-top:1px solid #f2f2f2;
    text-shadow: 1px 1px 1px  #fff;  
}



.main-menu .nav-icon {
    position:relative;
    display:table-cell;
    width:55px;
    height:36px;
    text-align:center;
    vertical-align:middle;
    font-size:18px;
}

.main-menu .nav-text  {   
    position:relative;
    display:table-cell;
    vertical-align:middle;
    width:190px;
    font-family: 'Titillium Web', sans-serif;
}


.main-menu>ul.logout {
    position:absolute;
    left:0;
    bottom:0;
}

.no-touch .scrollable.hover {
    overflow-y:hidden;
}

.no-touch .scrollable.hover:hover {
    overflow-y:auto;
    overflow:visible;
}


/* Logo Hover Property */


.settings:hover, settings:focus {   
  -webkit-transition: all 0.2s ease-in-out, width 0, height 0, top 0, left 0;
-moz-transition: all 0.2s ease-in-out, width 0, height 0, top 0, left 0;
-o-transition: all 0.2s ease-in-out, width 0, height 0, top 0, left 0;
transition: all 0.2s ease-in-out, width 0, height 0, top 0, left 0; 
}

.settings:active, settings:focus {   
  -webkit-transition: all 0.1s ease-in-out, width 0, height 0, top 0, left 0;
-moz-transition: all 0.1s ease-in-out, width 0, height 0, top 0, left 0;
-o-transition: all 0.1s ease-in-out, width 0, height 0, top 0, left 0;
transition: all 0.1s ease-in-out, width 0, height 0, top 0, left 0; 
}


a:hover,a:focus {
text-decoration:none;
border-left:0px solid #F7F7F7;



}

nav {
-webkit-user-select:none;
-moz-user-select:none;
-ms-user-select:none;
-o-user-select:none;
user-select:none;
  
}

nav ul,nav li {
outline:0;
margin:0;
padding:0;
text-transform: uppercase;
}




/* Darker element side menu Start*/


.darkerli
{
background-color:#ededed;
text-transform:capitalize;  
}

.darkerlishadow
{
background-color:#ededed;
text-transform:capitalize;  
-webkit-box-shadow: inset 0px 5px 5px -4px rgba(50, 50, 50, 0.55);
-moz-box-shadow:    inset 0px 5px 5px -4px rgba(50, 50, 50, 0.55);
box-shadow:         inset 0px 5px 5px -4px rgba(50, 50, 50, 0.55);
}


.darkerlishadowdown
{
background-color:#ededed;
text-transform:capitalize;  
-webkit-box-shadow: inset 0px -4px 5px -4px rgba(50, 50, 50, 0.55);
-moz-box-shadow:    inset 0px -4px 5px -4px rgba(50, 50, 50, 0.55);
box-shadow:         inset 0px -4px 5px -4px rgba(50, 50, 50, 0.55);
}

/* Darker element side menu End*/




.main-menu li:hover>a,nav.main-menu li.active>a,.dropdown-menu>li>a:hover,.dropdown-menu>li>a:focus,.dropdown-menu>.active>a,.dropdown-menu>.active>a:hover,.dropdown-menu>.active>a:focus,.no-touch .dashboard-page nav.dashboard-menu ul li:hover a,.dashboard-page nav.dashboard-menu ul li.active a {
color:#fff;
background-color:#00bbbb;
text-shadow: 0px 0px 0px; 
}
.area {
float: left;
background: #e2e2e2;
width: 100%;
height: 100%;
}
@font-face {
  font-family: 'Titillium Web';
  font-style: normal;
  font-weight: 300;
  src: local('Titillium WebLight'), local('TitilliumWeb-Light'), url(http://themes.googleusercontent.com/static/fonts/titilliumweb/v2/anMUvcNT0H1YN4FII8wpr24bNCNEoFTpS2BTjF6FB5E.woff) format('woff');
}


.container {
  display: grid;
  grid-template-columns: 1fr 2fr 2fr;
  padding: 10px;
  justify-content: center;
  gap: 20px;
  margin: 0 100px;
}
.container > div {
  background-color: #f1f1f1;
  border: 1px solid light-gray;
  border-radius: 10px;
  padding: 10px;
  font-size: 30px;
  text-align: center;

}

.student-info-card,  .box-announcement, .box-rules{
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: left;
    font-size: 18px;
    color: #333;
    height: 600px;
}

.student-info-card h2, .box-announcement h2, .box-rules h2 {
    color: #5a3772;
    border-bottom: 2px solid #5a3772;
    padding-bottom: 10px;
    font-size: 22px;
}

.info p {
    margin: 10px 20px;
    font-size: 16px;
    text-align: left;
}

.image-container {
    text-align: center;
    margin: 50px 0 50px;
}

.student-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%; /* Makes the image circular */
    object-fit: cover;
    border: 3px solid #5a3772;
}

.box-rules p {
    font-size: 20px;
}

.rules-list {
    text-align: left;
    margin: 0 50px;
}

.rules-list p {
    font-size: 15px;
}

.box-rules {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 3px 3px 15px rgba(0, 0, 0, 0.1);
            width: 450px;
            height: 600px; /* Fixed height */
            overflow: hidden; /* Prevents overflow */
            margin-bottom: 50px;        }

        h2 {
            text-align: center;
            color: #5a3772;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .rules-list {
            max-height: 300px; /* Scrollable area */
            overflow-y: auto;
            padding-right: 10px;

        }

        .rules-list p {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        /* Custom scrollbar styling */
        .rules-list::-webkit-scrollbar {
            width: 8px;
        }

        .rules-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        .rules-list::-webkit-scrollbar-thumb {
            background: #5a3772;
            border-radius: 8px;
        }

</style>

<body>
<nav class="main-menu">
  
 <div>
    <a class="logo" href="#">
    </a> 
  </div> 
<div class="settings"></div>
<div class="scrollbar" id="style-1">
      
<ul>
  
<li>                                   
<a href="#">
<i class="fa fa-home fa-lg"></i>
<span class="nav-text">Home</span>
</a>
</li>   
   
<li>                                 
<a href="editprofile.php">
<i class="fa fa-user fa-lg"></i>
<span class="nav-text">Edit Profile</span>
</a>
</li>   

    
<li>                                 
<a href="#">
<i class="fa fa-envelope-o fa-lg"></i>
<span class="nav-text">Notification</span>
</a>
</li>   
  
                            

  
  
</li>
<li class="darkerlishadow">
<a href="reservation.php">
<i class="fa fa-clock-o fa-lg"></i>
<span class="nav-text">Reservation</span>
</a>
</li>
  
<li class="darkerli">
<a href="#">
<i class="fa fa-calendar-o fa-lg"></i>
<span class="nav-text">History</span>
</a>
</li>
  
  
<li class="darkerli">
<a href="logout.php">
<i class="fa fa-arrow-left fa-lg"></i>
 <span class="nav-text">Logout</span>
</a>
</li>
 
</ul>

</nav>
        
<div class="container">
    <div class="student-info-card">
        <h2>Student Information</h2>
        <div class="image-container">
            <img src="./image/ccslogo.png" alt="" class="student-photo">
        </div>
        <div class="info">
            <p><strong>ID No:</strong> <?= htmlspecialchars($student['idno']); ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']); ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($student['course']); ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($student['year']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address']); ?></p>
            <p><strong>Session:</strong> <?= htmlspecialchars($student['sessions']); ?></p>
        </div>
    </div>

    <div class="box-announcement">
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

    <div class="box-rules">
        <h2>Rules and Regulations</h2>
        <p><strong>University of Cebu</strong></p>
        <p><strong>COLLEGE OF INFORMATION & COMPUTER STUDIES</strong></p>
        <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
        <div class="rules-list">
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

    </div>

			
  
  
</body>
</html>
