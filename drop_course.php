<?php
session_start();
header('Content-Type: application/json');

// 统一的session用户ID检查
$student_id = isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);

error_log("Drop course request - Student ID: " . $student_id);

if($student_id === 0){
    error_log("User not logged in");
    echo json_encode(['status'=>'error','message'=>'Please log in first']);
    exit();
}

$input = file_get_contents("php://input");
error_log("Raw input: " . $input);

$data = json_decode($input, true);
error_log("Parsed data: " . print_r($data, true));

$course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;
error_log("Course ID received: " . $course_id);

if($course_id <= 0){
    error_log("Invalid course ID detected: " . $course_id);
    echo json_encode(['status'=>'error','message'=>'Invalid course ID: ' . $course_id]);
    exit();
}

// 使用统一的数据库连接
require 'db.php';

// Check if the student has registered this course
$stmt = $conn->prepare("SELECT 1 FROM registrations WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
if($stmt->get_result()->num_rows == 0){
    error_log("Student " . $student_id . " is not registered in course " . $course_id);
    echo json_encode(['status'=>'error','message'=>'You are not registered in this course']);
    $stmt->close(); 
    $conn->close();
    exit();
}
$stmt->close();

// Delete registration
$stmt = $conn->prepare("DELETE FROM registrations WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
if($stmt->execute()){
    error_log("Successfully dropped course " . $course_id . " for student " . $student_id);
    echo json_encode(['status'=>'success','message'=>'Course dropped successfully']);
} else {
    error_log("Failed to drop course: " . $conn->error);
    echo json_encode(['status'=>'error','message'=>'Failed to drop course. Please try again.']);
}

$stmt->close();
$conn->close();
?>