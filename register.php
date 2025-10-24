<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// 如果已经登录，重定向到主页
if (isset($_SESSION['user_id'])) {
    header('Location: student_home.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 验证输入
    if (empty($student_id) || empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // 检查学生ID是否已存在
        $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ? OR email = ?");
        $stmt->bind_param("ss", $student_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Student ID or Email already exists';
        } else {
            // 创建新学生账户 - 确保所有字段都有值
            $username = $student_id; // 使用student_id作为username
            $full_name = $name;      // 确保full_name有值
            
            $stmt = $conn->prepare("INSERT INTO students (student_id, username, password, full_name, email) VALUES (?, ?, ?, ?, ?)");
            
            // 调试输出
            error_log("Inserting: student_id=$student_id, username=$username, full_name=$full_name, email=$email");
            
            $stmt->bind_param("sssss", $student_id, $username, $password, $full_name, $email);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
                // 清空表单
                $_POST = array();
            } else {
                $error = 'Registration failed: ' . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <style>
        body { font-family: Arial; background: #f4f7fb; padding: 40px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2b8fb6; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        button { width: 100%; padding: 12px; background: #2b8fb6; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1f6a91; }
        .error { color: red; margin-bottom: 15px; text-align: center; background: #ffe6e6; padding: 10px; border-radius: 4px; }
        .success { color: green; margin-bottom: 15px; text-align: center; background: #e6ffe6; padding: 10px; border-radius: 4px; }
        .login-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Registration</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label>Student ID:</label>
                <input type="text" name="student_id" value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>