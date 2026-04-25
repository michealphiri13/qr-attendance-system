<?php
// Navigation Bar Component
?>
<style>
    .navbar {
        background: rgba(255,255,255,0.95);
        border-radius: 60px;
        padding: 12px 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: bold;
        text-decoration: none;
        color: #2c3e50;
    }
    .nav-links {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 40px;
        text-decoration: none;
        color: #2c3e50;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-link:hover, .nav-link.active {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            border-radius: 20px;
        }
    }
</style>
<div class="navbar">
    <a href="index.php" class="logo"><span>📱</span><span>QR Attendance</span></a>
    <div class="nav-links">
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><span>🏠</span> Dashboard</a>
        <a href="create-event.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'create-event.php' ? 'active' : ''; ?>"><span>➕</span> Create</a>
        <a href="scan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'scan.php' ? 'active' : ''; ?>"><span>📷</span> Scan</a>
        <a href="attendance.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>"><span>📊</span> Reports</a>
    </div>
</div>