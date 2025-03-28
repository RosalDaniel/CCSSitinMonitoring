<?php
session_start();

include 'db_connect.php'; 

// Delete student
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM student WHERE idno = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Student deleted successfully!');
                window.location.href = 'students.php';
              </script>";
    } else {
        echo "Error deleting student: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>
