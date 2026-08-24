<?php
// Turn off error display, capture errors instead
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any accidental output
ob_start();

// Set JSON header early
header('Content-Type: application/json');

// Function to send JSON error and exit
function sendError($message) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

// Function to send JSON success
function sendSuccess($message, $data = null) {
    ob_end_clean();
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['disease'] = $data;
    }
    echo json_encode($response);
    exit();
}

try {
    session_start();
    
    // Include database connection
    if (!@include("../includes/db.php")) {
        sendError('Database configuration file not found');
    }
    
    // Check if database connection failed
    if (!isset($conn) || !$conn) {
        sendError('Database connection failed');
    }
    
    if (!isset($_SESSION['user_id'])) {
        sendError('Unauthorized - Please login');
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Invalid request method');
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $disease_id = (int)($_POST['disease_id'] ?? 0);
    $diagnosed_date_raw = $_POST['diagnosed_date'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $notes = $_POST['notes'] ?? '';
    
    // Validation
    if ($disease_id <= 0) {
        sendError('Please select a disease');
    }
    
    if ($user_id <= 0) {
        sendError('Invalid user session');
    }
    
    // Sanitize inputs
    $status = mysqli_real_escape_string($conn, $status);
    $notes = mysqli_real_escape_string($conn, $notes);
    
    // Validate status
    if (!in_array($status, ['active', 'managed', 'recovered', 'resolved'])) {
        $status = 'active';
    }
    
    // Handle status - check if table uses 'resolved' instead of 'recovered'
    if ($status === 'recovered') {
        $status_check = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases LIKE 'status'");
        if ($status_check) {
            $status_row = mysqli_fetch_assoc($status_check);
            if (isset($status_row['Type']) && strpos($status_row['Type'], 'resolved') !== false && strpos($status_row['Type'], 'recovered') === false) {
                $status = 'resolved';
            }
        }
    }
    
    // Handle diagnosed_date - convert to MySQL date format if provided
    $diagnosed_date = '';
    if (!empty($diagnosed_date_raw)) {
        // If date is in format MM/DD/YYYY, convert to YYYY-MM-DD
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $diagnosed_date_raw, $matches)) {
            $diagnosed_date = $matches[3] . '-' . $matches[1] . '-' . $matches[2];
        } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $diagnosed_date_raw)) {
            // Already in YYYY-MM-DD format
            $diagnosed_date = $diagnosed_date_raw;
        } else {
            $diagnosed_date = mysqli_real_escape_string($conn, $diagnosed_date_raw);
        }
    }
    
    // Check if disease exists
    $disease_check = mysqli_query($conn, "SELECT disease_id FROM diseases WHERE disease_id = $disease_id");
    if (!$disease_check) {
        sendError('Database error: ' . mysqli_error($conn));
    }
    if (mysqli_num_rows($disease_check) == 0) {
        sendError('Selected disease does not exist');
    }
    
    // Check if user already has this disease
    $existing_check = mysqli_query($conn, "SELECT * FROM user_diseases WHERE user_id = $user_id AND disease_id = $disease_id LIMIT 1");
    if (!$existing_check) {
        sendError('Database error: ' . mysqli_error($conn));
    }
    if (mysqli_num_rows($existing_check) > 0) {
        sendError('You have already added this disease');
    }
    
    // Check what columns exist in user_diseases table
    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM user_diseases");
    $columns = [];
    while ($col = mysqli_fetch_assoc($columns_check)) {
        $columns[] = $col['Field'];
    }
    
    // Build INSERT query dynamically based on available columns
    $insert_cols = ['user_id', 'disease_id'];
    $insert_vals = [$user_id, $disease_id];
    
    // Add diagnosed_date if column exists
    if (in_array('diagnosed_date', $columns)) {
        $insert_cols[] = 'diagnosed_date';
        $insert_vals[] = (!empty($diagnosed_date)) ? "'$diagnosed_date'" : "NULL";
    }
    
    // Add status if column exists
    if (in_array('status', $columns)) {
        $insert_cols[] = 'status';
        $insert_vals[] = "'$status'";
    }
    
    // Add notes if column exists
    if (in_array('notes', $columns)) {
        $insert_cols[] = 'notes';
        $insert_vals[] = (!empty(trim($notes))) ? "'$notes'" : "NULL";
    }
    
    // Build and execute INSERT query
    $query = "INSERT INTO user_diseases (" . implode(', ', $insert_cols) . ") 
              VALUES (" . implode(', ', $insert_vals) . ")";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Add Disease Error: " . $error . " | Query: " . $query);
        sendError('Failed to add disease: ' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8'));
    }
    
    // Get the disease details for response
    $disease_query = mysqli_query($conn, "SELECT * FROM diseases WHERE disease_id = $disease_id");
    $disease = null;
    if ($disease_query) {
        $disease = mysqli_fetch_assoc($disease_query);
    }
    
    sendSuccess('Disease added successfully', $disease);
    
} catch (Exception $e) {
    error_log("Add Disease Exception: " . $e->getMessage());
    sendError('An unexpected error occurred: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
} catch (Error $e) {
    error_log("Add Disease Fatal Error: " . $e->getMessage());
    sendError('A fatal error occurred. Please check server logs.');
}

mysqli_close($conn);
?>
