<?php
session_start();
include('config.php');

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch complaint details from database
if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    $sql = "SELECT * FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $complaint = $result->fetch_assoc();
    } else {
        echo "Complaint not found.";
        exit();
    }
} else {
    echo "Complaint ID not specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .complaint-info {
            margin-top: 20px;
        }
        .complaint-info p {
            margin: 10px 0;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .back-link a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Complaint Details</h1>

        <div class="complaint-info">
            <p><strong>ID:</strong> <?php echo $complaint['id']; ?></p>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint['category']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
            <p><strong>Student ID:</strong> <?php echo $complaint['student_id']; ?></p>
            <!-- You can add more details here as needed -->
        </div>

        <div class="back-link">
            <a href="manage_complaints.php">Back to Manage Complaints</a>
        </div>
    </div>
</body>
</html>
