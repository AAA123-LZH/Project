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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - SETU</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .students-table th,
        .students-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .students-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .students-table tr:hover {
            background: #f8f9fa;
        }
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }
        .program-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            color: #495057;
        }
    </style>
</head>
<body>
    <a href="admin_home.php" class="back-link">← Back to Admin Menu</a>
    
    <div class="container">
        <h1>All Students</h1>

        <?php
        // 获取学生总数
        $total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
        
        // 获取各专业学生数量
        $program_stats = $conn->query("
            SELECT program, COUNT(*) as count 
            FROM students 
            WHERE program IS NOT NULL AND program != '' 
            GROUP BY program
        ");
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_students; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <?php
            if ($program_stats && $program_stats->num_rows > 0) {
                while($program = $program_stats->fetch_assoc()) {
                    echo "<div class='stat-card'>
                            <div class='stat-number'>{$program['count']}</div>
                            <div class='stat-label'>{$program['program']}</div>
                          </div>";
                }
            }
            ?>
        </div>

        <table class="students-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Program</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 获取所有学生数据
                $result = $conn->query("
                    SELECT student_id, username, full_name, email, program
                    FROM students 
                    ORDER BY student_id
                ");
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $program_display = $row['program'] ? 
                            "<span class='program-badge'>{$row['program']}</span>" : 
                            "<span style='color: #999;'>Not set</span>";
                        
                        echo "<tr>
                                <td><strong>{$row['student_id']}</strong></td>
                                <td>{$row['username']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$program_display}</td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="5" class="no-data">No students found in the system.</td></tr>';
                }
                
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>