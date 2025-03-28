<?php
session_start();
include 'db_connect.php'; 

$idno = $_POST['idno'];
$lastname = $_POST['lastname'];
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$course = $_POST['course'];
$year = $_POST['year'];
$email = $_POST['email'];
$address = $_POST['address'];

$profile_image = null;
$upload_dir = "uploads/";
// Fetch the current profile image from the database
$stmt = $conn->prepare("SELECT profile_image FROM student WHERE idno = ?");
$stmt->bind_param("i", $idno);
$stmt->execute();
$stmt->bind_result($old_image);
$stmt->fetch();
$stmt->close();

// Delete old image if it exists (and is not default)
if (!empty($old_image) && file_exists("uploads/" . $old_image)) {
    unlink("uploads/" . $old_image);
}

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    $img_name = basename($_FILES['profile_image']['name']);
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($img_ext, $allowed_ext)) {
        $new_img_name = "profile_" . $idno . "_" . time() . "." . $img_ext;
        $upload_path = "uploads/" . $new_img_name;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
            $profile_image = $new_img_name;
        }
    }
}

if ($profile_image) {
    $stmt = $conn->prepare("UPDATE student SET lastname=?, firstname=?, middlename=?, course=?, year=?, email=?, address=?, profile_image=? WHERE idno=?");
    $stmt->bind_param("ssssssssi", $lastname, $firstname, $middlename, $course, $year, $email, $address, $profile_image, $idno);
} else {
    $stmt = $conn->prepare("UPDATE student SET lastname=?, firstname=?, middlename=?, course=?, year=?, email=?, address=? WHERE idno=?");
    $stmt->bind_param("sssssssi", $lastname, $firstname, $middlename, $course, $year, $email, $address, $idno);
}

if ($stmt->execute()) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='editprofile.php';</script>";
} else {
    echo json_encode(["success" => false, "message" => "Error updating profile: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
