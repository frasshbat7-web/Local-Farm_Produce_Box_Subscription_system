<?php
include "config.php";
checkLogin();

if(!isset($_GET['id'])) {
    header("Location: subscribers.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM subscribers WHERE id = $id";
$result = mysqli_query($conn, $sql);
$subscriber = mysqli_fetch_assoc($result);

if(!$subscriber) {
    header("Location: subscribers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المشترك - نظام الاشتراكات الزراعية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000000;
            --primary-dark: #333333;
            --secondary: #4CAF50;
            --success: #4CAF50;
            --danger: #F44336;
            --info: #2196F3;
            --warning: #FF9800;
            --light: #f8f9fa;
            --dark: #212529;
            --background: #f8f9fa;
            --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--background);
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* الشريط العلوي */
        .topbar {
            background-color: white;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info .avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-left: 10px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .logout-btn i {
            margin-left: 5px;
        }

        /* القائمة */
        .nav {
            display: flex;
            background: white;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            animation: slideDown 0.6s ease;
        }

        .nav a {
            padding: 1.2rem 1.8rem;
            text-decoration: none;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .nav a:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            z-index: -1;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .nav a:hover:before, .nav a.active:before {
            transform: translateY(0);
        }

        .nav a:hover, .nav a.active {
            color: white;
        }

        .nav a i {
            margin-left: 8px;
            font-size: 1.2rem;
        }
                
        .profile-container {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #E8F5E9;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-left: 20px;
            font-size: 2rem;
            font-weight: bold;
        }

        .profile-info h2 {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .detail-card {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .detail-card h3 {
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        .detail-item {
            margin-bottom: 0.8rem;
            display: flex;
        }

        .detail-label {
            font-weight: 600;
            min-width: 120px;
            color: #555;
        }

        .detail-value {
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--success);
        }

        .status-inactive {
            background-color: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .status-pending {
            background-color: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .subscription-type {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background-color: #E3F2FD;
            color: #1976D2;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .btn i {
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- الشريط العلوي -->
        <div class="topbar">
            <div class="user-info">
                <div class="avatar"><?php echo substr($_SESSION["username"], 0, 1); ?></div>
                <span><?php echo $_SESSION["username"]; ?></span>
            </div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </div>

        <!-- القائمة -->
        <div class="nav">
            <a href="index.php"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="subscribers.php"><i class="fas fa-users"></i> إدارة المشتركين</a>
        </div>

        <!-- تفاصيل المشترك -->
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar"><?php echo substr($subscriber['full_name'], 0, 1); ?></div>
                <div class="profile-info">
                    <h2><?php echo $subscriber['full_name']; ?></h2>
                    <p><?php echo $subscriber['email']; ?></p>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="detail-card">
                    <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
                    <div class="detail-item">
                        <span class="detail-label">البريد الإلكتروني:</span>
                        <span class="detail-value"><?php echo $subscriber['email']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">رقم الهاتف:</span>
                        <span class="detail-value"><?php echo $subscriber['phone'] ? $subscriber['phone'] : 'غير محدد'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">العنوان:</span>
                        <span class="detail-value"><?php echo $subscriber['address']; ?></span>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3><i class="fas fa-calendar-check"></i> معلومات الاشتراك</h3>
                    <div class="detail-item">
                        <span class="detail-label">نوع الاشتراك:</span>
                        <span class="detail-value"><span class="subscription-type"><?php echo $subscriber['subscription_type']; ?></span></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">حالة الاشتراك:</span>
                        <span class="detail-value">
                            <?php 
                            $status_class = '';
                            if($subscriber['subscription_status'] == 'نشط') {
                                $status_class = 'status-active';
                            } elseif($subscriber['subscription_status'] == 'موقف') {
                                $status_class = 'status-inactive';
                            } else {
                                $status_class = 'status-pending';
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $subscriber['subscription_status']; ?></span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">تاريخ البدء:</span>
                        <span class="detail-value"><?php echo $subscriber['start_date']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">طريقة الدفع:</span>
                        <span class="detail-value"><?php echo $subscriber['payment_method']; ?></span>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3><i class="fas fa-box"></i> تفضيلات الصندوق</h3>
                    <div class="detail-item">
                        <span class="detail-value"><?php echo $subscriber['box_preferences'] ? $subscriber['box_preferences'] : 'لا توجد تفضيلات محددة'; ?></span>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3><i class="fas fa-sticky-note"></i> ملاحظات</h3>
                    <div class="detail-item">
                        <span class="detail-value"><?php echo $subscriber['notes'] ? $subscriber['notes'] : 'لا توجد ملاحظات'; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="edit_subscriber.php?id=<?php echo $subscriber['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> تعديل البيانات</a>
                <a href="subscribers.php" class="btn btn-back"><i class="fas fa-arrow-right"></i> العودة إلى القائمة</a>
            </div>
        </div>
    </div>
</body>
</html>