<?php
session_start();
include 'config.php';
include 'navbar.php';

$total_events = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM events"))['count'];
$total_attendees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM attendees"))['count'];
$recent_checkins = mysqli_query($conn, "SELECT a.*, e.event_name FROM attendees a JOIN events e ON a.event_id = e.id ORDER BY a.scanned_at DESC LIMIT 5");
$events = mysqli_query($conn, "SELECT * FROM events ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; text-align: center; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 48px; margin-bottom: 15px; }
        .stat-number { font-size: 42px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .action-buttons { display: flex; gap: 20px; justify-content: center; margin-bottom: 40px; flex-wrap: wrap; }
        .action-btn { display: inline-flex; align-items: center; gap: 12px; padding: 15px 35px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 18px; transition: all 0.3s ease; }
        .action-btn-primary { background: linear-gradient(135deg, #3498db, #2980b9); color: white; }
        .action-btn-success { background: linear-gradient(135deg, #27ae60, #219a52); color: white; }
        .action-btn:hover { transform: translateY(-3px); }
        .section-title { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; color: white; }
        .section-title h2 { font-size: 28px; }
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 25px; }
        .event-card { background: white; border-radius: 20px; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .event-card:hover { transform: translateY(-5px); }
        .event-badge { background: linear-gradient(135deg, #667eea, #764ba2); padding: 12px 20px; color: white; font-size: 12px; font-weight: bold; }
        .event-content { padding: 25px; }
        .event-title { font-size: 22px; color: #2c3e50; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .event-info { color: #666; line-height: 1.8; margin-bottom: 20px; }
        .event-info p { margin: 8px 0; display: flex; align-items: center; gap: 10px; }
        .event-actions { display: flex; gap: 12px; margin-top: 20px; }
        .event-btn { flex: 1; text-align: center; padding: 10px; border-radius: 10px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .event-btn-view { background: #3498db; color: white; }
        .event-btn-attendance { background: #27ae60; color: white; }
        .event-btn:hover { opacity: 0.85; }
        .activity-section { background: white; border-radius: 20px; padding: 25px; margin-top: 40px; }
        .activity-section h3 { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th { background: #f8f9fa; padding: 15px; text-align: left; color: #2c3e50; }
        .activity-table td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .activity-table tr:hover { background: #f8f9fa; }
        .no-events { background: white; border-radius: 20px; padding: 60px; text-align: center; color: #999; }
        @media (max-width: 768px) { .action-btn { padding: 10px 20px; font-size: 14px; } .event-title { font-size: 18px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon">🎯</div><div class="stat-number"><?php echo $total_events; ?></div><div class="stat-label">Events</div></div>
            <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-number"><?php echo $total_attendees; ?></div><div class="stat-label">Check-ins</div></div>
            <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-number"><?php echo date('M d, Y'); ?></div><div class="stat-label">Today</div></div>
        </div>
        
        <div class="action-buttons">
            <a href="create-event.php" class="action-btn action-btn-primary"><span>➕</span> New Event</a>
            <a href="scan.php" class="action-btn action-btn-success"><span>📷</span> Scan QR</a>
        </div>
        
        <div class="section-title"><span style="font-size:32px;">📋</span><h2>Your Events</h2></div>
        
        <?php if(mysqli_num_rows($events) > 0): ?>
            <div class="events-grid">
                <?php while($event = mysqli_fetch_assoc($events)): ?>
                    <div class="event-card">
                        <div class="event-badge"><?php echo strtoupper(date('M d, Y', strtotime($event['event_date']))); ?></div>
                        <div class="event-content">
                            <div class="event-title"><span>📌</span> <?php echo htmlspecialchars($event['event_name']); ?></div>
                            <div class="event-info">
                                <p><span>🗓️</span> <?php echo date('l, F j, Y', strtotime($event['event_date'])); ?></p>
                                <p><span>⏰</span> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                                <p><span>📍</span> <?php echo htmlspecialchars($event['venue']); ?></p>
                            </div>
                            <div class="event-actions">
                                <a href="qr-view.php?id=<?php echo $event['id']; ?>" class="event-btn event-btn-view"><span>📷</span> QR Code</a>
                                <a href="attendance.php?event_id=<?php echo $event['id']; ?>" class="event-btn event-btn-attendance"><span>📊</span> Attendance</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-events"><span style="font-size:64px;">📭</span><h3>No Events Yet</h3><p>Click "New Event" to get started</p></div>
        <?php endif; ?>
        
        <div class="activity-section">
            <h3><span>🕐</span> Recent Activity</h3>
            <table class="activity-table">
                <thead><tr><th>👤 Attendee</th><th>🎯 Event</th><th>⏰ Time</th></tr></thead>
                <tbody>
                    <?php while($checkin = mysqli_fetch_assoc($recent_checkins)): ?>
                        <tr><td><?php echo htmlspecialchars($checkin['name']); ?></td><td><?php echo htmlspecialchars($checkin['event_name']); ?></td><td><?php echo date('M j, g:i A', strtotime($checkin['scanned_at'])); ?></td></tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($recent_checkins) == 0): ?>
                        <tr><td colspan="3" style="text-align:center; padding:40px;">📭 No check-ins yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>