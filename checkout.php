<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

$sql = "SELECT id, check_in_time FROM attendance WHERE user_id = $user_id AND date = '$today' AND check_out_time IS NULL";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $check_in = strtotime($row['check_in_time']);
    $check_out = strtotime($now);
    $hours_worked = round(($check_out - $check_in) / 3600, 2);
    
    $update_sql = "UPDATE attendance SET check_out_time = '$now', total_hours = '$hours_worked' WHERE id = " . $row['id'];
    mysqli_query($conn, $update_sql);
    $_SESSION['message'] = "🏠 Check-out recorded. Hours worked: $hours_worked";
} else {
    $_SESSION['message'] = "⚠️ No check-in found for today";
}

header('Location: dashboard.php');
exit();
?>