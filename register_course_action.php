<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

$student_id = isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($student_id === 0) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','message'=>'Please log in first']);
    } else {
        $_SESSION['message'] = "Please log in first.";
        header('Location: login.php');
    }
    exit;
}

$course_id = 0;
if ($is_ajax) {
    $data = json_decode(file_get_contents("php://input"), true);
    $course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
}

if ($course_id === 0) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','message'=>'Invalid course ID']);
    } else {
        $_SESSION['message'] = "Invalid course ID.";
        header('Location: courses.php');
    }
    exit;
}

// 检查课程存在
$stmt = $conn->prepare("SELECT 1 FROM courses WHERE course_id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','message'=>'Course does not exist']);
    } else {
        $_SESSION['message'] = "Course does not exist.";
        header('Location: courses.php');
    }
    exit;
}
$stmt->close();

// 检查是否已注册
$stmt = $conn->prepare("SELECT 1 FROM registrations WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','message'=>'Already registered']);
    } else {
        $_SESSION['message'] = "You have already registered this course.";
        header('Location: courses.php');
    }
    exit;
}
$stmt->close();

// 插入注册表
$stmt = $conn->prepare("INSERT INTO registrations (student_id, course_id, status, date_registered) VALUES (?, ?, 'Pending', NOW())");
$stmt->bind_param("ii", $student_id, $course_id);
$ok = $stmt->execute();
$stmt->close();

$conn->close();

if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode($ok ? ['status'=>'success','message'=>'Registered successfully'] : ['status'=>'error','message'=>'Registration failed']);
} else {
    $_SESSION['message'] = $ok ? "Course registration submitted." : "Registration failed.";
    header('Location: courses.php');
}
exit;
?>