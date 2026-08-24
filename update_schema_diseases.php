<?php
include("includes/db.php");

function executeQuery($conn, $sql, $message) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $message<br>";
    } else {
        // If error is duplicate column, that's fine
        if (strpos(mysqli_error($conn), "Duplicate column") !== false) {
             echo "[INFO] $message (Already exists)<br>";
        } else {
            echo "[ERROR] $message: " . mysqli_error($conn) . "<br>";
        }
    }
}

// Add status column
executeQuery($conn, 
    "ALTER TABLE user_diseases ADD COLUMN status ENUM('active', 'managed', 'recovered') DEFAULT 'active'", 
    "Added 'status' column to user_diseases"
);

// Add notes column
executeQuery($conn, 
    "ALTER TABLE user_diseases ADD COLUMN notes TEXT NULL", 
    "Added 'notes' column to user_diseases"
);

// Add created_at column
executeQuery($conn, 
    "ALTER TABLE user_diseases ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP", 
    "Added 'created_at' column to user_diseases"
);

echo "Schema update completed.";
?>
