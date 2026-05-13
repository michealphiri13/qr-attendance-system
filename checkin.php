<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

$check_sql = "SELECT id FROM attendance WHERE user_id = $user_id AND date = '$today'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    $status = (date('H') >= 9) ? 'late' : 'present';
    $sql = "INSERT INTO attendance (user_id, check_in_time, date, status) VALUES ('$user_id', '$now', '$today', '$status')";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "✅ Check-in recorded at " . date('h:i A');
} else {
    $_SESSION['message'] = "⚠️ You already checked in today";
}

header('Location: dashboard.php');
exit();
?>