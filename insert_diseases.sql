-- Insert 50+ Common Diseases with Categories
-- Run this SQL file to populate the diseases table
-- Compatible with database_schema.sql structure

-- Metabolic Disorders
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Diabetes Type 2', 'Metabolic disorder affecting blood sugar levels', 'Metabolic', 'moderate', 'Increased thirst, frequent urination, fatigue, blurred vision'),
('Diabetes Type 1', 'Autoimmune condition requiring insulin', 'Metabolic', 'moderate', 'Increased thirst, frequent urination, weight loss, fatigue'),
('Prediabetes', 'Elevated blood sugar levels below diabetes threshold', 'Metabolic', 'mild', 'Slightly elevated blood sugar, increased thirst'),
('PCOD (Polycystic Ovarian Disease)', 'Hormonal disorder affecting women of reproductive age', 'Endocrine', 'moderate', 'Irregular periods, weight gain, excess hair growth, acne'),
('PCOS (Polycystic Ovary Syndrome)', 'Hormonal imbalance causing irregular periods and cysts', 'Endocrine', 'moderate', 'Irregular periods, ovarian cysts, weight gain, infertility'),
('Hypothyroidism', 'Underactive thyroid gland', 'Endocrine', 'moderate', 'Fatigue, weight gain, cold intolerance, depression'),
('Hyperthyroidism', 'Overactive thyroid gland', 'Endocrine', 'moderate', 'Weight loss, rapid heartbeat, anxiety, tremors'),
('Metabolic Syndrome', 'Cluster of conditions increasing heart disease risk', 'Metabolic', 'moderate', 'High blood pressure, high blood sugar, excess body fat'),
('Obesity', 'Excessive body weight affecting health', 'Metabolic', 'moderate', 'Excess body fat, difficulty breathing, joint pain'),
('Insulin Resistance', 'Cells become less responsive to insulin', 'Metabolic', 'moderate', 'High blood sugar, fatigue, increased hunger');

-- Cardiovascular Diseases
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Hypertension', 'High blood pressure condition', 'Cardiovascular', 'moderate', 'Headaches, shortness of breath, nosebleeds, dizziness'),
('Heart Disease', 'Cardiovascular conditions affecting heart function', 'Cardiovascular', 'severe', 'Chest pain, shortness of breath, fatigue, irregular heartbeat'),
('Coronary Artery Disease', 'Narrowing of coronary arteries', 'Cardiovascular', 'severe', 'Chest pain, shortness of breath, heart attack risk'),
('Arrhythmia', 'Irregular heart rhythm', 'Cardiovascular', 'moderate', 'Palpitations, dizziness, chest discomfort, fainting'),
('Heart Failure', 'Heart unable to pump blood effectively', 'Cardiovascular', 'severe', 'Shortness of breath, fatigue, swelling in legs, rapid heartbeat'),
('Atrial Fibrillation', 'Irregular and rapid heart rate', 'Cardiovascular', 'moderate', 'Palpitations, weakness, dizziness, chest pain'),
('High Cholesterol', 'Elevated levels of cholesterol in blood', 'Cardiovascular', 'moderate', 'Usually no symptoms, detected through blood tests'),
('Peripheral Artery Disease', 'Narrowed arteries reducing blood flow to limbs', 'Cardiovascular', 'moderate', 'Leg pain, numbness, weakness, slow healing wounds');

-- Respiratory Diseases
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Asthma', 'Chronic respiratory condition', 'Respiratory', 'moderate', 'Wheezing, shortness of breath, chest tightness, coughing'),
('COPD (Chronic Obstructive Pulmonary Disease)', 'Progressive lung disease causing breathing difficulty', 'Respiratory', 'severe', 'Shortness of breath, chronic cough, wheezing, chest tightness'),
('Chronic Bronchitis', 'Long-term inflammation of bronchi', 'Respiratory', 'moderate', 'Persistent cough, mucus production, shortness of breath'),
('Emphysema', 'Lung condition causing shortness of breath', 'Respiratory', 'severe', 'Shortness of breath, chronic cough, wheezing, fatigue'),
('Sleep Apnea', 'Breathing repeatedly stops and starts during sleep', 'Respiratory', 'moderate', 'Loud snoring, daytime sleepiness, morning headaches, gasping for air');

-- Mental Health
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Depression', 'Mental health condition affecting mood', 'Mental Health', 'moderate', 'Persistent sadness, loss of interest, fatigue, sleep problems'),
('Anxiety Disorder', 'Excessive worry and fear', 'Mental Health', 'moderate', 'Excessive worry, restlessness, difficulty concentrating, sleep problems'),
('Bipolar Disorder', 'Mood disorder with extreme highs and lows', 'Mental Health', 'severe', 'Mood swings, manic episodes, depressive episodes, energy changes'),
('PTSD (Post-Traumatic Stress Disorder)', 'Anxiety disorder triggered by traumatic events', 'Mental Health', 'moderate', 'Flashbacks, nightmares, anxiety, avoidance of triggers'),
('OCD (Obsessive-Compulsive Disorder)', 'Anxiety disorder with repetitive thoughts and behaviors', 'Mental Health', 'moderate', 'Obsessive thoughts, compulsive behaviors, anxiety, distress'),
('ADHD (Attention Deficit Hyperactivity Disorder)', 'Neurodevelopmental disorder affecting attention', 'Mental Health', 'moderate', 'Inattention, hyperactivity, impulsivity, difficulty focusing'),
('Panic Disorder', 'Recurrent panic attacks', 'Mental Health', 'moderate', 'Sudden panic attacks, chest pain, rapid heartbeat, fear of dying');

