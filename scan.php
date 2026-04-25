<?php
session_start();
include 'config.php';
include 'navbar.php';
$events = mysqli_query($conn, "SELECT id, event_name, event_date FROM events ORDER BY event_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scan QR Code</title>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 30px; padding: 40px; }
        h1 { text-align: center; margin-bottom: 30px; color: #2c3e50; }
        select, input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; margin-bottom: 20px; }
        button { background: linear-gradient(135deg, #27ae60, #219a52); color: white; padding: 12px; border: none; border-radius: 10px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { opacity: 0.9; }
        #reader { width: 100%; margin: 20px 0; }
        .result { padding: 15px; border-radius: 10px; margin: 20px 0; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .manual-section { margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📷 Scan QR Code</h1>
        <label>Select Event</label>
        <select id="event_id">
            <option value="">-- Select Event --</option>
            <?php while($event = mysqli_fetch_assoc($events)): ?>
                <option value="<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['event_name']); ?> - <?php echo $event['event_date']; ?></option>
            <?php endwhile; ?>
        </select>
        <div id="reader"></div>
        <div id="result"></div>
        
        <div class="manual-section">
            <h3>Manual Check-in</h3>
            <input type="text" id="name" placeholder="Full Name">
            <input type="email" id="email" placeholder="Email (optional)">
            <input type="text" id="phone" placeholder="Phone (optional)">
            <button onclick="manualCheckin()">✅ Manual Check-in</button>
        </div>
    </div>
    <script>
        let scanner;
        document.getElementById('event_id').addEventListener('change', function() {
            if(scanner) scanner.stop();
            startScanner();
        });
        function startScanner() {
            let eventId = document.getElementById('event_id').value;
            if(!eventId) return;
            scanner = new Html5Qrcode("reader");
            scanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onSuccess, onError);
        }
        function onSuccess(decodedText) {
            scanner.stop();
            fetch('mark-attendance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_data=' + encodeURIComponent(decodedText) + '&event_id=' + document.getElementById('event_id').value
            }).then(r=>r.json()).then(data=>{
                let div = document.getElementById('result');
                div.innerHTML = data.message;
                div.className = 'result ' + (data.success ? 'success' : 'error');
                if(data.success) setTimeout(()=> startScanner(), 3000);
            });
        }
        function onError(err) { console.log(err); }
        function manualCheckin() {
            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let phone = document.getElementById('phone').value;
            let eventId = document.getElementById('event_id').value;
            if(!name || !eventId) { alert('Name and Event required'); return; }
            fetch('mark-attendance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&phone=' + encodeURIComponent(phone) + '&event_id=' + eventId
            }).then(r=>r.json()).then(data=>{
                let div = document.getElementById('result');
                div.innerHTML = data.message;
                div.className = 'result ' + (data.success ? 'success' : 'error');
                if(data.success) { document.getElementById('name').value = ''; document.getElementById('email').value = ''; document.getElementById('phone').value = ''; }
            });
        }
    </script>
</body>
</html>