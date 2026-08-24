<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Count active medications
$active_query = "SELECT COUNT(*) as count FROM medications WHERE user_id = $user_id AND status = 'active'";
$active_result = mysqli_query($conn, $active_query);
$active_count = mysqli_fetch_assoc($active_result)['count'];

// Calculate doses today (based on frequency)
$doses_today = 0;
$meds_query = "SELECT frequency FROM medications WHERE user_id = $user_id AND status = 'active'";
$meds_result = mysqli_query($conn, $meds_query);
while ($med = mysqli_fetch_assoc($meds_result)) {
    $freq = $med['frequency'];
    if (strpos($freq, 'Once') !== false) $doses_today += 1;
    elseif (strpos($freq, 'Twice') !== false) $doses_today += 2;
    elseif (strpos($freq, 'Three') !== false) $doses_today += 3;
    elseif (strpos($freq, 'Four') !== false) $doses_today += 4;
    else $doses_today += 1; // Default
}

// Next dose calculation (simplified)
$next_dose = "2h 30m";

// Adherence rate (simplified - would need actual tracking)
$adherence = 95;

echo json_encode([
    'success' => true,
    'stats' => [
        'active_count' => (int)$active_count,
        'doses_today' => (int)$doses_today,
        'next_dose' => $next_dose,
        'adherence' => (int)$adherence
    ]
]);

mysqli_close($conn);
?>
