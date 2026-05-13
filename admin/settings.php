<?php
require_once '../config.php';
requireAdmin();

$message = '';
$error = '';

// Create settings table if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $user_id = $_SESSION['user_id'];
    $user_query = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
    $user_data = mysqli_fetch_assoc($user_query);
    
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE id = $user_id");
            $message = "✅ Password changed successfully!";
        } else {
            $error = "❌ New passwords do not match!";
        }
    } else {
        $error = "❌ Current password is incorrect!";
    }
}

// Handle Company Settings
if (isset($_POST['save_company'])) {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $company_email = mysqli_real_escape_string($conn, $_POST['company_email']);
    $company_phone = mysqli_real_escape_string($conn, $_POST['company_phone']);
    $company_address = mysqli_real_escape_string($conn, $_POST['company_address']);
    
    mysqli_query($conn, "DELETE FROM settings WHERE setting_key = 'company_name'");
    mysqli_query($conn, "DELETE FROM settings WHERE setting_key = 'company_email'");
    mysqli_query($conn, "DELETE FROM settings WHERE setting_key = 'company_phone'");
    mysqli_query($conn, "DELETE FROM settings WHERE setting_key = 'company_address'");
    
    mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('company_name', '$company_name')");
    mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('company_email', '$company_email')");
    mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('company_phone', '$company_phone')");
    mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('company_address', '$company_address')");
    
    $message = "✅ Company settings saved successfully!";
}

// Handle Dark Mode
if (isset($_POST['dark_mode'])) {
    $dark_mode = $_POST['dark_mode'];
    mysqli_query($conn, "DELETE FROM settings WHERE setting_key = 'dark_mode'");
    mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('dark_mode', '$dark_mode')");
    $message = "✅ Theme preference saved!";
}

// Get current settings
$company_name = 'QR Attendance System';
$company_email = 'admin@qrattendance.com';
$company_phone = '+260 XXX XXX XXX';
$company_address = 'Lusaka, Zambia';
$dark_mode = '0';

$result = mysqli_query($conn, "SELECT * FROM settings");
while($row = mysqli_fetch_assoc($result)) {
    if ($row['setting_key'] == 'company_name') $company_name = $row['setting_value'];
    if ($row['setting_key'] == 'company_email') $company_email = $row['setting_value'];
    if ($row['setting_key'] == 'company_phone') $company_phone = $row['setting_value'];
    if ($row['setting_key'] == 'company_address') $company_address = $row['setting_value'];
    if ($row['setting_key'] == 'dark_mode') $dark_mode = $row['setting_value'];
}

