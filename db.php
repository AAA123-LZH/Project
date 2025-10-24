<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "course_registration";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
?>