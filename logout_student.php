<?php
    include 'db_connect.php'; 

    if (isset($_GET['sit_id'])) {
        $sit_id = $_GET['sit_id'];

        // Start transaction for safety
        $conn->begin_transaction();

        try {
            // Fetch student ID
            $fetchIdQuery = "SELECT idno FROM sit_in_records WHERE id = ? AND logout_time IS NULL";
            $fetchStmt = $conn->prepare($fetchIdQuery);
            $fetchStmt->bind_param("i", $sit_id);
            $fetchStmt->execute();
            $result = $fetchStmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idno = $row['idno'];

                // Update logout_time
                $query = "UPDATE sit_in_records SET logout_time = CURRENT_TIMESTAMP WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $sit_id);
                $stmt->execute();

                // Decrement session count
                $updateSql = "UPDATE student SET sessions = GREATEST(sessions - 1, 0) WHERE idno = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("s", $idno);
                $updateStmt->execute();

                $conn->commit();
                echo "<script>alert('Student logged out successfully!'); window.location.href = 'sit_in.php';</script>";
            } else {
                echo "<script>alert('No active session found or already logged out.'); window.location.href = 'sit_in.php';</script>";
            }

            $fetchStmt->close();
            $stmt->close();
            $updateStmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Failed to log out student. Error: " . $e->getMessage() . "'); window.location.href = 'sit_in.php';</script>";
        }
    }

    $conn->close();
?>
