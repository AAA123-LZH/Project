<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        
    
</head>
<head>
    <meta charset="UTF-8">
    <title>Student_homepage</title>
    <style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fb; /* 页面背景色 */
    color: #333;
    padding: 20px;
  }

  h2 {
    color: #2b8fb6;  /* 标题颜色 */
    text-align: center;
    margin-bottom: 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff; /* 表格背景 */
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  }

  th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
  }

  th {
    background-color: #2b8fb6;
    color: #fff;
  }

  tr:hover {
    background-color: #e6f2fb;
  }

  a.back {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #2b8fb6;
    font-weight: bold;
  }
</style>
</head>
<style>
body {
  font-family: Arial, sans-serif;
  background-color: #f4f7fb; /* 页面背景 */
  color: #333;
  padding: 20px;
   font-size: 40px;
}

.wrap {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
  margin: 0 auto;  /* 居中显示 */
  text-align: center;
}

h3 {
  color: #2b8fb6;
  margin-bottom: 20px;
}

ul {
  list-style: none;  /* 去掉小圆点 */
  padding: 0;
}

ul li {
  margin-bottom: 10px;
}

ul li a {
  display: block;
  text-decoration: none;
  background-color: #2b8fb6;
  color: #fff;
  padding: 10px;
  border-radius: 6px;
}

ul li a:hover {
  background-color: #1f6a91;
}
</style>
</head>

<body>
    <h2>Welcome back, <?php echo htmlspecialchars($username); ?>!</h2>

    <style>
body {
  font-family: Arial, sans-serif;
  background-color: #f4f7fb; /* 页面背景 */
  color: #333;
  padding: 20px;
}

.wrap {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
  margin: 0 auto;  /* 居中显示 */
  text-align: center;
}

h3 {
  color: #2b8fb6;
  margin-bottom: 20px;
}

ul {
  list-style: none;  /* 去掉小圆点 */
  padding: 0;
}

ul li {
  margin-bottom: 10px;
}

ul li a {
  display: block;
  text-decoration: none;
  background-color: #2b8fb6;
  color: #fff;
  padding: 10px;
  border-radius: 6px;
}

ul li a:hover {
  background-color: #1f6a91;
}
</style>
</head>
<body>
<div class="wrap">
  <h3>Student Menu</h3>

    
    <ul>
        <li><a href="courses.php">Register for a New Course</a></li>
        <li><a href="my_courses.php">View My Registered Courses</a></li>
        <li><a href="edit_profile.php">Edit Profile Information</a></li>
        
    </ul>


</body>

<body>
    <a href="logout.php" class="logout-btn">log out</a>
</body>
    
 

    <script>
        // 简单的交互效果
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>