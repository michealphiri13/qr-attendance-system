<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $employee_id = 'EMP' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $hourly_rate = floatval($_POST['hourly_rate']);
    
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email already registered";
    } else {
        $sql = "INSERT INTO users (fullname, email, password, employee_id, department, position, hourly_rate, role) 
                VALUES ('$fullname', '$email', '$password', '$employee_id', '$department', '$position', '$hourly_rate', 'employee')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | QR Attendance System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-icon {
            font-size: 48px;
            margin-bottom: 8px;
        }
        
        .logo h1 {
            font-size: 26px;
            color: #1a1a2e;
            font-weight: 700;
        }
        
        .logo p {
            color: #6c757d;
            font-size: 14px;
            margin-top: 6px;
        }
        
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #991b1b;
            font-size: 14px;
        }
        
        .success-message {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #166534;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a2e;
            font-size: 13px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0f3460;
            box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
        }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f3460, #16213e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 52, 96, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-link a {
            color: #0f3460;
            text-decoration: none;
            font-weight: 600;
        }
        
        @media (max-width: 560px) {
            .register-container {
                padding: 28px 20px;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <div class="logo-icon">📝</div>
            <h1>Create Account</h1>
            <p>Join the QR Attendance System</p>
        </div>
        
        <?php if($error): ?>
            <div class="error-message">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success-message">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" required placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" required placeholder="e.g., IT, HR, Sales">
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="position" required placeholder="Your job title">
                </div>
            </div>
            <div class="form-group">
                <label>Hourly Rate (Kwacha)</label>
                <input type="number" step="0.01" name="hourly_rate" required placeholder="e.g., 50.00">
            </div>
            <button type="submit" class="btn-register">Create Account</button>
        </form>
        
        <div class="login-link">
            <span style="color: #6c757d;">Already have an account?</span>
            <a href="login.php">Sign In</a>
        </div>
    </div>
</body>
</html>