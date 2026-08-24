-- VitalBalance Database Schema
-- An Integrated Wellness System for Personalized Chronic Disease Management and Natural Remedies

-- Diseases table
CREATE TABLE IF NOT EXISTS diseases (
    disease_id INT AUTO_INCREMENT PRIMARY KEY,
    disease_name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    severity_level ENUM('mild', 'moderate', 'severe') DEFAULT 'moderate',
    common_symptoms TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Diseases (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS user_diseases (
    user_disease_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    disease_id INT NOT NULL,
    status ENUM('active', 'managed', 'resolved') DEFAULT 'active',
    diagnosed_date DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (disease_id) REFERENCES diseases(disease_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_disease (user_id, disease_id)
);

-- Natural Remedies table
CREATE TABLE IF NOT EXISTS remedies (
    remedy_id INT AUTO_INCREMENT PRIMARY KEY,
    remedy_name VARCHAR(100) NOT NULL,
    description TEXT,
    benefits TEXT,
    usage_instructions TEXT,
    category VARCHAR(50),
    tags VARCHAR(255),
    dosage VARCHAR(100),
    precautions TEXT,
    related_diseases TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Remedy-Disease Relationship (Many-to-Many)
CREATE TABLE IF NOT EXISTS remedy_diseases (
    remedy_disease_id INT AUTO_INCREMENT PRIMARY KEY,
    remedy_id INT NOT NULL,
    disease_id INT NOT NULL,
    effectiveness_rating INT DEFAULT 3 CHECK (effectiveness_rating >= 1 AND effectiveness_rating <= 5),
    notes TEXT,
    FOREIGN KEY (remedy_id) REFERENCES remedies(remedy_id) ON DELETE CASCADE,
    FOREIGN KEY (disease_id) REFERENCES diseases(disease_id) ON DELETE CASCADE,
    UNIQUE KEY unique_remedy_disease (remedy_id, disease_id)
);

-- Health Records table
CREATE TABLE IF NOT EXISTS health_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    record_type ENUM('vitals', 'symptoms', 'medication', 'appointment', 'test', 'other') DEFAULT 'vitals',
    title VARCHAR(100),
    description TEXT,
    metric_name VARCHAR(50),
    metric_value VARCHAR(50),
    metric_unit VARCHAR(20),
    date_recorded DATE NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Wellness Tracking table
CREATE TABLE IF NOT EXISTS wellness_scores (
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
);

-- User Remedy Tracking (track user's use of remedies)
CREATE TABLE IF NOT EXISTS user_remedies (
    user_remedy_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    remedy_id INT NOT NULL,
    started_date DATE,
    ended_date DATE,
    frequency VARCHAR(50),
    dosage_taken VARCHAR(100),
    effectiveness_rating INT CHECK (effectiveness_rating >= 1 AND effectiveness_rating <= 5),
    notes TEXT,
    status ENUM('active', 'completed', 'stopped') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (remedy_id) REFERENCES remedies(remedy_id) ON DELETE CASCADE
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_type VARCHAR(50),
    healthcare_provider VARCHAR(100),
    appointment_date DATETIME,
    notes TEXT,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Medications table (for tracking medications)
CREATE TABLE IF NOT EXISTS medications (
    medication_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medication_name VARCHAR(100) NOT NULL,
    dosage VARCHAR(100),
    frequency VARCHAR(50),
    start_date DATE,
    end_date DATE,
    prescribed_by VARCHAR(100),
    notes TEXT,
    status ENUM('active', 'completed', 'stopped') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Symptoms Log table
CREATE TABLE IF NOT EXISTS symptoms_log (
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
);

-- Insert Sample Diseases
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Diabetes Type 2', 'A chronic condition affecting the way the body processes blood sugar', 'Metabolic', 'moderate', 'Increased thirst, frequent urination, fatigue, blurred vision'),
('Hypertension', 'High blood pressure condition affecting cardiovascular health', 'Cardiovascular', 'moderate', 'Headaches, shortness of breath, nosebleeds'),
('Arthritis', 'Inflammation of joints causing pain and stiffness', 'Musculoskeletal', 'moderate', 'Joint pain, stiffness, swelling, reduced range of motion'),
('Asthma', 'Chronic respiratory condition affecting breathing', 'Respiratory', 'moderate', 'Wheezing, shortness of breath, chest tightness, coughing'),
('Migraine', 'Recurrent headaches often accompanied by nausea', 'Neurological', 'mild', 'Severe headache, nausea, sensitivity to light and sound');

-- Insert Sample Remedies
INSERT INTO remedies (remedy_name, description, benefits, usage_instructions, category, tags, dosage, precautions, related_diseases) VALUES
('Turmeric', 'Golden spice with anti-inflammatory properties', 'Reduces inflammation, improves joint health, supports immune system', 'Mix 1 tsp in warm milk or water, take once daily', 'Herbal', 'anti-inflammatory,joint-health,immune', '1 teaspoon daily', 'May interact with blood thinners', 'Arthritis,Inflammation'),
('Ginger Tea', 'Warming herb that aids digestion and reduces inflammation', 'Relieves nausea, reduces inflammation, improves circulation', 'Steep fresh ginger slices in hot water for 10 minutes', 'Herbal Tea', 'digestion,anti-inflammatory,nausea', '1-2 cups daily', 'May cause heartburn in some individuals', 'Nausea,Digestive Issues'),
('Aloe Vera', 'Soothing plant gel with healing properties', 'Soothes skin, aids digestion, supports immune health', 'Apply topically for skin issues or consume gel for digestive health', 'Plant', 'skin-healing,digestion,immune', 'Apply as needed or 1-2 tbsp internally', 'Do not consume aloe latex, only gel', 'Skin Conditions,Digestive Health'),
('Omega-3 Fatty Acids', 'Essential fats found in fish and flax seeds', 'Supports heart health, reduces inflammation, improves brain function', 'Take as supplement or consume fatty fish 2-3 times per week', 'Supplement', 'heart-health,brain-function,anti-inflammatory', '1000-2000mg daily', 'May interact with blood thinners', 'Heart Disease,Inflammation'),
('Green Tea', 'Antioxidant-rich tea with numerous health benefits', 'Boosts metabolism, provides antioxidants, supports cardiovascular health', 'Steep 1-2 teaspoons in hot water for 3-5 minutes', 'Herbal Tea', 'antioxidants,metabolism,heart-health', '2-3 cups daily', 'Contains caffeine, limit if sensitive', 'Metabolism,Heart Health');

