<?php
require_once '../config.php';
requireAdmin();

// Backup database
$backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$output = '';

// Get all tables
$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while($row = mysqli_fetch_array($result)) {
    $tables[] = $row[0];
}

foreach($tables as $table) {
    $result = mysqli_query($conn, "SELECT * FROM $table");
    $num_fields = mysqli_num_fields($result);
    
    $output .= "DROP TABLE IF EXISTS $table;\n";
    $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE $table"));
    $output .= $row2[1] . ";\n\n";
    
    while($row = mysqli_fetch_row($result)) {
        $output .= "INSERT INTO $table VALUES(";
        for($j=0; $j<$num_fields; $j++) {
            $row[$j] = addslashes($row[$j]);
            $row[$j] = str_replace("\n", "\\n", $row[$j]);
            if(isset($row[$j])) { $output .= '"'.$row[$j].'"'; }
            else { $output .= 'NULL'; }
            if($j < ($num_fields-1)) { $output .= ','; }
        }
        $output .= ");\n";
    }
    $output .= "\n";
}

// Save file
$backup_path = '../backups/';
if (!file_exists($backup_path)) {
    mkdir($backup_path, 0777, true);
}
file_put_contents($backup_path . $backup_file, $output);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backup_file . '"');
header('Content-Length: ' . filesize($backup_path . $backup_file));
readfile($backup_path . $backup_file);
unlink($backup_path . $backup_file);
exit();
?>