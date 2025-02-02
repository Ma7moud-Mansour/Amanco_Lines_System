<?php
// اتصال بقاعدة البيانات
$host = 'localhost'; // أو عنوان السيرفر
$dbname = 'amancom_db';
$username = 'root'; // اسم المستخدم لقاعدة البيانات
$password = ''; // كلمة المرور لقاعدة البيانات

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// التحقق من إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // البحث عن المستخدم في قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // التحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            // تسجيل الدخول ناجح
            session_start();
            $_SESSION['username'] = $user['username'];
            header("Location: Dashboard.php"); // توجيه المستخدم لصفحة أخرى
            exit();
        } else {
            // كلمة المرور غير صحيحة
            echo "<script>alert('كلمة المرور غير صحيحة!'); window.location.href = 'index.html';</script>";
        }
    } else {
        // المستخدم غير موجود
        echo "<script>alert('اسم المستخدم غير صحيح!'); window.location.href = 'index.html';</script>";
    }
}
?> 

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../Style/Login.css" />
</head>
<body>
    <h1>!اهلا بيك في أمانكوم</h1>
    <div class="background"></div>
    <div class="login-box">
        <h2>تسجيل الدخول</h2>
        <form id="loginForm" action="login.php" method="POST">
            <input type="text" id="username" name="username" placeholder="اسم المستخدم" required>
            <input type="password" id="password" name="password" placeholder="كلمة السر" required>
            <button type="submit">دخول</button>
            <div class="error-message" id="errorMessage">اسم المستخدم أو كلمة السر غير صحيحة!</div>
        </form>
        <a href="#">نسيت كلمة السر؟</a>
    </div>

    <script>
        // التحقق من البيانات باستخدام JavaScript
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // لمنع إعادة تحميل الصفحة

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');

            // بيانات الدخول الصحيحة (مثال)
            const correctUsername = 'admin';
            const correctPassword = '123456';

            if (username === correctUsername && password === correctPassword) {
                alert('تم الدخول بنجاح!');
                errorMessage.style.display = 'none'; // إخفاء رسالة الخطأ
                // توجيه المستخدم لصفحة أخرى (مثال)
                window.location.href = 'Dashboard.php';
            } else {
                errorMessage.style.display = 'block'; // عرض رسالة الخطأ
            }
        });
    </script>
</body>
</html>