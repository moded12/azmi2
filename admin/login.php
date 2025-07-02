<?php
// admin/login.php
session_start();
// إخفاء الأخطاء الحقيقية في الإنتاج
ini_set('display_errors', 0);
error_reporting(0);

// استخدم password_hash لاستيراد الهَشّ التالي من كلمة مرور قوية:
// (مرة واحدة في بيئة التطوير)
// echo password_hash('كلمة_مرور_قوية_جداً', PASSWORD_DEFAULT);
$stored_hash = '$2y$10$HjAmD9viqfabkCTNqVgf6ucJIyg8j9ydj8Pf0Mk0lYEVWLaK8L.AK';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($user === 'admin' && password_verify($pass, $stored_hash)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: manage_content.php');
        exit;
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول | لوحة الإدارة</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f5f5;
      font-family: 'Cairo', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .login-card {
      width: 360px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.1);
      padding: 32px 24px;
    }
    .login-card h2 {
      margin-bottom: 24px;
      font-weight: bold;
      color: #1976d2;
      text-align: center;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #1976d2;
    }
    .btn-login {
      width: 100%;
      background: #1976d2;
      color: #fff;
      font-weight: bold;
      border: none;
      padding: 10px;
      border-radius: 8px;
      transition: background .2s;
    }
    .btn-login:hover {
      background: #005bb5;
    }
    .error-msg {
      color: #dc3545;
      text-align: center;
      margin-bottom: 12px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>تسجيل دخول الإدارة</h2>
    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-login">دخول</button>
    </form>
  </div>
</body>
</html>