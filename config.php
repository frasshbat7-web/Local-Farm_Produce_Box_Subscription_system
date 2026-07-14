<?php
// بدء الجلسة فقط إذا لم تكن قد بدأت بالفعل
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// إعدادات الاتصال بقاعدة البيانات
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'farm_subscription_db');

// محاولة الاتصال بقاعدة البيانات
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// التحقق من الاتصال
if($conn === false){
    die("خطأ في الاتصال: " . mysqli_connect_error());
}

// تعيين الترميز إلى UTF-8 للدعم العربي
mysqli_set_charset($conn, "utf8");

// التحقق من تسجيل الدخول
function checkLogin() {
    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
        header("location: login.php");
        exit;
    }
}

// دالة تسجيل الدخول المبسطة
function login($username, $password) {
    global $conn;
    
    // استخدام كلمة مرور بسيطة للتحقق
    if($username === "admin" && $password === "admin123"){
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = 1;
        $_SESSION["username"] = $username;
        return true;
    } else {
        return false;
    }
}

// دالة للحصول على إحصائيات النظام
function getSystemStats() {
    global $conn;
    
    $stats = array();
    
    // إجمالي المشتركين
    $sql = "SELECT COUNT(*) as total FROM subscribers";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_subscribers'] = $row['total'] ? $row['total'] : 0;
    
    // المشتركين النشطين
    $sql = "SELECT COUNT(*) as total FROM subscribers WHERE subscription_status = 'نشط'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['active_subscribers'] = $row['total'] ? $row['total'] : 0;
    
    // المشتركين حسب نوع الاشتراك
    $sql = "SELECT subscription_type, COUNT(*) as total FROM subscribers GROUP BY subscription_type";
    $result = mysqli_query($conn, $sql);
    $stats['subscribers_by_type'] = array();
    while($row = mysqli_fetch_assoc($result)) {
        $stats['subscribers_by_type'][$row['subscription_type']] = $row['total'];
    }
    
    // أحدث المشتركين
    $sql = "SELECT full_name, start_date FROM subscribers ORDER BY start_date DESC LIMIT 5";
    $result = mysqli_query($conn, $sql);
    $stats['recent_subscribers'] = array();
    while($row = mysqli_fetch_assoc($result)) {
        $stats['recent_subscribers'][] = $row;
    }
    
    return $stats;
}
?>