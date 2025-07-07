<?php
// إعدادات الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "azmi6";
$username = "azmi6";
$password = "Tvvcrtv1610@";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // تفعيل وضع الأخطاء الاستثنائية
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>