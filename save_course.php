<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $dbname = "course_registration";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 获取表单数据
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $credits = intval($_POST['credits']);
    $lecturer = trim($_POST['lecturer']);

    // 基本验证
    if (empty($course_code) || empty($course_name)) {
        echo "<script>alert('Course code and name are required!'); history.back();</script>";
        exit();
    }


    // 检查课程代码是否已存在
    $check_sql = "SELECT course_id FROM courses WHERE course_code = '$course_code'";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
        echo "<script>alert('Course code \\'$course_code\\' already exists! Please use a different code.'); history.back();</script>";
        exit();
    }

    // 插入新课程
    $sql = "INSERT INTO courses (course_code, course_name, description, credits, lecturer) 
            VALUES ('$course_code', '$course_name', '$description', $credits, '$lecturer')";

    if ($conn->query($sql) === TRUE) {
        header("Location: course_success.php");
        exit();
    } else {
        echo "<script>alert('Error adding course: " . $conn->error . "'); history.back();</script>";
    }

    $conn->close();
} else {
    header("Location: add_course.php");
    exit();
}
?>
