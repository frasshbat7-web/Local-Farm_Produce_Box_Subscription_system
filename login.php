<?php
include "config.php";

$error = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if(login($username, $password)){
        header("location: index.php");
        exit();
    } else {
        $error = "اسم المستخدم أو كلمة المرور غير صحيحة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام زراعي للاشتراكات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000000;
            --primary-dark: #333333;
            --secondary: #4CAF50;
            --background: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-sphere {
            width: 400px;
            height: 400px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: float 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .login-sphere:before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            top: -40px;
            left: -40px;
        }

        .login-sphere:after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
            bottom: -30px;
            right: -30px;
        }

        .login-container {
            text-align: center;
            padding: 2.5rem;
            z-index: 2;
            width: 80%;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .login-logo i {
            font-size: 2rem;
            color: white;
        }

        h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: right;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .input-icon .form-control {
            padding-left: 3rem;
        }

        .btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .error-message {
            background-color: rgba(244, 67, 54, 0.1);
            color: #F44336;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

        .login-info {
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #E8F5E9;
            border-radius: 15px;
            font-size: 0.9rem;
            color: #2E7D32;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 500px) {
            .login-sphere {
                width: 350px;
                height: 350px;
                border-radius: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-sphere">
        <div class="login-container">
            <div class="login-logo">
                <i class="fas fa-seedling"></i>
            </div>
            
            <h2>تسجيل الدخول</h2>
            <h2>نظام الاشتراكات الزراعية</h2>
            
            <?php if(!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</button>
            </form>
            
            <div class="login-info">
                <p><strong>بيانات الدخول الافتراضية:</strong></p>
                <p>اسم المستخدم: <strong>admin</strong></p>
                <p>كلمة المرور: <strong>admin123</strong></p>
            </div>
        </div>
    </div>
</body>
</html>