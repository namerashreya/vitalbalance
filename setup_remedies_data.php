<?php
include("includes/db.php");

// 1. Create or Update Remedies Table
mysqli_query($conn, "DROP TABLE IF EXISTS remedies");
$sql_create = "CREATE TABLE remedies (
    remedy_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    disease_id INT(11),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients TEXT,
    instructions TEXT,
    image_path VARCHAR(255) DEFAULT 'default_remedy.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql_create)) {
    echo "Remedies table created/checked.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// 2. Ensure we have some diseases to link to
// Let's get some disease IDs or insert dummy ones if empty
$check_diseases = mysqli_query($conn, "SELECT * FROM diseases");
if (mysqli_num_rows($check_diseases) == 0) {
    mysqli_query($conn, "INSERT INTO diseases (disease_name, description) VALUES 
    ('Flu', 'Viral infection aimed at respiratory system'),
    ('Diabetes', 'High blood sugar levels'),
    ('Hypertension', 'High blood pressure'),
    ('Migraine', 'Severe headache often with nausea')");
    echo "Inserted sample diseases.<br>";
}

// 3. Insert Sample Remedies
// Clear old samples to avoid duplicates for this demo
mysqli_query($conn, "TRUNCATE TABLE remedies");

// Get Disease IDs
$diseases = [];
$r = mysqli_query($conn, "SELECT * FROM diseases");
while($row = mysqli_fetch_assoc($r)) {
    $diseases[$row['disease_name']] = $row['disease_id'];
}

$flu_id = $diseases['Flu'] ?? 0;
$diab_id = $diseases['Diabetes'] ?? 0;

$samples = [
    [
        $flu_id, 
        "Ginger Tea", 
        "A soothing tea to relieve sore throat and congestion.", 
        "Fresh ginger, Water, Honey, Lemon",
        "Boil water with ginger for 5 mins. Strain, add honey and lemon.",
        "assets/images/ginger_tea_remedy.png"
    ],
    [
        $flu_id, 
        "Chicken Soup", 
        "Classic clear soup to hydrate and provide nutrients.", 
        "Chicken, Carrots, Celery, Onions, Broth",
        "Simmer chicken with veggies until tender.",
        "assets/images/healthy_soup_remedy.png"
    ],
    [
        $diab_id, 
        "Balanced Diet Plan", 
        "Low glycemic index foods to manage blood sugar.", 
        "Leafy greens, Whole grains, Lean protein",
        "Focus on portion control and regular meal times.",
        "assets/images/diet_chart_remedy.png"
    ]
];

$stmt = $conn->prepare("INSERT INTO remedies (disease_id, name, description, ingredients, instructions, image_path) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($samples as $sample) {
    $stmt->bind_param("isssss", $sample[0], $sample[1], $sample[2], $sample[3], $sample[4], $sample[5]);
    $stmt->execute();
}
echo "Sample remedies inserted.<br>";

echo "Setup Complete.";
?>
