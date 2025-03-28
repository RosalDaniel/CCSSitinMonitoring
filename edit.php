<?php
    session_start();
    include 'auth_check.php';
    include 'db_connect.php'; 

    // Retrieve student information
    $username = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT idno, lastname, firstname, middlename, course, year, email, address FROM student WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 900px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            gap: 20px;
        }

        .form-group div {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 20px;
        }

        button {
            padding: 10px 50px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-btn {
            background: #F9F7F7;
            border: 1px solid #DBE2EF;
        }

        .save-btn {
            background: #112D4E;
            color: #fff;
        }
        .modal {
            display: none; /* Hide the modal initially */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content button {
            background: #5a3772;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
        }

        .modal-content button:hover {
            background: rgb(165, 99, 209);
        }
        .back-btn {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>

    <div class="container">
    <a href="javascript:history.back()" class="back-btn">
        <button type="button">Back</button>
    </a>
        <h2>Edit Profile</h2>
        <form id="editProfileForm" action="update_profile.php" method="POST">
            <label>ID No</label>
            <input type="text" name="idno" value="<?= htmlspecialchars($student['idno']); ?>" disabled>
            <input type="hidden" name="idno" value="<?= htmlspecialchars($student['idno']); ?>">
            <div class="form-group">
                <div>
                    <label>First Name</label>
                    <input type="text" name="firstname" value="<?= htmlspecialchars($student['firstname']); ?>" required>
                </div>
                <div>
                    <label>Middle Name</label>
                    <input type="text" name="middlename" value="<?= htmlspecialchars($student['middlename']); ?>">
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="lastname" value="<?= htmlspecialchars($student['lastname']); ?>" required>
                </div>
            </div>

            <label>Course</label>
            <input type="text" name="course" value="<?= htmlspecialchars($student['course']); ?>" required>

            <label>Year</label>
            <input type="text" name="year" value="<?= htmlspecialchars($student['year']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($student['address']); ?>" required>


            <div class="actions">
                <a href="studenthome.php">
                    <button type="button" class="cancel-btn">Cancel</button>
                </a>
                <button type="button" onclick="updateProfile()" class="save-btn">Save</button>
            </div>
        </form>
        <div id="successModal" class="modal">
            <div class="modal-content">
                <p id="modalMessage">Profile updated successfully!</p>
                <button onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>
    <script>
        function updateProfile() {
            let form = document.getElementById("editProfileForm");
            let formData = new FormData(form);

            fetch("update_profile.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("modalMessage").innerText = data.message;
                    document.getElementById("successModal").style.display = "flex";
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        function closeModal() {
            document.getElementById("successModal").style.display = "none";
            window.location.href = "edit.php"; // Redirect to home page after closing modal
        }

    </script>
</body>
</html>

