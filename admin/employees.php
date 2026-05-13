<?php
require_once '../config.php';
requireAdmin();

// Handle employee deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'employee'");
    header('Location: employees.php');
    exit();
}

$employees = mysqli_query($conn, "SELECT * FROM users WHERE role = 'employee' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees | Admin Panel</title>
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
        .user-menu { display: flex; align-items: center; gap: 20px; background: white; padding: 10px 20px; border-radius: 50px; }
        .user-menu a { color: #dc2626; text-decoration: none; }
        .card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .card-header h2 { font-size: 20px; font-weight: 600; color: #1a1a2e; }
        .btn-add { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 10px 20px; border: none; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #1a1a2e; }
        .delete-btn { color: #dc2626; text-decoration: none; }
        .view-link { color: #667eea; text-decoration: none; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-qrcode"></i> QR Attendance</h2></div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="employees.php" class="nav-item active"><i class="fas fa-users"></i><span>Employees</span></a>
            <a href="attendance.php" class="nav-item"><i class="fas fa-calendar-check"></i><span>Attendance</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Reports</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Employees</h1><p>Manage your workforce</p></div>
            <div class="user-menu"><span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['fullname']; ?></span><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> Employee Directory</h2>
                <a href="../register.php" class="btn-add"><i class="fas fa-plus"></i> Add New Employee</a>
            </div>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Position</th><th>Rate</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($emp = mysqli_fetch_assoc($employees)): ?>
                    <tr>
                        <td><?php echo $emp['employee_id']; ?></td>
                        <td><?php echo htmlspecialchars($emp['fullname']); ?></td>
                        <td><?php echo $emp['email']; ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td><?php echo htmlspecialchars($emp['position']); ?></td>
                        <td>K <?php echo number_format($emp['hourly_rate'], 2); ?></td>
                        <td>
                            <a href="view-attendance.php?id=<?php echo $emp['id']; ?>" class="view-link"><i class="fas fa-eye"></i> View</a> | 
                            <a href="?delete=<?php echo $emp['id']; ?>" class="delete-btn" onclick="return confirm('Delete this employee?')"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>