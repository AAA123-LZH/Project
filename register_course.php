<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['student_id'])){
    echo json_encode(['status'=>'error','message'=>'Please log in first']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$course_id = isset($data['course_id']) ? trim($data['course_id']) : '';
$student_id = $_SESSION['student_id'];

if($course_id === ''){
    echo json_encode(['status'=>'error','message'=>'Invalid course ID']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "student_portal");
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'Database connection failed']);
    exit();
}

// Check if the course exists
$stmt = $conn->prepare("SELECT 1 FROM courses WHERE course_id=?");
$stmt->bind_param("s", $course_id);
$stmt->execute();
if($stmt->get_result()->num_rows == 0){
    echo json_encode(['status'=>'error','message'=>'Course does not exist']);
    $stmt->close(); $conn->close(); exit();
}
$stmt->close();

// Check if the student already registered
$stmt = $conn->prepare("SELECT 1 FROM registrations WHERE student_id=? AND course_id=?");
$stmt->bind_param("is", $student_id, $course_id); // student_id 是整数，course_id 是字符串
$stmt->execute();
if($stmt->get_result()->num_rows > 0){
    echo json_encode(['status'=>'error','message'=>'You have already registered for this course']);
    $stmt->close(); $conn->close(); exit();
}
$stmt->close();

// Insert registration record
$stmt = $conn->prepare("INSERT INTO registrations (student_id, course_id, status, registration_date) VALUES (?, ?, 'Pending', NOW())");
$stmt->bind_param("is", $student_id, $course_id);

if($stmt->execute()){
    echo json_encode(['status'=>'success','message'=>'Course registered successfully. Waiting for admin approval.']);
} else {
    echo json_encode(['status'=>'error','message'=>'Registration failed. Please try again later.']);
}

$stmt->close();
$conn->close();
?>


