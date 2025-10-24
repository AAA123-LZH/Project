<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

echo "<h2>Edit Profile Debug</h2>";

// 检查session
echo "<h3>Session Info:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 检查数据库连接
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "<p style='color:green'>Database connected</p>";

// 检查当前用户数据
if (isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    echo "<p>User ID from session: $student_id</p>";
    
    // 检查表结构
    echo "<h3>Table Structure:</h3>";
    $result = mysqli_query($conn, "DESCRIBE students");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 检查当前用户数据
    echo "<h3>Current User Data:</h3>";
    $sql = "SELECT * FROM students WHERE id = $student_id";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo "<p style='color:red'>Query failed: " . mysqli_error($conn) . "</p>";
    } elseif (mysqli_num_rows($result) === 0) {
        echo "<p style='color:red'>No user found with ID: $student_id</p>";
    } else {
        $user = mysqli_fetch_assoc($result);
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        // 测试查询语句
        echo "<h3>Testing Prepared Statement:</h3>";
        $stmt = $conn->prepare("SELECT student_id, full_name, email FROM students WHERE id = ?");
        if ($stmt) {
            echo "<p style='color:green'>Prepare successful</p>";
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $data = $result->fetch_assoc();
                echo "<p>Data from prepared statement:</p>";
                echo "<pre>";
                print_r($data);
                echo "</pre>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red'>Prepare failed: " . $conn->error . "</p>";
        }
    }
} else {
    echo "<p style='color:red'>No user_id in session</p>";
}
?>