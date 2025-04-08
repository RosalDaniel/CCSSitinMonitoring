<?php
session_start();
include 'auth_check.php';
include 'sidebar.php'; // Assuming you have a sidebar for admin
include 'db_connect.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $uploadedBy = $_SESSION['user'];

    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['resource_file']['tmp_name'];
        $fileName = basename($_FILES['resource_file']['name']);
        $fileType = $_FILES['resource_file']['type'];
        $uploadDir = "uploads/resources/";

        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            $stmt = $conn->prepare("INSERT INTO resource_files (title, description, filename, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $description, $fileName, $fileType, $uploadedBy);
            if ($stmt->execute()) {
                $message = "File uploaded successfully!";
            } else {
                $error = "Failed to save file info.";
            }
            $stmt->close();
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Please choose a valid file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resource Files</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .content { flex-grow: 1; padding: 20px; }
        .header { background: #112D4E; color: #fff; padding: 20px 30px; text-align: center; font-size: 24px; margin-bottom: 20px; border-radius: 5px; }
        form { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; margin-bottom: 50px;}
        .form-group { width: 100%; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px;}
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;}
        .submit-btn { background-color: #112D4E; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .submit-btn:hover { background-color: #3F72AF; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="content">
    <div class="header">UPLOAD RESOURCE FILES</div>

    <?php if ($message): ?>
        <div class="alert success"><?php echo $message; ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return disableSubmit()">
        <div class="form-group">
            <label for="title">File Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" maxlength="300" required></textarea>
        </div>

        <div class="form-group">
            <label for="resource_file">Select File (PDF, DOCX, etc.)</label>
            <input type="file" name="resource_file" id="resource_file" accept=".pdf,.doc,.docx,.txt" required>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">Upload File</button>
    </form>
    <br>
    <hr>
    <h3>Uploaded Resources</h3>
            <ul>
                <?php
                    $result = $conn->query("SELECT * FROM resource_files ORDER BY uploaded_at DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo " " . htmlspecialchars($row['title']) . " ";
                        echo "<li><a href='uploads/resources/" . htmlspecialchars($row['filename']) . "' target='_blank'>" . htmlspecialchars($row['filename']) . "</a> - " . htmlspecialchars($row['uploaded_by']) . " on " . date('F j, Y', strtotime($row['uploaded_at'])) . "</li>";
                    }
                ?>
            </ul>
</div>

<script>
    function disableSubmit() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerText = "Uploading...";
        return true;
    }
</script>
</body>
</html>
