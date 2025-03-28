<?php
include 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $conn->real_escape_string($_POST['idno']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $lab = $conn->real_escape_string($_POST['lab']);

    // Check if the student already has an active session
    $checkSql = "SELECT * FROM sit_in_records WHERE idno = '$idno' AND logout_time IS NULL";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        // Student has an active session
        echo "<script>alert('You cannot submit another sit-in form until you log out.'); window.location.href='admin_home.php';</script>";
    } else {

        // Insert sit-in record
        $insertSql = "INSERT INTO sit_in_records (idno, purpose, lab, date_time) VALUES ('$idno', '$purpose', '$lab', NOW())";

        if ($conn->query($insertSql) === TRUE) {
            echo "<script>alert('Sit-in recorded successfully!'); window.location.href='sit_in.php';</script>";
        } else {
            echo "Error: " . $insertSql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>