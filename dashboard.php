<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$employee_id = $_SESSION['employee_id'];

$today = date('Y-m-d');
$today_sql = "SELECT * FROM attendance WHERE user_id = $user_id AND date = '$today'";
$today_result = mysqli_query($conn, $today_sql);
$today_attendance = mysqli_fetch_assoc($today_result);

$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$month_sql = "SELECT SUM(total_hours) as total FROM attendance WHERE user_id = $user_id AND date BETWEEN '$month_start' AND '$month_end'";
$month_result = mysqli_fetch_assoc(mysqli_query($conn, $month_sql));
$total_hours = $month_result['total'] ?? 0;

$recent_sql = "SELECT * FROM attendance WHERE user_id = $user_id ORDER BY date DESC LIMIT 10";
$recent_result = mysqli_query($conn, $recent_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard | QR Attendance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        
        /* Top Navbar */
        .navbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .logo { font-size: 22px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
        .logo i { color: #667eea; font-size: 28px; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links span { color: #1a1a2e; font-weight: 500; }
        .nav-links a { color: #dc2626; text-decoration: none; font-weight: 500; }
        
        /* Main Container */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        /* Welcome Card */
        .welcome-card { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 24px; padding: 35px; color: white; margin-bottom: 35px; }
        .welcome-card h1 { font-size: 28px; margin-bottom: 8px; }
        .welcome-card p { opacity: 0.9; }
        
        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; display: flex; align-items: center; gap: 20px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .stat-icon { width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; }
        .stat-info { flex: 1; }
        .stat-value { font-size: 32px; font-weight: 800; color: #1a1a2e; }
        .stat-label { color: #666; font-size: 14px; margin-top: 5px; }
        
        /* Check-in Card */
        .checkin-card { background: white; border-radius: 24px; padding: 35px; text-align: center; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .checkin-card h2 { margin-bottom: 20px; color: #1a1a2e; }
        .qr-box { background: #f8f9fa; padding: 20px; border-radius: 20px; display: inline-block; margin: 15px 0; }
        .btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 35px; border-radius: 50px; text-decoration: none; font-weight: 600; margin: 10px; transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); }
        .btn-checkin { background: linear-gradient(135deg, #27ae60, #219a52); color: white; box-shadow: 0 4px 15px rgba(39,174,96,0.3); }
        .btn-checkout { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; box-shadow: 0 4px 15px rgba(231,76,60,0.3); }
        .btn-disabled { background: #95a5a6; cursor: not-allowed; box-shadow: none; }
        
        /* Table */
        .card { background: white; border-radius: 24px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 20px; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8f9fa; color: #1a1a2e; font-weight: 600; border-radius: 12px; }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; }
        .status-present { color: #27ae60; font-weight: 600; }
        .status-late { color: #e74c3c; font-weight: 600; }
        
        @media (max-width: 768px) {
            .container { padding: 0 15px; }
            .navbar { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fas fa-qrcode"></i> QR Attendance System</div>
        <div class="nav-links">
            <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($fullname); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>👋 Welcome, <?php echo htmlspecialchars($fullname); ?></h1>
            <p>Employee ID: <?php echo htmlspecialchars($employee_id); ?></p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $total_hours; ?></div>
                    <div class="stat-label">Hours This Month</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $today_attendance ? '✅' : '⏰'; ?></div>
                    <div class="stat-label">Today's Status</div>
                </div>
            </div>
        </div>
        
        <div class="checkin-card">
            <h2><i class="fas fa-qrcode"></i> Quick Check-in / Check-out</h2>
            <div class="qr-box">
                <?php
                $qr_data = "user_id={$user_id}&timestamp=" . time();
                if (file_exists('vendor/autoload.php')) {
                    require_once 'vendor/autoload.php';
                    echo '<img src="' . (new chillerlan\QRCode\QRCode)->render($qr_data) . '" width="150" alt="QR Code">';
                } else {
                    echo '<div style="padding: 20px;"><i class="fas fa-qrcode" style="font-size: 80px; color: #667eea;"></i></div>';
                }
                ?>
            </div>
            <p>Scan this QR code to check in or out</p>
            <div>
                <?php if(!$today_attendance || !$today_attendance['check_in_time']): ?>
                    <a href="checkin.php" class="btn btn-checkin"><i class="fas fa-check-circle"></i> Check In</a>
                <?php elseif($today_attendance['check_in_time'] && !$today_attendance['check_out_time']): ?>
                    <a href="checkout.php" class="btn btn-checkout"><i class="fas fa-sign-out-alt"></i> Check Out</a>
                <?php else: ?>
                    <button class="btn btn-disabled" disabled><i class="fas fa-check-double"></i> Completed for Today</button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-history"></i> Recent Attendance</h3>
            <table>
                <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['check_in_time'])); ?></td>
                        <td><?php echo $row['check_out_time'] ? date('h:i A', strtotime($row['check_out_time'])) : '--'; ?></td>
                        <td><?php echo $row['total_hours'] ? number_format($row['total_hours'], 2) : '--'; ?></td>
                        <td class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>