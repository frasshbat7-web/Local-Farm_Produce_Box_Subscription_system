<?php
include "config.php";
checkLogin();

// معالجة عمليات الحذف
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM subscribers WHERE id = $id";
    if(mysqli_query($conn, $sql)) {
        header("Location: subscribers.php?message=تم حذف المشترك بنجاح");
        exit();
    }
}

// البحث عن المشتركين
$search = "";
if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_condition = "WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR address LIKE '%$search%'";
} else {
    $search_condition = "";
}

// جلب جميع المشتركين مع إمكانية البحث
$sql = "SELECT * FROM subscribers $search_condition ORDER BY start_date DESC, created_at DESC";
$result = mysqli_query($conn, $sql);
$subscribers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشتركين - نظام الاشتراكات الزراعية</title>
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

        /* عنوان الصفحة */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
        }

        .page-title {
            font-size: 1.8rem;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-left: 10px;
            color: var(--primary);
        }

        .add-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .add-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .add-btn i {
            margin-left: 8px;
        }

        /* شريط البحث */
        .search-container {
            background-color: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.8rem;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
        }

        .search-form {
            display: flex;
            width: 100%;
        }

        .search-input {
            flex: 1;
            padding: 1rem 1.2rem;
            border: 2px solid #ddd;
            border-radius: 12px 0 0 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 12px 12px 0;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
        }

        /* جدول المشتركين */
        .subscribers-table-container {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            overflow-x: auto;
        }

        .subscribers-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .subscribers-table th {
            background-color: #E8F5E9;
            padding: 1.2rem;
            text-align: right;
            font-weight: 600;
            color: #2E7D32;
            border-bottom: 2px solid #C8E6C9;
        }

        .subscribers-table td {
            padding: 1.2rem;
            border-bottom: 1px solid #eee;
        }

        .subscribers-table tr:last-child td {
            border-bottom: none;
        }

        .subscribers-table tr:hover {
            background-color: #f8f9fa;
        }

        .status {
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
            display: flex;
            gap: 0.6rem;
        }

        .btn {
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
        }

        .btn-view {
            background-color: rgba(33, 150, 243, 0.15);
            color: var(--info);
        }

        .btn-edit {
            background-color: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .btn-delete {
            background-color: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn i {
            margin-left: 5px;
            font-size: 0.9rem;
        }

        /* رسائل التنبيه */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--success);
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .alert-danger {
            background-color: rgba(244, 67, 54, 0.15);
            color: var(--danger);
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

        .alert i {
            margin-left: 10px;
        }

        /* التجاوب مع الشاشات المختلفة */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-input {
                border-radius: 12px;
                margin-bottom: 0.8rem;
            }
            
            .search-btn {
                border-radius: 12px;
                padding: 0.8rem;
            }
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
            <a href="subscribers.php" class="active"><i class="fas fa-users"></i> إدارة المشتركين</a>
        </div>

        <!-- عنوان الصفحة -->
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-users"></i> إدارة المشتركين</h1>
            <a href="add_subscriber.php" class="add-btn"><i class="fas fa-plus"></i> إضافة مشترك جديد</a>
        </div>

        <!-- رسائل التنبيه -->
        <?php if(isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_GET['message']; ?>
            </div>
        <?php endif; ?>

        <!-- شريط البحث -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="ابحث عن مشترك بالاسم أو البريد الإلكتروني أو العنوان..." value="<?php echo $search; ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> بحث</button>
            </form>
        </div>

        <!-- جدول المشتركين -->
        <div class="subscribers-table-container">
            <table class="subscribers-table">
                <thead>
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>البريد الإلكتروني</th>
                        <th>نوع الاشتراك</th>
                        <th>حالة الاشتراك</th>
                        <th>تاريخ البدء</th>
                        <th>طريقة الدفع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($subscribers) > 0): ?>
                        <?php foreach($subscribers as $subscriber): ?>
                            <tr>
                                <td><?php echo $subscriber['full_name']; ?></td>
                                <td><?php echo $subscriber['email']; ?></td>
                                <td><span class="subscription-type"><?php echo $subscriber['subscription_type']; ?></span></td>
                                <td>
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
                                    <span class="status <?php echo $status_class; ?>"><?php echo $subscriber['subscription_status']; ?></span>
                                </td>
                                <td><?php echo $subscriber['start_date']; ?></td>
                                <td><?php echo $subscriber['payment_method']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_subscriber.php?id=<?php echo $subscriber['id']; ?>" class="btn btn-view"><i class="fas fa-eye"></i> عرض</a>
                                        <a href="edit_subscriber.php?id=<?php echo $subscriber['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> تعديل</a>
                                        <a href="subscribers.php?delete_id=<?php echo $subscriber['id']; ?>" class="btn btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا المشترك؟')"><i class="fas fa-trash"></i> حذف</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                <?php if($search): ?>
                                    لا توجد نتائج بحث تطابق "<?php echo $search; ?>"
                                <?php else: ?>
                                    لا يوجد مشتركين مسجلين حتى الآن
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>