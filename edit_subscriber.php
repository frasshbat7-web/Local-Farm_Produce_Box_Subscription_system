<?php
include "config.php";
checkLogin();

if(!isset($_GET['id'])) {
    header("Location: subscribers.php");
    exit();
}

$id = $_GET['id'];
$error = '';
$success = '';

// جلب بيانات المشترك
$sql = "SELECT * FROM subscribers WHERE id = $id";
$result = mysqli_query($conn, $sql);
$subscriber = mysqli_fetch_assoc($result);

if(!$subscriber) {
    header("Location: subscribers.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $subscription_type = mysqli_real_escape_string($conn, $_POST['subscription_type']);
    $subscription_status = mysqli_real_escape_string($conn, $_POST['subscription_status']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $box_preferences = mysqli_real_escape_string($conn, $_POST['box_preferences']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    // التحقق من البريد الإلكتروني غير مكرر (استثناء المشترك الحالي)
    $check_email = "SELECT id FROM subscribers WHERE email = '$email' AND id != $id";
    $result = mysqli_query($conn, $check_email);
    
    if(mysqli_num_rows($result) > 0) {
        $error = "البريد الإلكتروني مسجل مسبقاً لمشترك آخر";
    } else {
        $sql = "UPDATE subscribers SET 
                full_name = '$full_name',
                email = '$email',
                phone = '$phone',
                address = '$address',
                subscription_type = '$subscription_type',
                subscription_status = '$subscription_status',
                start_date = '$start_date',
                payment_method = '$payment_method',
                box_preferences = '$box_preferences',
                notes = '$notes'
                WHERE id = $id";
        
        if(mysqli_query($conn, $sql)) {
            $success = "تم تحديث بيانات المشترك بنجاح";
            // تحديث بيانات المشترك بعد التعديل
            $sql = "SELECT * FROM subscribers WHERE id = $id";
            $result = mysqli_query($conn, $sql);
            $subscriber = mysqli_fetch_assoc($result);
        } else {
            $error = "حدث خطأ أثناء التحديث: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المشترك - نظام الاشتراكات الزراعية</title>
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
                  
        .form-container {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .form-title {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #E8F5E9;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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

        <!-- رسائل التنبيه -->
        <?php if(!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- نموذج التعديل -->
        <div class="form-container">
            <h2 class="form-title"><i class="fas fa-user-edit"></i> تعديل بيانات المشترك</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name" class="form-label">الاسم الكامل *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $subscriber['full_name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">البريد الإلكتروني *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $subscriber['email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $subscriber['phone']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">العنوان *</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $subscriber['address']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="subscription_type" class="form-label">نوع الاشتراك *</label>
                    <select class="form-control" id="subscription_type" name="subscription_type" required>
                        <option value="أسبوعي" <?php echo $subscriber['subscription_type'] == 'أسبوعي' ? 'selected' : ''; ?>>أسبوعي</option>
                        <option value="شهري" <?php echo $subscriber['subscription_type'] == 'شهري' ? 'selected' : ''; ?>>شهري</option>
                        <option value="ربع سنوي" <?php echo $subscriber['subscription_type'] == 'ربع سنوي' ? 'selected' : ''; ?>>ربع سنوي</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="subscription_status" class="form-label">حالة الاشتراك *</label>
                    <select class="form-control" id="subscription_status" name="subscription_status" required>
                        <option value="نشط" <?php echo $subscriber['subscription_status'] == 'نشط' ? 'selected' : ''; ?>>نشط</option>
                        <option value="موقف" <?php echo $subscriber['subscription_status'] == 'موقف' ? 'selected' : ''; ?>>موقف</option>
                        <option value="ملغى" <?php echo $subscriber['subscription_status'] == 'ملغى' ? 'selected' : ''; ?>>ملغى</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="start_date" class="form-label">تاريخ البدء *</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $subscriber['start_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_method" class="form-label">طريقة الدفع *</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="بطاقة ائتمان" <?php echo $subscriber['payment_method'] == 'بطاقة ائتمان' ? 'selected' : ''; ?>>بطاقة ائتمان</option>
                        <option value="تحويل بنكي" <?php echo $subscriber['payment_method'] == 'تحويل بنكي' ? 'selected' : ''; ?>>تحويل بنكي</option>
                        <option value="نقدي عند الاستلام" <?php echo $subscriber['payment_method'] == 'نقدي عند الاستلام' ? 'selected' : ''; ?>>نقدي عند الاستلام</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="box_preferences" class="form-label">تفضيلات الصندوق</label>
                    <textarea class="form-control" id="box_preferences" name="box_preferences" rows="2"><?php echo $subscriber['box_preferences']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $subscriber['notes']; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
                <a href="view_subscriber.php?id=<?php echo $subscriber['id']; ?>" class="btn" style="background: #ddd; margin-right: 10px;"><i class="fas fa-eye"></i> عرض البيانات</a>
                <a href="subscribers.php" class="btn" style="background: #ddd; margin-right: 10px;"><i class="fas fa-times"></i> إلغاء</a>
            </form>
        </div>
    </div>
</body>
</html>