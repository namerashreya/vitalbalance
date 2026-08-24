-- VitalBalance Database Schema v2.0

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    name VARCHAR(255),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Chronic diseases table (trending/common diseases)
CREATE TABLE IF NOT EXISTS diseases (
    disease_id INT PRIMARY KEY AUTO_INCREMENT,
    disease_name VARCHAR(255) NOT NULL,
    prevalence_rate DECIMAL(5,2) DEFAULT 0.00,
    trend VARCHAR(20) DEFAULT 'stable', -- 'increasing', 'decreasing', 'stable'
    trend_percentage DECIMAL(5,2) DEFAULT 0.00,
    description TEXT,
    category VARCHAR(100),
    icon VARCHAR(50),
    color_code VARCHAR(7), -- Hex color
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User diseases table (user's personal chronic diseases)
CREATE TABLE IF NOT EXISTS user_diseases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    disease_id INT NOT NULL,
    diagnosed_date DATE,
    status ENUM('active', 'managed', 'recovered') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (disease_id) REFERENCES diseases(disease_id) ON DELETE CASCADE
);

-- Health records table
CREATE TABLE IF NOT EXISTS health_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    record_type VARCHAR(50) NOT NULL, -- 'blood_pressure', 'blood_sugar', 'weight', 'temperature', 'heart_rate', 'medication', 'symptoms'
    value VARCHAR(100) NOT NULL,
    unit VARCHAR(20),
    recorded_at DATETIME NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_record (user_id, recorded_at)
);

-- Recipes table
CREATE TABLE IF NOT EXISTS recipes (
    recipe_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients TEXT NOT NULL, -- JSON or comma-separated
    instructions TEXT NOT NULL,
    meal_type VARCHAR(50), -- 'breakfast', 'lunch', 'dinner', 'snacks', 'juices'
    prep_time INT DEFAULT 0, -- minutes
    cook_time INT DEFAULT 0, -- minutes
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
    image_url VARCHAR(255),
    health_benefits TEXT,
    nutritional_info TEXT, -- JSON
    servings INT DEFAULT 1,
    tags VARCHAR(255), -- comma-separated
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_meal_type (meal_type)
);

-- User favorites (recipes)
CREATE TABLE IF NOT EXISTS user_favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, recipe_id)
);

-- Insert sample trending diseases
INSERT INTO diseases (disease_name, prevalence_rate, trend, trend_percentage, description, category, icon, color_code) VALUES
('Diabetes Type 2', 8.5, 'increasing', 2.3, 'Metabolic disorder affecting blood sugar levels', 'Metabolic', '🩺', '#FF6B6B'),
('Hypertension', 12.5, 'increasing', 1.8, 'High blood pressure condition', 'Cardiovascular', '❤️', '#4ECDC4'),
('Obesity', 15.2, 'increasing', 3.1, 'Excessive body weight', 'Metabolic', '⚖️', '#FFE66D'),
('Asthma', 7.8, 'stable', 0.2, 'Chronic respiratory condition', 'Respiratory', '🌬️', '#95E1D3'),
('Arthritis', 9.1, 'increasing', 1.5, 'Joint inflammation and pain', 'Musculoskeletal', '🦴', '#F38181'),
('Depression', 6.7, 'increasing', 2.7, 'Mental health condition', 'Mental Health', '🧠', '#AA96DA'),
('Heart Disease', 10.3, 'increasing', 1.9, 'Cardiovascular conditions', 'Cardiovascular', '💓', '#FCBAD3'),
('Chronic Kidney Disease', 4.2, 'increasing', 1.2, 'Progressive loss of kidney function', 'Renal', '🔬', '#A8E6CF');

-- Insert sample recipes
INSERT INTO recipes (title, description, ingredients, instructions, meal_type, prep_time, cook_time, difficulty, health_benefits, tags) VALUES
('Green Detox Smoothie', 'A refreshing morning smoothie packed with nutrients', 'Spinach (2 cups), Banana (1), Apple (1), Lemon juice (1 tbsp), Ginger (1 inch), Water (1 cup)', '1. Wash all fruits and vegetables. 2. Blend spinach, banana, apple, lemon juice, and ginger with water. 3. Blend until smooth. 4. Serve immediately.', 'juices', 5, 0, 'easy', 'Boosts immunity, improves digestion, detoxifies body', 'detox,green,healthy'),
('Quinoa Buddha Bowl', 'Nutritious bowl with quinoa and fresh vegetables', 'Quinoa (1 cup), Chickpeas (1/2 cup), Avocado (1), Cherry tomatoes (1/2 cup), Cucumber (1/2), Lemon dressing', '1. Cook quinoa according to package. 2. Roast chickpeas with spices. 3. Slice avocado and vegetables. 4. Arrange in bowl and drizzle with lemon dressing.', 'lunch', 15, 20, 'medium', 'High protein, fiber-rich, supports heart health', 'vegetarian,protein,healthy'),
('Overnight Oats', 'Easy breakfast that prepares itself overnight', 'Rolled oats (1/2 cup), Almond milk (1/2 cup), Chia seeds (1 tbsp), Berries (1/2 cup), Honey (1 tsp)', '1. Mix oats, milk, and chia seeds. 2. Add berries and honey. 3. Refrigerate overnight. 4. Enjoy cold in the morning.', 'breakfast', 5, 0, 'easy', 'High fiber, supports digestion, provides sustained energy', 'breakfast,overnight,healthy'),
('Turmeric Golden Milk', 'Anti-inflammatory warm beverage', 'Coconut milk (1 cup), Turmeric (1 tsp), Ginger (1/2 inch), Cinnamon (1/2 tsp), Honey (1 tsp), Black pepper (pinch)', '1. Heat coconut milk gently. 2. Add turmeric, ginger, cinnamon. 3. Simmer for 5 minutes. 4. Strain and add honey and black pepper.', 'juices', 5, 5, 'easy', 'Anti-inflammatory, boosts immunity, improves sleep', 'anti-inflammatory,bedtime,healthy');

