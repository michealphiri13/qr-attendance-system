<?php
require_once '../config.php';
requireAdmin();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="payroll_' . date('Y-m') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Employee ID', 'Name', 'Department', 'Position', 'Total Hours', 'Hourly Rate', 'Salary (K)']);

$sql = "SELECT u.employee_id, u.fullname, u.department, u.position, u.hourly_rate, COALESCE(SUM(a.total_hours), 0) as total_hours 
        FROM users u 
        LEFT JOIN attendance a ON u.id = a.user_id AND MONTH(a.date) = MONTH(CURDATE())
        WHERE u.role = 'employee'
        GROUP BY u.id";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    $salary = $row['total_hours'] * $row['hourly_rate'];
    fputcsv($output, [
        $row['employee_id'],
        $row['fullname'],
        $row['department'],
        $row['position'],
        number_format($row['total_hours'], 2),
        number_format($row['hourly_rate'], 2),
        number_format($salary, 2)
    ]);
}
fclose($output);
exit();
?>