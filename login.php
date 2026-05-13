<?php
require_once 'config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['employee_id'] = $user['employee_id'];
            
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | QR Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 20s infinite ease-in-out;
            z-index: 0;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 500px;
            height: 500px;
            bottom: -200px;
            right: -150px;
            animation-delay: 5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 10%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 32px;
            padding: 48px 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
        }

        /* Logo Section */
        .logo {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }

        .logo-icon i {
            font-size: 36px;
            color: white;
        }

        .logo h1 {
            font-size: 28px;
            color: #1a1a2e;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo p {
            color: #6c757d;
            font-size: 14px;
            margin-top: 8px;
        }

        /* Error Message */
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #991b1b;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a2e;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 18px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Forgot Password */
        .forgot-link {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 24px;
        }

        .forgot-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        .forgot-link a:hover {
            color: #667eea;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .register-link span {
            color: #6c757d;
            font-size: 14px;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Demo Credentials */
        .demo-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 24px;
            text-align: center;
        }

        .demo-box p {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .demo-box .creds {
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 12px;
        }

        .demo-box .creds span {
            background: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-family: monospace;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
            }
            .logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <h1>QR Attendance</h1>
            <p>Check-in & Check-out System</p>
        </div>

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
            </div>
            <div class="forgot-link">
                <a href="forgot-password.php"><i class="fas fa-question-circle"></i> Forgot password?</a>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right-to-bracket"></i> Sign In
            </button>
        </form>

        <div class="register-link">
            <span>Don't have an account?</span>
            <a href="register.php">Create Account</a>
        </div>

        <div class="demo-box">
            <p><i class="fas fa-info-circle"></i> Demo Credentials</p>
            <div class="creds">
                <span><i class="fas fa-envelope"></i> admin@qrattendance.com</span>
                <span><i class="fas fa-key"></i> admin123</span>
            </div>
        </div>
    </div>
</body>
</html>