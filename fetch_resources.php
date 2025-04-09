<?php
include 'db_connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM resource_files";

if ($search !== '') {
    $query .= " WHERE title LIKE '%$search%' OR description LIKE '%$search%'";
}

$query .= " ORDER BY uploaded_at DESC";
$result = $conn->query($query);
?>

<ul>
<?php while ($row = $result->fetch_assoc()): ?>
    <li>
        <div>
            <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
            <a href='uploads/resources/<?php echo htmlspecialchars($row['filename']); ?>' target='_blank'>
                <?php echo htmlspecialchars($row['filename']); ?>
            </a>
            <small> - <?php echo htmlspecialchars($row['uploaded_by']); ?> on <?php echo date('F j, Y', strtotime($row['uploaded_at'])); ?></small>
        </div>
        <div class="actions">
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this file?')">
                <input type="hidden" name="delete_file_id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="delete-btn"><i class="fa fa-trash"></i> Delete</button>
            </form>
        </div>
    </li>
<?php endwhile; ?>
</ul>
