<?php
header('Content-Type: application/json');
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    if (!$event_id) { echo json_encode(['success'=>false,'message'=>'Invalid event']); exit; }
    
    if (isset($_POST['qr_data'])) {
        parse_str($_POST['qr_data'], $qr);
        if (!isset($qr['event_id']) || $qr['event_id'] != $event_id) { echo json_encode(['success'=>false,'message'=>'QR code mismatch']); exit; }
        $name = 'Attendant ' . date('His');
        $check = mysqli_query($conn, "SELECT id FROM attendees WHERE event_id=$event_id AND name='$name'");
        if(mysqli_num_rows($check)>0) { echo json_encode(['success'=>false,'message'=>'Already checked in']); exit; }
        mysqli_query($conn, "INSERT INTO attendees (name, event_id) VALUES ('$name', $event_id)");
        echo json_encode(['success'=>true,'message'=>'✓ Check-in successful!']);
    }
    elseif (isset($_POST['name'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $check = mysqli_query($conn, "SELECT id FROM attendees WHERE event_id=$event_id AND name='$name'");
        if(mysqli_num_rows($check)>0) { echo json_encode(['success'=>false,'message'=>'Already checked in']); exit; }
        mysqli_query($conn, "INSERT INTO attendees (name, email, phone, event_id) VALUES ('$name', '$email', '$phone', $event_id)");
        echo json_encode(['success'=>true,'message'=>'✓ Check-in successful!']);
    }
    else { echo json_encode(['success'=>false,'message'=>'Invalid request']); }
} else { echo json_encode(['success'=>false,'message'=>'Invalid method']); }
?>