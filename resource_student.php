<?php
session_start();
include 'auth_check.php';
include 'student_sidebar.php';
include 'db_connect.php';

$files = $conn->query("SELECT * FROM resource_files ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resource Files</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
        }
        .sidebar {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
            flex-shrink: 0;
            background-color: #112D4E;
            color: white;
            min-height: 100vh;
            height: auto;
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
        .file-list {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .file-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .file-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .file-description {
            color: #555;
            margin-bottom: 10px;
        }
        .file-meta {
            font-size: 12px;
            color: #888;
        }
        .file-link {
            display: inline-block;
            margin-top: 5px;
            color: #3F72AF;
            text-decoration: none;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .file-missing {
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="content">
    <div class="header">RESOURCE MATERIALS</div>

    <div class="file-list">
        <?php if ($files->num_rows > 0): ?>
            <?php while ($row = $files->fetch_assoc()): ?>
                <div class="file-item">
                    <div class="file-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    <div class="file-description"><?php echo htmlspecialchars($row['description']); ?></div>
                    <div class="file-meta">
                        Type: <?php echo htmlspecialchars($row['file_type']); ?> |
                        Uploaded by: <?php echo htmlspecialchars($row['uploaded_by']); ?> |
                        Date: <?php echo date('F j, Y h:i A', strtotime($row['uploaded_at'])); ?>
                    </div>

                    <?php
                        $filename = $row['filename'];
                        $filepath = 'uploads/resources/' . $filename;
                        if (file_exists($filepath)):
                    ?>
                        <a class="file-link" href="<?php echo $filepath; ?>" target="_blank">
                            <i class="fa fa-download"></i> Download
                        </a>
                    <?php else: ?>
                        <div class="file-missing">File not found on server.</div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No resource files uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
