<?php
include "config.php";
checkLogin();
$stats = getSystemStats();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام الاشتراكات الزراعية</title>
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
            animation: slideDown 0.5s ease;
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

        /* قسم الترحيب */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            text-align: center;
            animation: fadeIn 1s ease;
        }

        .welcome-section h2 {
            font-size: 2.2rem;
            margin-bottom: 0.8rem;
        }

        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Flip Cards */
        .flip-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .flip-card {
            perspective: 1000px;
            height: 200px;
        }

        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .flip-card-front, .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: var(--card-shadow);
        }

        .flip-card-front {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .flip-card-back {
            background: white;
            color: var(--dark);
            transform: rotateY(180deg);
            padding: 1.5rem;
        }

        .flip-card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .flip-card-front h3 {
            font-size: 1.5rem;
        }

        .flip-card-back h3 {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
            color: var(--primary);
        }

        .flip-card-back p {
            font-size: 2rem;
            font-weight: 800;
            color: var(--secondary);
        }

        /* أحدث المشتركين */
        .recent-subscribers {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.8rem;
        }

        .recent-subscribers h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #E8F5E9;
        }

        .subscriber-list {
            display: grid;
            gap: 1rem;
        }

        .subscriber-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .subscriber-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .subscriber-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-left: 15px;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .subscriber-info {
            flex: 1;
        }

        .subscriber-name {
            font-weight: 600;
            color: #333;
        }

        .subscriber-date {
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* التجاوب مع الشاشات المختلفة */
        @media (max-width: 768px) {
            .flip-cards {
                grid-template-columns: 1fr;
            }
            
            .nav {
                flex-direction: column;
            }
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
            <a href="index.php" class="active"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="subscribers.php"><i class="fas fa-users"></i> إدارة المشتركين</a>
        </div>

        <!-- قسم الترحيب -->
        <div class="welcome-section">
            <h2>مرحباً <?php echo $_SESSION["username"]; ?>!</h2>
            <p>هنا يمكنك إدارة اشتراكات المزرعة بسهولة وكفاءة.</p>
        </div>
        
        <!-- Flip Cards -->
        <div class="flip-cards">
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="flip-card-icon"><i class="fas fa-users"></i></div>
                        <h3>إجمالي المشتركين</h3>
                    </div>
                    <div class="flip-card-back">
                        <h3>إجمالي المشتركين</h3>
                        <p><?php echo $stats['total_subscribers']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="flip-card-icon"><i class="fas fa-user-check"></i></div>
                        <h3>المشتركين النشطين</h3>
                    </div>
                    <div class="flip-card-back">
                        <h3>المشتركين النشطين</h3>
                        <p><?php echo $stats['active_subscribers']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="flip-card-icon"><i class="fas fa-calendar-check"></i></div>
                        <h3>الاشتراكات الأسبوعية</h3>
                    </div>
                    <div class="flip-card-back">
                        <h3>الاشتراكات الأسبوعية</h3>
                        <p><?php echo isset($stats['subscribers_by_type']['أسبوعي']) ? $stats['subscribers_by_type']['أسبوعي'] : 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- أحدث المشتركين -->
        <div class="recent-subscribers">
            <h3><i class="fas fa-history"></i> أحدث المشتركين المنضمين</h3>
            <div class="subscriber-list">
                <?php if(count($stats['recent_subscribers']) > 0): ?>
                    <?php foreach($stats['recent_subscribers'] as $subscriber): ?>
                        <div class="subscriber-item">
                            <div class="subscriber-avatar"><?php echo substr($subscriber['full_name'], 0, 1); ?></div>
                            <div class="subscriber-info">
                                <div class="subscriber-name"><?php echo $subscriber['full_name']; ?></div>
                                <div class="subscriber-date">تاريخ الاشتراك: <?php echo $subscriber['start_date']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; padding: 2rem;">لا يوجد مشتركين حتى الآن</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>