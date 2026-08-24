<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get active medications and create reminders
$query = "SELECT medication_name, dosage, frequency FROM medications 
          WHERE user_id = $user_id AND status = 'active'";
$result = mysqli_query($conn, $query);

$reminders = [];
while ($med = mysqli_fetch_assoc($result)) {
    $freq = $med['frequency'];
    
    // Generate times based on frequency
    if (strpos($freq, 'Once') !== false) {
        $reminders[] = [
            'time' => '09:00',
            'medication' => $med['medication_name'],
            'dosage' => $med['dosage']
        ];
    } elseif (strpos($freq, 'Twice') !== false) {
        $reminders[] = [
            'time' => '09:00',
            'medication' => $med['medication_name'],
            'dosage' => $med['dosage']
        ];
        $reminders[] = [
            'time' => '21:00',
            'medication' => $med['medication_name'],
            'dosage' => $med['dosage']
        ];
    } elseif (strpos($freq, 'Three') !== false) {
        $reminders[] = ['time' => '08:00', 'medication' => $med['medication_name'], 'dosage' => $med['dosage']];
        $reminders[] = ['time' => '14:00', 'medication' => $med['medication_name'], 'dosage' => $med['dosage']];
        $reminders[] = ['time' => '20:00', 'medication' => $med['medication_name'], 'dosage' => $med['dosage']];
    }
}

// Sort by time
usort($reminders, function($a, $b) {
    return strcmp($a['time'], $b['time']);
});

echo json_encode([
    'success' => true,
    'reminders' => $reminders
]);

mysqli_close($conn);
?>
