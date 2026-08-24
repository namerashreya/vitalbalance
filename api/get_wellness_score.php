<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Calculate wellness score based on health records
function calculateWellnessScore($conn, $user_id) {
    $score = 100; // Start with perfect score
    
    // Get all health records
    $records_query = "SELECT record_type, value, unit, recorded_at 
                      FROM health_records 
                      WHERE user_id = $user_id 
                      ORDER BY recorded_at DESC 
                      LIMIT 30";
    $records_result = mysqli_query($conn, $records_query);
    
    if (!$records_result || mysqli_num_rows($records_result) == 0) {
        return 75; // Default score if no records
    }
    
    $recent_records = [];
    while ($record = mysqli_fetch_assoc($records_result)) {
        $recent_records[] = $record;
    }
    
    // Analyze blood pressure
    $bp_records = array_filter($recent_records, function($r) {
        return $r['record_type'] == 'blood_pressure';
    });
    if (count($bp_records) > 0) {
        $latest_bp = reset($bp_records);
        $bp_value = $latest_bp['value'];
        // Parse BP (e.g., "120/80")
        if (preg_match('/(\d+)\/(\d+)/', $bp_value, $matches)) {
            $systolic = (int)$matches[1];
            $diastolic = (int)$matches[2];
            
            // Normal: 90-120/60-80
            if ($systolic > 140 || $diastolic > 90) {
                $score -= 15; // High BP
            } elseif ($systolic < 90 || $diastolic < 60) {
                $score -= 10; // Low BP
            } elseif ($systolic > 120 || $diastolic > 80) {
                $score -= 5; // Elevated
            }
        }
    }
    
    // Analyze blood sugar
    $bs_records = array_filter($recent_records, function($r) {
        return $r['record_type'] == 'blood_sugar';
    });
    if (count($bs_records) > 0) {
        $latest_bs = reset($bs_records);
        $bs_value = (float)str_replace(['mg/dL', 'mmol/L'], '', $latest_bs['value']);
        
        // Normal fasting: 70-100 mg/dL
        if ($bs_value > 125) {
            $score -= 20; // High (diabetic range)
        } elseif ($bs_value > 100) {
            $score -= 10; // Elevated
        } elseif ($bs_value < 70) {
            $score -= 15; // Low (hypoglycemia)
        }
    }
    
    // Analyze weight trends
    $weight_records = array_filter($recent_records, function($r) {
        return $r['record_type'] == 'weight';
    });
    if (count($weight_records) >= 2) {
        $weights = array_map(function($r) {
            return (float)str_replace(['kg', 'lbs'], '', $r['value']);
        }, array_slice($weight_records, 0, 5));
        
        if (count($weights) >= 2) {
            $trend = $weights[0] - $weights[count($weights)-1];
            $percent_change = abs($trend / $weights[count($weights)-1]) * 100;
            
            // Significant weight change (more than 5% in short time) is concerning
            if ($percent_change > 5) {
                $score -= 10;
            }
        }
    }
    
    // Check record frequency (more records = better tracking = better score)
    $days_since_first = 0;
    if (count($recent_records) > 0) {
        $first_record = end($recent_records);
        $last_record = reset($recent_records);
        $days_since_first = (strtotime($last_record['recorded_at']) - strtotime($first_record['recorded_at'])) / 86400;
        
        if ($days_since_first > 0) {
            $records_per_week = (count($recent_records) / max($days_since_first, 1)) * 7;
            if ($records_per_week >= 3) {
                $score += 5; // Bonus for regular tracking
            }
        }
    }
    
    // Check active diseases
    $disease_query = "SELECT COUNT(*) as count FROM user_diseases WHERE user_id = $user_id AND status = 'active'";
    $disease_result = mysqli_query($conn, $disease_query);
    if ($disease_result) {
        $active_diseases = mysqli_fetch_assoc($disease_result)['count'];
        $score -= ($active_diseases * 5); // Each active disease reduces score
    }
    
    // Ensure score is between 0 and 100
    return max(0, min(100, round($score)));
}

$wellness_score = calculateWellnessScore($conn, $user_id);

// Get score trend (compare with previous week)
$week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
$old_records_query = "SELECT COUNT(*) as count FROM health_records 
                      WHERE user_id = $user_id AND recorded_at < '$week_ago'";
$old_records_result = mysqli_query($conn, $old_records_query);
$old_count = 0;
if ($old_records_result) {
    $old_count = mysqli_fetch_assoc($old_records_result)['count'];
}

// Get current records count
$current_count_query = "SELECT COUNT(*) as count FROM health_records WHERE user_id = $user_id";
$current_count_result = mysqli_query($conn, $current_count_query);
$current_count = 0;
if ($current_count_result) {
    $current_count = mysqli_fetch_assoc($current_count_result)['count'];
}

$trend = 'stable';
if ($current_count > $old_count) {
    $trend = 'improving';
} elseif ($current_count < $old_count) {
    $trend = 'declining';
}

echo json_encode([
    'success' => true,
    'wellness_score' => $wellness_score,
    'trend' => $trend,
    'records_count' => $current_count
]);

mysqli_close($conn);
?>

