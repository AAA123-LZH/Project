<?php
session_start();
require 'db.php';

// 确保管理员已登录
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$error = '';
$success = '';

// 处理添加新课程
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $credits = (int)$_POST['credits'];
    $lecturer = trim($_POST['lecturer']);

    // 验证输入
    if (empty($course_code) || empty($course_name) || empty($lecturer)) {
        $error = 'Course code, course name and lecturer are required';
    } elseif ($credits <= 0) {
        $error = 'Credits must be greater than 0';
    } else {
        // 检查课程代码是否已存在
        $stmt = $conn->prepare("SELECT course_id FROM courses WHERE course_code = ?");
        $stmt->bind_param("s", $course_code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Course code already exists';
        } else {
            // 插入新课程
            $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, credits, lecturer) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $course_code, $course_name, $credits, $lecturer);
            
            if ($stmt->execute()) {
                $success = 'Course added successfully!';
                // 清空表单
                $_POST = array();
            } else {
                $error = 'Failed to add course: ' . $conn->error;
            }
        }
        $stmt->close();
    }
}

// 获取所有课程列表
$courses_result = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_id DESC");
$courses = [];
if ($courses_result) {
    $courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - Add New Course</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f7fb; 
            padding: 20px;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #d9534f;
            text-align: center;
            margin-bottom: 30px;
        }
        .error { 
            color: #d9534f; 
            background: #f8d7da; 
            padding: 15px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
        }
        .success { 
            color: #155724; 
            background: #d4edda; 
            padding: 15px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
        }
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #d9534f;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #333;
        }
        input[type="text"], input[type="number"] {
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box;
            font-size: 16px;
        }
        button { 
            padding: 12px 30px; 
            background: #d9534f; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { 
            background: #c9302c; 
        }
        .courses-section {
            margin-top: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #d9534f;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .back-link { 
            margin-top: 30px; 
            text-align: center;
        }
        .back-link a { 
            color: #d9534f; 
            text-decoration: none;
            font-weight: bold;
            padding: 10px 20px;
            border: 2px solid #d9534f;
            border-radius: 4px;
        }
        .back-link a:hover {
            background: #d9534f;
            color: white;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Add New Course</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
            
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- 添加新课程表单 -->
        <div class="form-section">
            <h2 style="margin-top: 0; color: #333;">Add New Course</h2>
            <form method="post" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="course_code">Course Code *</label>
                        <input type="text" id="course_code" name="course_code" 
                               value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>" 
                               placeholder="e.g., CS101"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="course_name">Course Name *</label>
                        <input type="text" id="course_name" name="course_name" 
                               value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>" 
                               placeholder="e.g., Introduction to Programming"
                               required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="credits">Credits *</label>
                        <input type="number" id="credits" name="credits" min="1" max="10" 
                               value="<?php echo isset($_POST['credits']) ? htmlspecialchars($_POST['credits']) : '3'; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lecturer">Lecturer *</label>
                        <input type="text" id="lecturer" name="lecturer" 
                               value="<?php echo isset($_POST['lecturer']) ? htmlspecialchars($_POST['lecturer']) : ''; ?>" 
                               placeholder="e.g., Dr. Smith"
                               required>
                    </div>
                </div>

                <button type="submit">➕ Add Course</button>
            </form>
        </div>

        <!-- 现有课程列表 -->
        <div class="courses-section">
            <h2 style="color: #333; border-bottom: 2px solid #d9534f; padding-bottom: 10px;">
                Existing Courses (<?php echo count($courses); ?>)
            </h2>
            
            <?php if (empty($courses)): ?>
                <p style="text-align: center; color: #666; padding: 20px;">No courses available. Add your first course above.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Lecturer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($course['course_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['credits']); ?></td>
                            <td><?php echo htmlspecialchars($course['lecturer']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="admin_dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>