// Apply dark mode
if ($dark_mode == '1') {
    echo '<style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important; }
        .card, .stat-card, .user-menu { background: #2d2d3f !important; color: white !important; }
        .page-title h1, .page-title p, .card h2, .card p, label { color: white !important; }
        .form-group input, .form-group select, .form-group textarea { background: #3d3d4f; color: white; border-color: #4d4d5f; }
        table, th, td { background: #2d2d3f; color: white; }
        th { background: #3d3d4f; }
    </style>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; transition: all 0.3s ease; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100%; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%); color: white; z-index: 1000; overflow-y: auto; }
        .sidebar-header { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .sidebar-header h2 i { color: #667eea; }
        .sidebar-nav { padding: 20px 0; }
        .nav-item { padding: 12px 25px; margin: 5px 0; display: flex; align-items: center; gap: 15px; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(102,126,234,0.1); color: white; border-left-color: #667eea; }
        .nav-item i { width: 24px; }
        .main-content { margin-left: 280px; padding: 30px 40px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .page-title h1 { font-size: 28px; font-weight: 700; color: #1a1a2e; }
        .page-title p { color: #666; margin-top: 5px; }
        .user-menu { display: flex; align-items: center; gap: 20px; background: white; padding: 10px 20px; border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-menu a { color: #dc2626; text-decoration: none; }
        .settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; }
        .card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
        .card h2 { font-size: 20px; font-weight: 600; color: #1a1a2e; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .card p { color: #666; font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #1a1a2e; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #667eea; }
        button { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 24px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; width: 100%; }
        button:hover { opacity: 0.9; }
        .message { background: #d4edda; color: #155724; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
        .toggle-switch { position: relative; display: inline-block; width: 60px; height: 30px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: 0.3s; border-radius: 30px; }
        .toggle-slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 4px; bottom: 4px; background-color: white; transition: 0.3s; border-radius: 50%; }
        input:checked + .toggle-slider { background: linear-gradient(135deg, #667eea, #764ba2); }
        input:checked + .toggle-slider:before { transform: translateX(30px); }
        .theme-row { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); position: absolute; } .main-content { margin-left: 0; } .settings-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-qrcode"></i> QR Attendance</h2></div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="employees.php" class="nav-item"><i class="fas fa-users"></i><span>Employees</span></a>
            <a href="attendance.php" class="nav-item"><i class="fas fa-calendar-check"></i><span>Attendance</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Reports</span></a>
            <a href="settings.php" class="nav-item active"><i class="fas fa-cog"></i><span>Settings</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>System Settings</h1><p>Configure your organization</p></div>
            <div class="user-menu"><span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['fullname']; ?></span><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
        </div>

        <?php if($message): ?>
            <div class="message"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="settings-grid">
            <!-- Company Settings -->
            <div class="card">
                <h2><i class="fas fa-building"></i> Company Information</h2>
                <p>Update your organization details</p>
                <form method="POST">
                    <div class="form-group">
                        <label>🏢 Company Name</label>
                        <input type="text" name="company_name" value="<?php echo htmlspecialchars($company_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>📧 Company Email</label>
                        <input type="email" name="company_email" value="<?php echo htmlspecialchars($company_email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>📞 Company Phone</label>
                        <input type="text" name="company_phone" value="<?php echo htmlspecialchars($company_phone); ?>">
                    </div>
                    <div class="form-group">
                        <label>📍 Company Address</label>
                        <textarea name="company_address" rows="2"><?php echo htmlspecialchars($company_address); ?></textarea>
                    </div>
                    <button type="submit" name="save_company"><i class="fas fa-save"></i> Save Settings</button>
                </form>
            </div>

            <!-- Theme Settings -->
            <div class="card">
                <h2><i class="fas fa-palette"></i> Appearance</h2>
                <p>Customize your dashboard</p>
                <form method="POST">
                    <div class="form-group">
                        <label>🌓 Dark Mode</label>
                        <div class="theme-row">
                            <span><i class="fas fa-sun"></i> Light Mode</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="dark_mode" value="1" onchange="this.form.submit()" <?php echo $dark_mode == '1' ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                            <span><i class="fas fa-moon"></i> Dark Mode</span>
                        </div>
                    </div>
                </form>
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                    <h3 style="font-size: 14px; margin-bottom: 10px;"><i class="fas fa-shield-alt"></i> Security Status</h3>
                    <p><i class="fas fa-check-circle" style="color: #27ae60;"></i> Session Active</p>
                    <p><i class="fas fa-database"></i> Database Connected</p>
                    <p><i class="fas fa-user-shield"></i> Admin Protected</p>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card">
                <h2><i class="fas fa-key"></i> Change Password</h2>
                <p>Update your account password</p>
                <form method="POST">
                    <div class="form-group">
                        <label>🔒 Current Password</label>
                        <input type="password" name="current_password" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label>🆕 New Password</label>
                        <input type="password" name="new_password" required placeholder="Enter new password">
                    </div>
                    <div class="form-group">
                        <label>✓ Confirm Password</label>
                        <input type="password" name="confirm_password" required placeholder="Confirm new password">
                    </div>
                    <button type="submit" name="change_password"><i class="fas fa-sync-alt"></i> Update Password</button>
                </form>
            </div>

            <!-- Database Info -->
            <div class="card">
                <h2><i class="fas fa-database"></i> Database Status</h2>
                <p>System information</p>
                <div class="form-group">
                    <label>📊 Total Employees</label>
                    <input type="text" value="<?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='employee'"))['count']; ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label>📋 Total Attendance Records</label>
                    <input type="text" value="<?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance"))['count']; ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label>📅 Last Activity</label>
                    <input type="text" value="<?php echo date('F j, Y g:i A'); ?>" readonly disabled>
                </div>
            </div>
        </div>
    </div>
</body>
</html>