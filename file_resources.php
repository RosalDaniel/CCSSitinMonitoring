<?php
session_start();
include 'auth_check.php';
include 'sidebar.php';
include 'db_connect.php';

$message = "";
$error = "";

// Handle File Deletion (AJAX-safe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file_id'])) {
    $fileId = intval($_POST['delete_file_id']);
    $fileQuery = $conn->query("SELECT filename FROM resource_files WHERE id = $fileId");
    if ($fileQuery && $fileQuery->num_rows > 0) {
        $file = $fileQuery->fetch_assoc();
        $filePath = "uploads/resources/" . $file['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $conn->query("DELETE FROM resource_files WHERE id = $fileId");
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// Handle File Upload (AJAX-safe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resource_file'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $uploadedBy = $_SESSION['user'];

    if ($_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['resource_file']['tmp_name'];
        $originalName = basename($_FILES['resource_file']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $uniqueName = $safeName . '_' . time() . '.' . $extension;

        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        $fileType = mime_content_type($fileTmp);

        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type.";
            exit;
        }

        $uploadDir = "uploads/resources/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $uniqueName;

        if (move_uploaded_file($fileTmp, $destination)) {
            $stmt = $conn->prepare("INSERT INTO resource_files (title, description, filename, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $description, $uniqueName, $fileType, $uploadedBy);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Database insert failed.";
            }
            $stmt->close();
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "No file selected.";
    }
    exit;
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
        .header {
            background: #112D4E; color: #fff; padding: 20px 30px;
            font-size: 24px; margin-bottom: 10px; border-radius: 5px;
            text-align: center;
        }
        .upload-btn-wrapper {
            display: flex; justify-content: flex-end; margin-bottom: 20px;
        }
        .upload-btn {
            background-color: #112D4E; color: white; padding: 10px 16px;
            border: none; border-radius: 5px; cursor: pointer; font-size: 14px;
        }
        .upload-btn:hover { background-color: #5b8bd4; }

        .modal {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);
            z-index: 1000; justify-content: center; align-items: center;
        }
        .modal-content {
            background: white; padding: 30px; border-radius: 8px;
            max-width: 600px; width: 90%; position: relative;
        }
        .modal-close {
            position: absolute; top: 10px; right: 15px;
            background: none; border: none; font-size: 24px;
            color: #aaa; cursor: pointer;
        }

        .form-group { width: 100%; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px;}
        input, select, textarea {
            width: 100%; padding: 10px; border: 1px solid #ccc;
            border-radius: 5px; box-sizing: border-box;
        }
        .submit-btn {
            background-color: #112D4E; color: white; padding: 10px 20px;
            border: none; border-radius: 5px; font-size: 16px; cursor: pointer;
        }
        .submit-btn:hover { background-color: #3F72AF; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

        .resource-list ul { list-style-type: none; padding: 0; }
        .resource-list li {
            margin-bottom: 10px; background: #fff; padding: 10px;
            border-radius: 5px; display: flex; justify-content: space-between; align-items: center;
        }
        .resource-list .actions {
            display: flex; gap: 10px; background: transparent; box-shadow: none;
        }
        .resource-list .actions form {
            background: none; box-shadow: none; padding: 20px; margin: 0;
        }
        .delete-btn {
            background-color: #D7263D; color: white; border: none;
            padding: 6px 12px; border-radius: 5px; cursor: pointer;
        }
        .delete-btn:hover { background-color: #a61b2b; }
    </style>
</head>
<body>
<?php renderSidebar(); ?>
<div class="content">
    <div class="header">UPLOAD RESOURCE FILES</div>
    <div class="upload-btn-wrapper">
        <button class="upload-btn" onclick="openModal()">
            <i class="fa fa-upload"></i> Upload New File
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert success"><?php echo $message; ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2 style="text-align:center; margin-bottom: 20px;">Upload Resource File</h2>
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
        </div>
    </div>

    <hr>
    <h3>Uploaded Resources</h3>
    <input type="text" id="searchInput" placeholder="Search resources..." style="width: 100%; max-width: 400px; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">

    <div class="resource-list" id="resourceList">
        <ul>
            <?php
            $result = $conn->query("SELECT * FROM resource_files ORDER BY uploaded_at DESC");
            while ($row = $result->fetch_assoc()):
            ?>
                <li>
                    <div>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <a href='uploads/resources/<?php echo htmlspecialchars($row['filename']); ?>' target='_blank'><?php echo htmlspecialchars($row['filename']); ?></a>
                        <small> - <?php echo htmlspecialchars($row['uploaded_by']); ?> on <?php echo date('F j, Y', strtotime($row['uploaded_at'])); ?></small>
                    </div>
                    <div class="actions">
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this file?')">
                            <input type="hidden" name="delete_file_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-btn"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<!-- your <head> and <body> as-is until script -->

<script>
    function openModal() {
        document.getElementById('uploadModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('uploadModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('uploadModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    function fetchResourceList(search = '') {
        const listContainer = document.getElementById('resourceList');
        fetch(`fetch_resources.php?search=${encodeURIComponent(search)}`)
            .then(response => response.text())
            .then(html => {
                listContainer.innerHTML = html;
                attachDeleteHandlers();
            });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        fetchResourceList(this.value);
    });

    function attachDeleteHandlers() {
        const deleteForms = document.querySelectorAll('.resource-list form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this file?')) return;

                const formData = new FormData(form);
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        fetchResourceList();
                    } else {
                        alert('Failed to delete file.');
                    }
                });
            });
        });
    }

    function handleUploadSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(form);
        submitBtn.disabled = true;
        submitBtn.innerText = "Uploading...";

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === 'success') {
                closeModal();
                fetchResourceList(); // Refresh list
                form.reset();
            } else {
                alert(result);
            }
            submitBtn.disabled = false;
            submitBtn.innerText = "Upload File";
        });
    }

    document.querySelector('#uploadModal form').addEventListener('submit', handleUploadSubmit);

    window.addEventListener('DOMContentLoaded', () => fetchResourceList());
</script>

</body>
</html>
