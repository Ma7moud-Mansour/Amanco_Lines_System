<?php
session_start();
session_destroy(); // إنهاء الجلسة
header("Location: Login.php"); // إعادة التوجيه لصفحة تسجيل الدخول
exit();
?>