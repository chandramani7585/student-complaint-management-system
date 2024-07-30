<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Function to get user's username based on user_id
function getUserUsername($conn, $user_id) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #282c34;
            margin: 0;
            padding: 0;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background-color: #20232a;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 600px;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 24px;
            color: #61dafb;
        }
        .dashboard-header p {
            margin: 5px 0 0;
        }
        .action-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .action-buttons a {
            display: block;
            background-color: #61dafb;
            color: #20232a;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 10px 0;
            text-decoration: none;
            text-align: center;
            width: 80%;
            transition: background-color 0.3s ease;
        }
        .action-buttons a:hover {
            background-color: #21a1f1;
        }
        .logout-link {
            text-align: center;
            margin-top: 30px;
        }
        .logout-link a {
            color: #61dafb;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .logout-link a:hover {
            color: #21a1f1;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars(getUserUsername($conn, $user_id)); ?>!</p>
        </div>

        <div class="action-buttons">
            <?php if ($role === 'admin'): ?>
                <a href="manage_complaints.php">Manage Complaints</a>
                <!-- Add more admin-specific actions/buttons here -->
            <?php else: ?>
                <a href="file_complaint.php">File a Complaint</a>
                <a href="view_complaints.php">View Your Complaints</a>
                <!-- Add more student-specific actions/buttons here -->
            <?php endif; ?>
        </div>

        <div class="logout-link">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
