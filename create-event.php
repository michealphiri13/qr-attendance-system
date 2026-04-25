<?php
session_start();
include 'config.php';
include 'navbar.php';
require_once 'vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$success = '';
$error = '';
$qr_code_path = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_description = mysqli_real_escape_string($conn, $_POST['event_description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    
    $sql = "INSERT INTO events (event_name, event_description, event_date, event_time, venue) VALUES ('$event_name', '$event_description', '$event_date', '$event_time', '$venue')";
    
    if (mysqli_query($conn, $sql)) {
        $event_id = mysqli_insert_id($conn);
        $qr_data = "event_id=" . $event_id;
        if (!file_exists('qrcodes')) mkdir('qrcodes', 0777, true);
        $options = new QROptions(['version' => 5, 'outputType' => QRCode::OUTPUT_IMAGE_PNG]);
        $qrCode = (new QRCode($options))->render($qr_data);
        $qr_filename = "qrcodes/qrcode_event_{$event_id}.png";
        file_put_contents($qr_filename, base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $qrCode)));
        mysqli_query($conn, "UPDATE events SET qr_code = '$qr_filename' WHERE id = $event_id");
        $success = "Event created!";
        $qr_code_path = $qr_filename;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 30px; padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; font-size: 16px; }
        button { background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 15px; border: none; border-radius: 10px; font-size: 18px; cursor: pointer; width: 100%; }
        button:hover { opacity: 0.9; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .qr-result { text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 15px; }
        .qr-result img { max-width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Create New Event</h1>
        <?php if($success): ?>
            <div class="success">✅ <?php echo $success; ?></div>
            <div class="qr-result"><h3>QR Code:</h3><img src="<?php echo $qr_code_path; ?>"><p>Scan to check in</p></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Event Name *</label><input type="text" name="event_name" required></div>
            <div class="form-group"><label>Description</label><textarea name="event_description" rows="3"></textarea></div>
            <div class="form-group"><label>Event Date *</label><input type="date" name="event_date" required></div>
            <div class="form-group"><label>Event Time *</label><input type="time" name="event_time" required></div>
            <div class="form-group"><label>Venue *</label><input type="text" name="venue" required></div>
            <button type="submit">✨ Create Event & Generate QR</button>
        </form>
    </div>
</body>
</html>