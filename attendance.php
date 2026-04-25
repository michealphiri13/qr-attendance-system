<?php
session_start();
include 'config.php';
include 'navbar.php';
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$events = mysqli_query($conn, "SELECT id, event_name, event_date FROM events ORDER BY event_date DESC");
$event = null;
$attendees = null;
if($event_id > 0){
    $event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id=$event_id"));
    $attendees = mysqli_query($conn, "SELECT * FROM attendees WHERE event_id=$event_id ORDER BY scanned_at DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Reports</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 30px; padding: 40px; }
        h1 { text-align: center; margin-bottom: 30px; color: #2c3e50; }
        select, input { padding: 12px; border: 1px solid #ddd; border-radius: 10px; margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; color: #2c3e50; }
        .event-info { background: #e8f4fd; padding: 15px; border-radius: 15px; margin-bottom: 20px; }
        .btn-export { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-export:hover { opacity: 0.9; }
        .search-box { width: 300px; margin-bottom: 20px; }
        .count { margin-top: 20px; font-weight: bold; color: #2c3e50; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Attendance Reports</h1>
        <form method="GET" style="margin-bottom: 20px;">
            <select name="event_id" onchange="this.form.submit()">
                <option value="">-- Select Event --</option>
                <?php while($e = mysqli_fetch_assoc($events)): ?>
                    <option value="<?php echo $e['id']; ?>" <?php echo $event_id==$e['id']?'selected':''; ?>><?php echo htmlspecialchars($e['event_name']); ?> - <?php echo $e['event_date']; ?></option>
                <?php endwhile; ?>
            </select>
            <?php if($event_id): ?>
                <a href="export-excel.php?event_id=<?php echo $event_id; ?>" class="btn-export">📎 Export to Excel</a>
            <?php endif; ?>
        </form>
        <?php if($event): ?>
            <div class="event-info"><strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br>📅 <?php echo $event['event_date']; ?> at <?php echo $event['event_time']; ?><br>📍 <?php echo htmlspecialchars($event['venue']); ?></div>
            <input type="text" id="search" class="search-box" placeholder="🔍 Search by name, email, phone..." onkeyup="filterTable()">
            <table id="attendanceTable">
                <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Check-in Time</th></tr></thead>
                <tbody>
                    <?php $i=1; while($a = mysqli_fetch_assoc($attendees)): ?>
                        <tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($a['name']); ?></td><td><?php echo htmlspecialchars($a['email']); ?></td><td><?php echo htmlspecialchars($a['phone']); ?></td><td><?php echo $a['scanned_at']; ?></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="count">Total: <?php echo mysqli_num_rows($attendees); ?> attendees</div>
        <?php else: ?>
            <p style="text-align:center; padding:40px;">Select an event to view attendance</p>
        <?php endif; ?>
    </div>
    <script>
        function filterTable() {
            let input = document.getElementById('search').value.toLowerCase();
            let rows = document.querySelectorAll('#attendanceTable tbody tr');
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>