<?php
session_start();

// 检查是否管理员登录
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }
        .menu {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .menu-item {
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .menu-item:hover {
            background: #0056b3;
        }
        .logout {
            display: block;
            width: 100%;
            padding: 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administrator Menu</h1>
        <div class="welcome">
            Welcome, <?php echo $_SESSION['username']; ?>! (Administrator)
        </div>
        
        <div class="menu">
            <a href="add_course.php" class="menu-item">
                <h3>Add New Courses</h3>
                <p>Create new courses for students</p>
            </a>
            
            <a href="view_students.php" class="menu-item">
                <h3>View All Students</h3>
                <p>See all registered students</p>
            </a>
            
            <a href="manage_registrations.php" class="menu-item">
                <h3>Approve/Remove Registrations</h3>
                <p>Manage course registrations</p>
            </a>
            
            <a href="student_course.php" class="menu-item">
                <h3>View Student Courses</h3>
                <p>See courses per student</p>
            </a>
        </div>
        
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>