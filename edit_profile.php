<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// 确保学生已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$error = '';
$success = '';

// 获取当前学生信息
$stmt = $conn->prepare("SELECT student_id, username, full_name, email FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found");
}

$student = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 调试信息
    error_log("Edit Profile - Name: $name, Email: $email, Student ID: $student_id");

    // 验证必填字段
    if (empty($name) || empty($email)) {
        $error = 'Name and email are required';
    } else {
        // 检查邮箱是否被其他用户使用
        $stmt = $conn->prepare("SELECT student_id FROM students WHERE email = ? AND student_id != ?");
        $stmt->bind_param("ss", $email, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email is already taken by another user';
        } else {
            // 更新基本信息 - 确保值不为null
            $stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ? WHERE student_id = ?");
            
            // 再次确保值不为空
            if (empty($name) || empty($email)) {
                $error = 'Name and email cannot be empty';
            } else {
                $stmt->bind_param("sss", $name, $email, $student_id);
                
                if ($stmt->execute()) {
                    // 更新session中的姓名
                    $_SESSION['name'] = $name;
                    $_SESSION['username'] = $name;
                    
                    // 如果提供了新密码，验证并更新密码
                    if (!empty($new_password)) {
                        if (empty($current_password)) {
                            $error = 'Current password is required to change password';
                        } elseif ($new_password !== $confirm_password) {
                            $error = 'New passwords do not match';
                        } elseif (strlen($new_password) < 6) {
                            $error = 'New password must be at least 6 characters long';
                        } else {
                            // 验证当前密码
                            $stmt_check = $conn->prepare("SELECT password FROM students WHERE student_id = ?");
                            $stmt_check->bind_param("s", $student_id);
                            $stmt_check->execute();
                            $result_check = $stmt_check->get_result();
                            $user = $result_check->fetch_assoc();
                            $stmt_check->close();
                            
                            if ($current_password === $user['password']) {
                                $stmt_pass = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ?");
                                $stmt_pass->bind_param("ss", $new_password, $student_id);
                                
                                if ($stmt_pass->execute()) {
                                    $success = 'Profile and password updated successfully';
                                } else {
                                    $error = 'Failed to update password';
                                }
                                $stmt_pass->close();
                            } else {
                                $error = 'Current password is incorrect';
                            }
                        }
                    } else {
                        $success = 'Profile updated successfully';
                    }
                } else {
                    $error = 'Failed to update profile: ' . $conn->error;
                }
            }
            $stmt->close();
        }
    }
    
    // 重新获取学生信息
    $stmt = $conn->prepare("SELECT student_id, username, full_name, email FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; background: #f4f7fb; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2b8fb6; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        button { 
            padding: 10px 20px; 
            background: #2b8fb6; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-right: 10px;
        }
        button:hover { background: #1f6a91; }
        .error { color: #d9534f; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .back-link { margin-top: 20px; }
        .back-link a { color: #2b8fb6; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .password-section { border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile Information</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" readonly style="background-color: #f5f5f5;">
                <small style="color: #666;">Student ID cannot be changed</small>
            </div>
            
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            
            <div class="password-section">
                <h3>Change Password (Optional)</h3>
                
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password to change">
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                </div>
            </div>
            
            <button type="submit">Update Profile</button>
            <a href="student_home.php">Cancel</a>
        </form>
        
        <div class="back-link">
            <a href="student_home.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>