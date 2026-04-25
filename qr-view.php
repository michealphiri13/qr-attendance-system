<?php
include 'config.php';
include 'navbar.php';
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id = $event_id"));
if (!$event) die("Event not found");
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code - <?php echo htmlspecialchars($event['event_name']); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; margin: 0; }
        .container { background: white; border-radius: 30px; padding: 40px; text-align: center; max-width: 500px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        .event-info { color: #666; margin-bottom: 30px; }
        .qr-box { background: white; padding: 20px; border-radius: 20px; display: inline-block; margin: 20px 0; }
        img { max-width: 250px; height: auto; }
        .instructions { background: #e8f4fd; padding: 15px; border-radius: 15px; margin: 20px 0; text-align: left; }
        .btn { display: inline-block; background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px; margin: 5px; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📷 Event QR Code</h1>
        <div class="event-info"><strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br><?php echo $event['event_date']; ?> at <?php echo $event['event_time']; ?><br>📍 <?php echo htmlspecialchars($event['venue']); ?></div>
        <div class="qr-box"><?php if(!empty($event['qr_code']) && file_exists($event['qr_code'])): ?><img src="<?php echo $event['qr_code']; ?>" alt="QR Code"><?php else: ?><p>QR code not available</p><?php endif; ?></div>
        <div class="instructions"><strong>📱 How to check in:</strong><br>1. Open phone camera or QR scanner<br>2. Scan this QR code<br>3. Attendee is checked in automatically</div>
        <a href="index.php" class="btn">← Back</a>
        <a href="scan.php" class="btn">📷 Scanner</a>
    </div>
</body>
</html>