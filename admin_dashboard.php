<?php
session_start();
include('config.php');

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all complaints from the database
$sql = "SELECT * FROM complaints";
$result = $conn->query($sql);

// Initialize complaints array
$complaints = [];

// Fetch complaints into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

// Function to get user's username based on user_id (for demo purpose)
function getUsername($conn, $user_id) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['username'];
    } else {
        return "Unknown User";
    }
}

// Handle actions (update status, delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
        $complaint_id = $_POST['complaint_id'];
        $new_status = $_POST['new_status'];

        $sql = "UPDATE complaints SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $complaint_id);
        $stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: admin_dashboard.php");
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $complaint_id = $_POST['complaint_id'];

        $sql = "DELETE FROM complaints WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2b2b2b;
            color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #393939;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        h1 {
            text-align: center;
            color: #e74c3c;
            margin-bottom: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #4e4e4e;
        }
        th {
            background-color: #e74c3c;
            color: #f7f7f7;
        }
        tbody tr:nth-child(even) {
            background-color: #4e4e4e;
        }
        tbody tr:nth-child(odd) {
            background-color: #393939;
        }
        .button {
            padding: 10px 20px;
            background-color: #3498db;
            color: #f7f7f7;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 5px;
        }
        .button:hover {
            background-color: #2980b9;
        }
        .button.delete {
            background-color: #e74c3c;
        }
        .button.delete:hover {
            background-color: #c0392b;
        }
        .status-form {
            display: inline-block;
            margin-right: 10px;
        }
        a{
            text-decoration:none;
            color:WHITE;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard - Manage Complaints</h1>
        <button class="button" ><a href="logout.php">Logout</a></button>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?php echo $complaint['id']; ?></td>
                        <td><?php echo getUsername($conn, $complaint['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['category']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                        <td>
                            <form class="status-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                <select name="new_status">
                                    <option value="Pending" <?php echo ($complaint['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?php echo ($complaint['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <input type="hidden" name="action" value="update_status">
                                <button type="submit" class="button">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="button delete" onclick="return confirm('Are you sure you want to delete this complaint?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
