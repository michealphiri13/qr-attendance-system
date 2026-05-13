<?php
require_once 'config.php';

// First, remove any existing admin to avoid duplicate
mysqli_query($conn, "DELETE FROM users WHERE employee_id = 'ADMIN001' OR email = 'admin@qrattendance.com'");

// Create fresh admin user
$fullname = "System Administrator";
$email = "admin@qrattendance.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";
$employee_id = "ADMIN001";
$department = "Management";
$position = "System Administrator";

$sql = "INSERT INTO users (fullname, email, password, role, employee_id, department, position, hourly_rate) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssss", $fullname, $email, $password, $role, $employee_id, $department, $position);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Admin user created successfully!<br><br>";
    echo "📧 Email: <strong>admin@qrattendance.com</strong><br>";
    echo "🔑 Password: <strong>admin123</strong><br><br>";
    echo "<a href='login.php' style='background: #0f3460; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>Go to Login Page →</a>";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>