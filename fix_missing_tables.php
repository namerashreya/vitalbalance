<?php
include 'includes/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS wellness_scores (
        score_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        overall_score INT CHECK (overall_score >= 0 AND overall_score <= 100),
        physical_score INT CHECK (physical_score >= 0 AND physical_score <= 100),
        mental_score INT CHECK (mental_score >= 0 AND mental_score <= 100),
        emotional_score INT CHECK (emotional_score >= 0 AND emotional_score <= 100),
        date_recorded DATE NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS remedy_diseases (
        remedy_disease_id INT AUTO_INCREMENT PRIMARY KEY,
        remedy_id INT NOT NULL,
        disease_id INT NOT NULL,
        effectiveness_rating INT DEFAULT 3 CHECK (effectiveness_rating >= 1 AND effectiveness_rating <= 5),
        notes TEXT,
        FOREIGN KEY (remedy_id) REFERENCES remedies(remedy_id) ON DELETE CASCADE,
        FOREIGN KEY (disease_id) REFERENCES diseases(disease_id) ON DELETE CASCADE,
        UNIQUE KEY unique_remedy_disease (remedy_id, disease_id)
    )",
    "CREATE TABLE IF NOT EXISTS appointments (
        appointment_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        appointment_type VARCHAR(50),
        healthcare_provider VARCHAR(100),
        appointment_date DATETIME,
        notes TEXT,
        status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS symptoms_log (
        symptom_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        symptom_name VARCHAR(100) NOT NULL,
        severity INT CHECK (severity >= 1 AND severity <= 10),
        date_occurred DATE NOT NULL,
        duration VARCHAR(50),
        triggers TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )"
];

foreach ($queries as $q) {
    if (mysqli_query($conn, $q)) {
        echo "Success executing query.\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}
?>
