<?php
require_once '../config.php';
requireAdmin();

// Get statistics
$total_employees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='employee'"))['count'];
$total_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE date = CURDATE()"))['count'];
$total_hours_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_hours) as total FROM attendance WHERE MONTH(date) = MONTH(CURDATE())"))['total'] ?? 0;

// Get recent check-ins
$recent_checkins = mysqli_query($conn, "SELECT a.*, u.fullname, u.employee_id FROM attendance a JOIN users u ON a.user_id = u.id ORDER BY a.check_in_time DESC LIMIT 5");

$employees = mysqli_query($conn, "SELECT * FROM users WHERE role='employee' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | QR Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100%; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%); color: white; z-index: 1000; }
        .sidebar-header { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .sidebar-header h2 i { color: #667eea; }
        .sidebar-nav { padding: 20px 0; }
        .nav-item { padding: 12px 25px; margin: 5px 0; display: flex; align-items: center; gap: 15px; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(102,126,234,0.1); color: white; border-left-color: #667eea; }
        .nav-item i { width: 24px; }
        .main-content { margin-left: 280px; padding: 30px 40px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title h1 { font-size: 28px; font-weight: 700; color: #1a1a2e; }
        .page-title p { color: #666; margin-top: 5px; }
        .user-menu { display: flex; align-items: center; gap: 20px; background: white; padding: 10px 20px; border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-menu a { color: #dc2626; text-decoration: none; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .stat-icon { width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; }
        .stat-value { font-size: 36px; font-weight: 800; color: #1a1a2e; margin-bottom: 8px; }
        .stat-label { color: #666; font-size: 14px; }
        .card { background: white; border-radius: 20px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .card-header h2 { font-size: 20px; font-weight: 600; color: #1a1a2e; }
        .btn-export { background: linear-gradient(135deg, #27ae60, #219a52); color: white; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-export:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(39,174,96,0.3); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #1a1a2e; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        .view-link { color: #667eea; text-decoration: none; font-weight: 500; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-qrcode"></i> QR Attendance</h2></div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="employees.php" class="nav-item"><i class="fas fa-users"></i><span>Employees</span></a>
            <a href="attendance.php" class="nav-item"><i class="fas fa-calendar-check"></i><span>Attendance</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Reports</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Dashboard</h1><p>Welcome back, <?php echo $_SESSION['fullname']; ?> 👋</p></div>
            <div class="user-menu"><span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['fullname']; ?></span><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-header"><div class="stat-icon"><i class="fas fa-users"></i></div></div><div class="stat-value"><?php echo $total_employees; ?></div><div class="stat-label">Total Employees</div></div>
            <div class="stat-card"><div class="stat-header"><div class="stat-icon"><i class="fas fa-clock"></i></div></div><div class="stat-value"><?php echo $total_today; ?></div><div class="stat-label">Checked In Today</div></div>
            <div class="stat-card"><div class="stat-header"><div class="stat-icon"><i class="fas fa-hourglass-half"></i></div></div><div class="stat-value"><?php echo number_format($total_hours_month, 1); ?></div><div class="stat-label">Total Hours (Month)</div></div>
        </div>

        <div class="card">
            <div class="card-header"><h2><i class="fas fa-history"></i> Recent Check-ins</h2></div>
            <table>
                <thead><tr><th>Employee</th><th>Employee ID</th><th>Check-in Time</th><th>Check-out Time</th><th>Hours</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_checkins)): ?>
                    <tr><td><?php echo htmlspecialchars($row['fullname']); ?></td><td><?php echo $row['employee_id']; ?></td><td><?php echo date('h:i A', strtotime($row['check_in_time'])); ?></td><td><?php echo $row['check_out_time'] ? date('h:i A', strtotime($row['check_out_time'])) : '--'; ?></td><td><?php echo $row['total_hours'] ?? '--'; ?></td><td><span class="badge badge-<?php echo $row['status'] == 'present' ? 'success' : 'warning'; ?>"><?php echo ucfirst($row['status']); ?></span></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-header"><h2><i class="fas fa-list"></i> Employee Directory</h2><a href="export-payroll.php" class="btn-export"><i class="fas fa-download"></i> Export Payroll</a></div>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Position</th><th>Rate</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($emp = mysqli_fetch_assoc($employees)): ?>
                    <tr><td><?php echo $emp['employee_id']; ?></td><td><?php echo htmlspecialchars($emp['fullname']); ?></td><td><?php echo $emp['email']; ?></td><td><?php echo htmlspecialchars($emp['department']); ?></td><td><?php echo htmlspecialchars($emp['position']); ?></td><td>K <?php echo number_format($emp['hourly_rate'], 2); ?></td><td><a href="view-attendance.php?id=<?php echo $emp['id']; ?>" class="view-link"><i class="fas fa-eye"></i> View</a></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>