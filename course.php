<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}

$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "course_registration";

$conn = new mysqli($servername, $username, $password, $dbname);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - SETU</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .header-actions {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .courses-table th,
        .courses-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .courses-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 0 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-btn {
            background: #ffc107;
            color: black;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="card">
        <a href="admin_home.php" class="btn">← Back to Menu</a>
        
        <div class="header-actions">
            <h1>Manage Courses</h1>
            <a href="add_course.php" class="btn btn-success">+ Add New Course</a>
        </div>

        <table class="courses-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Max Students</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM courses ORDER BY course_code");
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['course_code']}</td>
                                <td>{$row['course_name']}</td>
                                <td>{$row['credits']}</td>
                                <td>{$row['max_students']}</td>
                                <td>{$row['description']}</td>
                                <td>
                                    <button class='action-btn edit-btn'>Edit</button>
                                    <button class='action-btn delete-btn'>Delete</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="6" class="no-data">No courses found. <a href="add_course.php">Add the first course</a></td></tr>';
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>