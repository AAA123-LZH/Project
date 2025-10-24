<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $dbname = "course_registration";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 获取用户输入
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    $login_type = $_POST['login_type'];

  
    if ($login_type == 'admin') {
        // 检查管理员表是否存在
        $table_check = $conn->query("SHOW TABLES LIKE 'admins'");
        if ($table_check->num_rows == 0) {
            echo "<script>alert('管理员表不存在！请先创建管理员表。'); window.location.href='';</script>";
            exit();
        }

        // 管理员登录逻辑
        $sql = "SELECT * FROM admins WHERE username = '$input_username' AND password = '$input_password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = 'admin';
            
            echo "<script>alert('管理员登录成功！'); window.location.href='admin_home.php';</script>";
            exit();
        } else {
            echo "<script>alert('管理员用户名或密码错误！'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        // 学生登录逻辑
        $sql = "SELECT * FROM students WHERE username = '$input_username' AND password = '$input_password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['student_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = 'student';
            
            echo "<script>alert('学生登录成功！'); window.location.href='student_home.php';</script>";
            exit();
        } else {
            echo "<script>alert('学生用户名或密码错误！'); window.location.href='login.html';</script>";
            exit();
        }
    }

    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>