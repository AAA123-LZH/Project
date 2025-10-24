<?php
session_start();
require 'db.php';

// 确保学生已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = (int)$_SESSION['user_id'];

// 获取当前学生已注册课程
$sql = "SELECT r.registration_id, c.course_id, c.course_code, c.course_name, c.credits, c.lecturer, r.status
        FROM registrations r
        JOIN courses c ON r.course_id = c.course_id
        WHERE r.student_id = ?
        ORDER BY r.date_registered DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Courses</title>
<style>
body { font-family: Arial; background: #f4f7fb; padding: 20px; }
.wrap { max-width:700px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; text-align:center; }
h2 { color:#2b8fb6; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border:1px solid #ccc; text-align:left; }
th { background:#2b8fb6; color:#fff; }
tr:hover { background:#eaf6ff; }
.back { display:inline-block; margin-top:12px; color:#2b8fb6; text-decoration:none; }
</style>
</head>
<body>
<div class="wrap">
  <h2>My Courses</h2>
  <div id="message"></div>
  <?php if(empty($courses)): ?>
    <p>You have not registered for any courses yet.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Course Code</th>
        <th>Course Name</th>
        <th>Credits</th>
        <th>Lecturer</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php foreach($courses as $c): ?>
      <tr id="course-<?php echo (int)$c['course_id']; ?>">
        <td><?php echo htmlspecialchars($c['course_code']); ?></td>
        <td><?php echo htmlspecialchars($c['course_name']); ?></td>
        <td><?php echo (int)$c['credits']; ?></td>
        <td><?php echo htmlspecialchars($c['lecturer']); ?></td>
        <td><?php echo htmlspecialchars($c['status']); ?></td>
        <td>
          <?php if($c['status'] != 'Dropped'): ?>
            <button onclick="dropCourse(<?php echo (int)$c['course_id']; ?>)" 
                    style="padding:4px 8px; font-size:12px;">
              Drop
            </button>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  <a class="back" href="student_home.php">← Back to Dashboard</a>
</div>

<script>
function dropCourse(courseId) {
    console.log('Dropping course ID:', courseId);
    
    if (!courseId || courseId <= 0) {
        alert('Invalid course ID: ' + courseId);
        return;
    }
    
    if (!confirm('Are you sure you want to drop this course?')) {
        return;
    }
    
    fetch('drop_course.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({course_id: courseId})
    })
    .then(res => res.json())
    .then(data => {
        console.log('Server response:', data);
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = data.message;

        if(data.status === 'success'){
            msgDiv.style.color = 'green';
            // 移除对应的表格行
            const row = document.getElementById('course-' + courseId);
            if(row) {
                row.remove();
            }
            // 如果没有课程了，刷新页面
            if(document.querySelectorAll('table tr').length <= 1) {
                location.reload();
            }
        } else {
            msgDiv.style.color = 'red';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = 'Request failed. Please try again.';
        msgDiv.style.color = 'red';
    });
}
</script>
</body>
</html>