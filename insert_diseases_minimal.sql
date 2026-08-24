-- Insert 50+ Common Diseases - Minimal Version
-- This version only uses disease_name and description (most basic columns)
-- Run this if your table doesn't have category, severity_level, or common_symptoms columns

-- Metabolic Disorders
INSERT INTO diseases (disease_name, description) VALUES
('Diabetes Type 2', 'Metabolic disorder affecting blood sugar levels'),
('Diabetes Type 1', 'Autoimmune condition requiring insulin'),
('Prediabetes', 'Elevated blood sugar levels below diabetes threshold'),
('PCOD (Polycystic Ovarian Disease)', 'Hormonal disorder affecting women of reproductive age'),
('PCOS (Polycystic Ovary Syndrome)', 'Hormonal imbalance causing irregular periods and cysts'),
('Hypothyroidism', 'Underactive thyroid gland'),
('Hyperthyroidism', 'Overactive thyroid gland'),
('Metabolic Syndrome', 'Cluster of conditions increasing heart disease risk'),
('Obesity', 'Excessive body weight affecting health'),
('Insulin Resistance', 'Cells become less responsive to insulin');

-- Cardiovascular Diseases
INSERT INTO diseases (disease_name, description) VALUES
('Hypertension', 'High blood pressure condition'),
('Heart Disease', 'Cardiovascular conditions affecting heart function'),
('Coronary Artery Disease', 'Narrowing of coronary arteries'),
('Arrhythmia', 'Irregular heart rhythm'),
('Heart Failure', 'Heart unable to pump blood effectively'),
('Atrial Fibrillation', 'Irregular and rapid heart rate'),
('High Cholesterol', 'Elevated levels of cholesterol in blood'),
('Peripheral Artery Disease', 'Narrowed arteries reducing blood flow to limbs');

-- Respiratory Diseases
INSERT INTO diseases (disease_name, description) VALUES
('Asthma', 'Chronic respiratory condition'),
('COPD (Chronic Obstructive Pulmonary Disease)', 'Progressive lung disease causing breathing difficulty'),
('Chronic Bronchitis', 'Long-term inflammation of bronchi'),
('Emphysema', 'Lung condition causing shortness of breath'),
('Sleep Apnea', 'Breathing repeatedly stops and starts during sleep');

-- Mental Health
INSERT INTO diseases (disease_name, description) VALUES
('Depression', 'Mental health condition affecting mood'),
('Anxiety Disorder', 'Excessive worry and fear'),
('Bipolar Disorder', 'Mood disorder with extreme highs and lows'),
('PTSD (Post-Traumatic Stress Disorder)', 'Anxiety disorder triggered by traumatic events'),
('OCD (Obsessive-Compulsive Disorder)', 'Anxiety disorder with repetitive thoughts and behaviors'),
('ADHD (Attention Deficit Hyperactivity Disorder)', 'Neurodevelopmental disorder affecting attention'),
('Panic Disorder', 'Recurrent panic attacks');

-- Musculoskeletal
INSERT INTO diseases (disease_name, description) VALUES
('Arthritis', 'Joint inflammation and pain'),
('Rheumatoid Arthritis', 'Autoimmune disease causing joint inflammation'),
('Osteoarthritis', 'Degenerative joint disease'),
('Osteoporosis', 'Bone density loss increasing fracture risk'),
('Fibromyalgia', 'Chronic pain condition affecting muscles and soft tissues'),
('Gout', 'Inflammatory arthritis caused by uric acid crystals'),
('Lower Back Pain', 'Chronic pain in the lower back region');

-- Neurological
INSERT INTO diseases (disease_name, description) VALUES
('Migraine', 'Recurrent severe headaches'),
('Epilepsy', 'Neurological disorder causing seizures'),
('Parkinson\'s Disease', 'Progressive nervous system disorder'),
('Multiple Sclerosis', 'Autoimmune disease affecting central nervous system'),
('Alzheimer\'s Disease', 'Progressive brain disorder affecting memory'),
('Chronic Fatigue Syndrome', 'Severe fatigue not improved by rest');

-- Renal
INSERT INTO diseases (disease_name, description) VALUES
('Chronic Kidney Disease', 'Progressive loss of kidney function'),
('Kidney Stones', 'Hard deposits forming in kidneys'),
('Nephritis', 'Inflammation of the kidneys');

-- Gastrointestinal
INSERT INTO diseases (disease_name, description) VALUES
('Irritable Bowel Syndrome (IBS)', 'Chronic digestive disorder'),
('Crohn\'s Disease', 'Inflammatory bowel disease'),
('Ulcerative Colitis', 'Chronic inflammatory bowel disease'),
('GERD (Gastroesophageal Reflux Disease)', 'Chronic acid reflux'),
('Celiac Disease', 'Autoimmune reaction to gluten'),
('Gastritis', 'Inflammation of the stomach lining');

-- Autoimmune
INSERT INTO diseases (disease_name, description) VALUES
('Lupus', 'Autoimmune disease affecting multiple organs'),
('Psoriasis', 'Chronic skin condition with rapid cell growth'),
('Hashimoto\'s Thyroiditis', 'Autoimmune thyroid disorder');

-- Skin Conditions
INSERT INTO diseases (disease_name, description) VALUES
('Eczema', 'Chronic skin inflammation'),
('Dermatitis', 'Skin inflammation from various causes');

-- Other Common Conditions
INSERT INTO diseases (disease_name, description) VALUES
('Anemia', 'Low red blood cell count'),
('Vitamin D Deficiency', 'Insufficient vitamin D levels'),
('Osteopenia', 'Lower than normal bone density'),
('Sleep Disorders', 'Various conditions affecting sleep quality'),
('Chronic Pain', 'Persistent pain lasting months or years'),
('Tinnitus', 'Ringing or buzzing in the ears');
