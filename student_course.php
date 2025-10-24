<?php
// 显示所有错误
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// 获取学生ID
$student_id = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
} elseif (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
}

$student_info = null;
$courses_result = null;

if ($student_id > 0) {
    // 获取学生信息
    $student_sql = "SELECT student_id, username, full_name, email, program FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($student_sql);
    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $student_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($student_info) {
        // 获取学生的注册课程 - 使用正确的表名 registrations
        $courses_sql = "
            SELECT r.registration_id, r.date_registered, r.status,
                   c.course_code, c.course_name, c.credits, c.lecturer, c.description
            FROM registrations r
            JOIN courses c ON r.course_id = c.course_id
            WHERE r.student_id = ?
            ORDER BY r.date_registered DESC
        ";
        
        $stmt = $conn->prepare($courses_sql);
        if ($stmt) {
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $courses_result = $stmt->get_result();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Courses - SETU</title>
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
        .search-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            align-items: end;
        }
        .form-group {
            flex: 1;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            padding: 10px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .student-info {
            background: #e7f3ff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .courses-table th,
        .courses-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .courses-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            color: #666;
            padding: 50px;
        }
        .no-student {
            text-align: center;
            color: #dc3545;
            padding: 30px;
            background: #f8d7da;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <a href="admin_home.php" class="back-link">← Back to Admin Menu</a>
    
    <div class="container">
        <h1>View Student Courses</h1>

        <!-- 搜索框 -->
        <div class="search-box">
            <form method="POST" class="search-form">
                <div class="form-group">
                    <label for="student_id">Enter Student ID:</label>
                    <input type="number" id="student_id" name="student_id" 
                           value="<?php echo $student_id ? $student_id : ''; ?>" 
                           placeholder="Example: 1, 2, 3..." required>
                </div>
                <button type="submit" class="btn">Search Courses</button>
            </form>
        </div>

        <?php if ($student_id > 0): ?>
            <?php if ($student_info): ?>
                <!-- 学生信息 -->
                <div class="student-info">
                    <div class="student-name"><?php echo htmlspecialchars($student_info['full_name']); ?></div>
                    <div class="student-details">
                        <strong>Student ID:</strong> <?php echo $student_info['student_id']; ?> | 
                        <strong>Username:</strong> <?php echo htmlspecialchars($student_info['username']); ?> | 
                        <strong>Program:</strong> <?php echo htmlspecialchars($student_info['program']); ?> | 
                        <strong>Email:</strong> <?php echo htmlspecialchars($student_info['email']); ?>
                    </div>
                </div>

                <!-- 课程表格 -->
                <h2>Registered Courses</h2>

                <?php if ($courses_result && $courses_result->num_rows > 0): ?>
                    <table class="courses-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Credits</th>
                                <th>Lecturer</th>
                                <th>Description</th>
                                <th>Date Registered</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($course = $courses_result->fetch_assoc()): ?>
                                <?php
                                $status_class = 'status-' . $course['status'];
                                $registration_date = date('Y-m-d H:i', strtotime($course['date_registered']));
                                ?>
                                <tr>
                                    <td><strong><?php echo $course['course_code']; ?></strong></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td><?php echo $course['credits']; ?></td>
                                    <td><?php echo $course['lecturer']; ?></td>
                                    <td><?php echo $course['description']; ?></td>
                                    <td><?php echo $registration_date; ?></td>
                                    <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($course['status']); ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        This student has not registered for any courses yet.
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-student">
                    Student with ID <?php echo $student_id; ?> not found.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; color: #666; padding: 50px;">
                Enter a Student ID to view their registered courses.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php 
if (isset($stmt)) $stmt->close();
$conn->close(); 
?>