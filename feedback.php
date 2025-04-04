<?php
session_start();
include 'auth_check.php';
include 'db_connect.php'; 

$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : '';
if (!$record_id) {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Give Feedback</title>
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #eef2ff;
            --background-color: #f9fafb;
            --text-color: #1f2937;
            --border-radius: 0.5rem;
            --transition-speed: 0.3s;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        main {
            background: white;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            text-align: center;
            color: var(--primary-color);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
            padding: 1rem;
            font-size: 1rem;
            border: 1px solid #d1d5db;
            border-radius: var(--border-radius);
            transition: border var(--transition-speed);
        }

        textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px var(--secondary-color);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition-speed);
        }

        button:hover {
            background-color: #4338ca;
        }

        @media (max-width: 480px) {
            main {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            button {
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <main>
        <h2>Give Feedback</h2>
        <form action="feedback_action.php" method="post">
            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record_id); ?>">
            <label for="feedback_text" class="sr-only">Your Feedback</label>
            <textarea name="feedback_text" id="feedback_text" placeholder="Write your feedback here..." required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </main>
</body>
</html>
