<?php
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user's complaints from database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM complaints WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize complaints array
$complaints = [];

// Fetch complaints into an array
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #1c1c1c;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        h1 {
            text-align: center;
            color: #e74c3c;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #34495e;
        }
        th {
            background-color: #34495e;
            color: #ecf0f1;
        }
        tbody tr:nth-child(even) {
            background-color: #3e4a55;
        }
        tbody tr:nth-child(odd) {
            background-color: #2c3e50;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #2980b9;
            transition: background-color 0.3s ease;
        }
        .back-link a:hover {
            background-color: #1c5980;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Your Filed Complaints</h1>

        <?php if (count($complaints) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No complaints filed yet.</p>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
