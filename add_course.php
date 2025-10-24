<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - SETU</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <a href="admin_home.php" class="back-link">← Back to Admin Menu</a>
    
    <div class="container">
        <h1>Add New Course</h1>
        
        <form action="save_course.php" method="POST">
            <div class="form-group">
                <label for="course_code">Course Code *</label>
                <input type="text" id="course_code" name="course_code" required 
                       placeholder="e.g., CS101">
            </div>
            
            <div class="form-group">
                <label for="course_name">Course Name *</label>
                <input type="text" id="course_name" name="course_name" required 
                       placeholder="e.g., Introduction to Programming">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" 
                         placeholder="Course description..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="credits">Credits *</label>
                <select id="credits" name="credits" required>
                    <option value="1">1 Credit</option>
                    <option value="2">2 Credits</option>
                    <option value="3" selected>3 Credits</option>
                    <option value="4">4 Credits</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="lecturer">Lecturer *</label>
                <input type="text" id="lecturer" name="lecturer" required 
                       placeholder="e.g., Dr. John Smith">
            </div>

            <div class="btn-group">
                <a href="admin_home.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Course</button>
            </div>
        </form>
    </div>
</body>
</html>