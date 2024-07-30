<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for form submission
$title = $category = $description = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $title = htmlspecialchars(trim($_POST['title']));
    $category = htmlspecialchars(trim($_POST['category']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Validate input
    if (empty($title) || empty($category) || empty($description)) {
        $error = "All fields are required.";
    } else {
        // Insert complaint into database
        $student_id = $_SESSION['user_id'];
        $status = "Pending"; // Default status for new complaints

        $sql = "INSERT INTO complaints (student_id, title, category, description, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $student_id, $title, $category, $description, $status);

        if ($stmt->execute()) {
            // Redirect to dashboard or view complaints page
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error occurred while filing complaint. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File a Complaint</title>
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 500px;
        }
        h1 {
            text-align: center;
            color: #ff8c00;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ffa500;
        }
        input[type=text], select, textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #2c2c2c;
            color: #ffffff;
        }
        textarea {
            height: 120px;
        }
        .error {
            color: #ff4444;
            font-size: 14px;
            margin-top: 20px;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            padding: 12px 20px;
            background-color: #ff8c00;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #e67300;
        }
    </style>
 
</head>
<body>
    <div class="container">
        <h1>File a Complaint</h1>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="title">Complaint Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="" selected disabled>Select Category</option>
                    <option value="Water and Parking">Water and Parking</option>
                    <option value="Infrastructure">Infrastructure</option>
                    <option value="Staff">Staff</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="button-container">
                <button type="submit" class="button">Submit Complaint</button>
            </div>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
