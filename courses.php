<?php
session_start();
require 'db.php'; // Your database connection file

// Ensure student is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = (int)$_SESSION['user_id'];

// Fetch available courses (exclude courses already registered by the student)
$sql = "SELECT c.course_id, c.course_code, c.course_name, c.credits, c.lecturer 
        FROM courses c
        WHERE c.course_id NOT IN (
            SELECT r.course_id 
            FROM registrations r 
            WHERE r.student_id = ?
        )";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$courses = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Courses</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f7fb; padding: 20px; }
.wrap { max-width: 700px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; text-align: center; }
h2 { color: #2b8fb6; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background: #2b8fb6; color: #fff; }
tr:hover { background: #eaf6ff; }
button { padding: 6px 12px; border: none; border-radius: 5px; background: #2b8fb6; color: #fff; cursor: pointer; }
button:hover { background: #1f6a91; }
.back { display: inline-block; margin-top: 12px; color: #2b8fb6; text-decoration: none; }
</style>
</head>
<body>
<?php if(isset($_SESSION['message'])): ?>
    <p style="color:green;"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="wrap">
  <h2>Available Courses</h2>
  <?php if(empty($courses)): ?>
    <p>No courses available at the moment or you have registered all available courses.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Course Code</th>
        <th>Course Name</th>
        <th>Credits</th>
        <th>Lecturer</th>
        <th>Action</th>
      </tr>
      <?php foreach($courses as $c): ?>
      <tr>
        <td><?php echo htmlspecialchars($c['course_code']); ?></td>
        <td><?php echo htmlspecialchars($c['course_name']); ?></td>
        <td><?php echo (int)$c['credits']; ?></td>
        <td><?php echo htmlspecialchars($c['lecturer']); ?></td>
        <td>
          <form method="post" action="register_course_action.php" style="margin:0;">
            <input type="hidden" name="course_id" value="<?php echo (int)$c['course_id']; ?>">
            <button type="submit">Register</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  <a class="back" href="student_home.php">← Back to Dashboard</a>
</div>
</body>
</html>