-- Musculoskeletal
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Arthritis', 'Joint inflammation and pain', 'Musculoskeletal', 'moderate', 'Joint pain, stiffness, swelling, reduced range of motion'),
('Rheumatoid Arthritis', 'Autoimmune disease causing joint inflammation', 'Musculoskeletal', 'severe', 'Joint pain, swelling, morning stiffness, fatigue'),
('Osteoarthritis', 'Degenerative joint disease', 'Musculoskeletal', 'moderate', 'Joint pain, stiffness, loss of flexibility, bone spurs'),
('Osteoporosis', 'Bone density loss increasing fracture risk', 'Musculoskeletal', 'moderate', 'Bone fractures, loss of height, back pain, stooped posture'),
('Fibromyalgia', 'Chronic pain condition affecting muscles and soft tissues', 'Musculoskeletal', 'moderate', 'Widespread pain, fatigue, sleep problems, cognitive issues'),
('Gout', 'Inflammatory arthritis caused by uric acid crystals', 'Musculoskeletal', 'moderate', 'Severe joint pain, swelling, redness, warmth in joints'),
('Lower Back Pain', 'Chronic pain in the lower back region', 'Musculoskeletal', 'moderate', 'Persistent back pain, stiffness, muscle spasms, limited mobility');

-- Neurological
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Migraine', 'Recurrent severe headaches', 'Neurological', 'moderate', 'Severe headache, nausea, sensitivity to light and sound, visual disturbances'),
('Epilepsy', 'Neurological disorder causing seizures', 'Neurological', 'severe', 'Seizures, loss of consciousness, confusion, staring spells'),
('Parkinson\'s Disease', 'Progressive nervous system disorder', 'Neurological', 'severe', 'Tremors, slow movement, muscle stiffness, balance problems'),
('Multiple Sclerosis', 'Autoimmune disease affecting central nervous system', 'Neurological', 'severe', 'Fatigue, numbness, weakness, vision problems, coordination issues'),
('Alzheimer\'s Disease', 'Progressive brain disorder affecting memory', 'Neurological', 'severe', 'Memory loss, confusion, difficulty thinking, personality changes'),
('Chronic Fatigue Syndrome', 'Severe fatigue not improved by rest', 'Neurological', 'moderate', 'Extreme fatigue, sleep problems, muscle pain, cognitive difficulties');

-- Renal
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Chronic Kidney Disease', 'Progressive loss of kidney function', 'Renal', 'severe', 'Fatigue, swelling, changes in urination, nausea'),
('Kidney Stones', 'Hard deposits forming in kidneys', 'Renal', 'moderate', 'Severe pain in side and back, blood in urine, frequent urination'),
('Nephritis', 'Inflammation of the kidneys', 'Renal', 'moderate', 'Blood in urine, high blood pressure, swelling, reduced urine output');

-- Gastrointestinal
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Irritable Bowel Syndrome (IBS)', 'Chronic digestive disorder', 'Gastrointestinal', 'moderate', 'Abdominal pain, bloating, diarrhea, constipation'),
('Crohn\'s Disease', 'Inflammatory bowel disease', 'Gastrointestinal', 'severe', 'Abdominal pain, diarrhea, weight loss, fatigue, fever'),
('Ulcerative Colitis', 'Chronic inflammatory bowel disease', 'Gastrointestinal', 'severe', 'Diarrhea with blood, abdominal pain, urgency to defecate, weight loss'),
('GERD (Gastroesophageal Reflux Disease)', 'Chronic acid reflux', 'Gastrointestinal', 'moderate', 'Heartburn, regurgitation, chest pain, difficulty swallowing'),
('Celiac Disease', 'Autoimmune reaction to gluten', 'Gastrointestinal', 'moderate', 'Diarrhea, bloating, weight loss, fatigue, anemia'),
('Gastritis', 'Inflammation of the stomach lining', 'Gastrointestinal', 'mild', 'Stomach pain, nausea, vomiting, indigestion');

-- Autoimmune
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Lupus', 'Autoimmune disease affecting multiple organs', 'Autoimmune', 'severe', 'Fatigue, joint pain, skin rash, fever, organ inflammation'),
('Psoriasis', 'Chronic skin condition with rapid cell growth', 'Autoimmune', 'moderate', 'Red patches, silvery scales, dry cracked skin, itching'),
('Hashimoto\'s Thyroiditis', 'Autoimmune thyroid disorder', 'Autoimmune', 'moderate', 'Fatigue, weight gain, cold intolerance, depression, hair loss');

-- Skin Conditions
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Eczema', 'Chronic skin inflammation', 'Dermatological', 'moderate', 'Itchy skin, red patches, dry skin, skin thickening'),
('Dermatitis', 'Skin inflammation from various causes', 'Dermatological', 'mild', 'Redness, itching, swelling, blisters, dry skin');

-- Other Common Conditions
INSERT INTO diseases (disease_name, description, category, severity_level, common_symptoms) VALUES
('Anemia', 'Low red blood cell count', 'Hematological', 'moderate', 'Fatigue, weakness, pale skin, shortness of breath, dizziness'),
('Vitamin D Deficiency', 'Insufficient vitamin D levels', 'Nutritional', 'mild', 'Fatigue, bone pain, muscle weakness, mood changes'),
('Osteopenia', 'Lower than normal bone density', 'Musculoskeletal', 'mild', 'Usually no symptoms, detected through bone density scan'),
('Sleep Disorders', 'Various conditions affecting sleep quality', 'Sleep', 'moderate', 'Difficulty falling asleep, frequent waking, daytime sleepiness'),
('Chronic Pain', 'Persistent pain lasting months or years', 'Pain Management', 'moderate', 'Persistent pain, fatigue, sleep problems, mood changes'),
('Tinnitus', 'Ringing or buzzing in the ears', 'Auditory', 'mild', 'Ringing, buzzing, or hissing sounds in ears');
