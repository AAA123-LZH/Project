<?php
session_start();

// 清除所有session变量
$_SESSION = array();

// 销毁session
session_destroy();

// 重定向到管理员登录页面
header('Location: admin_login.php');
exit;
?>