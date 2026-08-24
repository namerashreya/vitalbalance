<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Fetch remedies from database instead of hardcoded array
$remedies = [];
$db_remedies_query = "SELECT * FROM remedies WHERE approved_by_admin = 1 ORDER BY remedy_name ASC";
$db_remedies_result = mysqli_query($conn, $db_remedies_query);

while ($row = mysqli_fetch_assoc($db_remedies_result)) {
    // Convert comma-separated ingredients back to array if needed (though the JS might handle it)
    $ingredients = array_map('trim', explode(',', $row['ingredients']));
    
    $remedies[] = [
        'title' => $row['remedy_name'],
        'category' => $row['category'],
        'image' => $row['image'],
        'description' => $row['description'],
        'ingredients' => $ingredients,
        'instructions' => $row['instructions']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalBalance | Browse Remedies</title>
    <link rel="stylesheet" href="../assets/css/design-system-v2.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-beige);
            min-height: 100vh;
        }

        .remedies-container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--primary-green-dark);
            animation: slideDown 0.6s ease-out;
        }

        .page-header p {
            font-size: 18px;
            color: #64748b;
            animation: slideDown 0.6s ease-out 0.2s backwards;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Slideshow Styles */
        .slideshow-section {
            margin-bottom: 60px;
            position: relative;
        }

        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: 0 auto;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(5, 150, 105, 0.15);
            height: 500px;
            background: #fff;
            display: flex;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slideshow-container:hover {
            transform: scale(1.01);
            box-shadow: 0 40px 80px rgba(5, 150, 105, 0.2);
        }

        .slide {
            display: none;
            width: 100%;
            height: 100%;
            animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slide.active {
            display: flex;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .slide-img-container {
            flex: 1.3;
            overflow: hidden;
            position: relative;
        }

        .slide-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 10s linear;
        }

        .slide.active .slide-img {
            transform: scale(1.15);
        }

        .slide-content {
            flex: 0.7;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
        }

        .slide-content::before {
            content: '\f06c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 40px;
            opacity: 0.1;
            transform: rotate(20deg);
            color: var(--primary-green);
        }

        .slide-tag {
            display: inline-block;
            padding: 8px 16px;
            background: var(--secondary-sage-light);
            color: var(--primary-green-dark);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 24px;
            width: fit-content;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--shadow-sm);
        }

        .slide-title {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 800;
            color: #064e3b;
            margin-bottom: 20px;
            line-height: 1.1;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
        }

        .slide.active .slide-title {
            transform: translateY(0);
            opacity: 1;
        }

        .slide-desc {
            font-size: 19px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 36px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.4s;
        }

        .slide.active .slide-desc {
            transform: translateY(0);
            opacity: 1;
        }

        .slide-btn {
            padding: 18px 36px;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 20px;
            font-weight: 700;
            font-size: 17px;
            cursor: pointer;
            width: fit-content;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.6s;
        }

        .slide.active .slide-btn {
            transform: translateY(0);
            opacity: 1;
        }

        .slide-btn:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        .slideshow-nav {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(5, 150, 105, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            width: 30px;
            border-radius: 10px;
            background: var(--primary-green);
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: 60px;
            height: 60px;
            margin-top: -30px;
            color: #059669;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            z-index: 10;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: none;
            font-size: 18px;
        }

        .next { right: -30px; }
        .prev { left: -30px; }

        .prev:hover, .next:hover {
            background-color: #059669;
            color: white;
            transform: scale(1.1);
        }

        @media (max-width: 1024px) {
            .next { right: 10px; }
            .prev { left: 10px; }
            .slideshow-container { height: auto; flex-direction: column; }
            .slide.active { flex-direction: column; }
            .slide-img-container { height: 300px; width: 100%; }
            .slide-content { width: 100%; padding: 40px; }
            .slide-title { font-size: 32px; }
        }

        /* Filter Dropdown */
        .filter-container {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }

        .filter-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .filter-select {
            appearance: none;
            -webkit-appearance: none;
            width: 100%;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            background-color: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2310b981%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 14px;
            padding-right: 50px;
            font-family: 'Inter', sans-serif;
        }

        .filter-select:hover, .filter-select:focus {
            border-color: #10b981;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15);
            outline: none;
        }

        .remedy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .remedy-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(0,0,0,0.05);
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .remedy-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }

        .card-img-wrapper {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .card-img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.4));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .remedy-card:hover .card-img-overlay {
            opacity: 1;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .remedy-card:hover .card-img {
            transform: scale(1.1);
        }

        .card-body {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-category {
            font-size: 11px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 12px;
            background: #ecfdf5;
            padding: 4px 10px;
            border-radius: 8px;
            width: fit-content;
        }

        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }
        
        .remedy-card:hover .card-title {
            color: var(--primary-green);
        }

        .card-text {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
            font-size: 13px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .add-remedy-btn {
            width: 100%;
            padding: 12px 20px;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-remedy-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669, #047857);
        }

        .add-remedy-btn:active {
            transform: translateY(-1px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: white;
            border-radius: 30px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
        }

        .modal-main-content {
            display: grid;
            grid-template-columns: 350px 1fr;
            overflow-y: auto;
            max-height: calc(90vh - 100px);
        }

        @media (max-width: 800px) {
            .modal-main-content {
                grid-template-columns: 1fr;
            }
            .modal-header {
                height: 250px;
            }
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 2px solid #f1f5f9;
            background: white;
            border-radius: 0 0 30px 30px;
            position: sticky;
            bottom: 0;
            z-index: 10;
        }

        .modal-header {
            position: relative;
            height: 100%;
            min-height: 250px;
        }

        .modal-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background-color: #f1f5f9;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 10;
            color: #1e293b;
            font-size: 20px;
        }

        .close-btn:hover {
            transform: rotate(90deg) scale(1.1);
        }

        .modal-body {
            padding: 40px;
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-category {
            display: inline-block;
            padding: 6px 14px;
            background: #dbeafe;
            color: #2563eb;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #10b981;
            padding-left: 12px;
        }

        .ingredients-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 32px;
        }

        .ingredients-list li {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
        }

        .ingredients-list li i {
            color: #10b981;
        }

        .instruction-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 24px;
            color: #166534;
            line-height: 1.8;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .slideshow-container {
                height: auto;
                flex-direction: column;
            }
            .slide.active {
                flex-direction: column;
            }
            .slide-img-container {
                height: 250px;
            }
            .slide-content {
                padding: 30px;
            }
            .modal-content {
                grid-template-columns: 1fr;
            }
        }

        /* Header Customization for Remedies Page */
    </style>
</head>
<body>

    <!-- Shared Header & Nav -->
    <?php include('../includes/header.php'); ?>

    <main class="remedies-container">


        <div class="page-header">
            <h1><i class="fas fa-leaf" style="color: var(--primary-green);"></i> Natural Remedies Library</h1>
            <p>Discover time-tested home remedies for everyday wellness</p>
        </div>

        <!-- Slideshow Section -->
        <div class="slideshow-section">
            <div class="slideshow-container" id="remedySlideshow" onmouseenter="pauseAutoSlide()" onmouseleave="resumeAutoSlide()">
                <!-- Dynamic Slides -->
                <?php 
                $slideshow_items = $remedies;
                if (count($remedies) > 5) {
                    $random_keys = array_rand($remedies, 5);
                    $slideshow_items = [];
                    foreach($random_keys as $k) {
                        $slideshow_items[$k] = $remedies[$k];
                    }
                }
                
                $slide_count = count($slideshow_items);
                $i = 0;
                foreach($slideshow_items as $index => $item): 
                    $i++;
                ?>
                <div class="slide <?php echo ($i === 1) ? 'active' : ''; ?>">
                    <div class="slide-img-container">
                        <img src="<?php echo $item['image']; ?>" class="slide-img" onerror="this.onerror=null;this.src='https://placehold.co/800x600/e2e8f0/475569?text=<?php echo urlencode($item['title']); ?>'">
                    </div>
                    <div class="slide-content">
                        <span class="slide-tag"><?php echo htmlspecialchars($item['category']); ?></span>
                        <h2 class="slide-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                        <p class="slide-desc"><?php echo htmlspecialchars(substr($item['description'], 0, 150)) . (strlen($item['description']) > 150 ? '...' : ''); ?></p>
                        <button class="slide-btn" onclick="openModal(<?php echo $index; ?>)">
                            Explore Remedy <i class="fas fa-sparkles"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

                <button class="prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>

                <div class="slideshow-nav">
                    <?php for($j = 0; $j < $slide_count; $j++): ?>
                        <span class="dot <?php echo ($j === 0) ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $j + 1; ?>)"></span>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-container">
            <?php
            $categories = array_unique(array_column($remedies, 'category'));
            sort($categories);
            ?>
            <div class="filter-wrapper">
                <select class="filter-select" onchange="filterRemedies(this.value)">
                    <option value="all">Show All Diseases & Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Grid of Remedies -->
        <div class="remedy-grid">
            <?php foreach($remedies as $index => $remedy): ?>
            <div class="remedy-card" data-category="<?php echo $remedy['category']; ?>">
                <div class="card-img-wrapper" onclick="openModal(<?php echo $index; ?>)" style="cursor: pointer;">
                    <img src="<?php echo $remedy['image']; ?>" class="card-img" alt="<?php echo $remedy['title']; ?>" onerror="this.onerror=null;this.src='https://placehold.co/800x600/e2e8f0/475569?text=<?php echo urlencode($remedy['title']); ?>'">
                    <div class="card-img-overlay"></div>
                </div>
                <div class="card-body">
                    <div class="card-category"><?php echo $remedy['category']; ?></div>
                    <h3 class="card-title"><?php echo $remedy['title']; ?></h3>
                    <p class="card-text"><?php echo $remedy['description']; ?></p>
                    <div class="card-meta">
                        <div class="meta-item"><i class="fas fa-clock"></i> 5 mins</div>
                        <div class="meta-item"><i class="fas fa-leaf"></i> 100% Natural</div>
                    </div>
                    <button class="add-remedy-btn" onclick="event.stopPropagation(); quickAddRemedyFromCard('<?php echo addslashes($remedy['title']); ?>', '<?php echo addslashes($remedy['description']); ?>', '<?php echo addslashes($remedy['instructions']); ?>', this)">
                        <i class="fas fa-leaf"></i> Add to Dashboard
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Remedy Detail Modal -->
    <div id="remedyModal" class="modal">
        <div class="modal-content">
            <div class="close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </div>
            
            <div class="modal-main-content">
                <div class="modal-header">
                    <img id="modalImg" src="" class="modal-img">
                </div>
                <div class="modal-body">
                    <span id="modalCategory" class="modal-category">Category</span>
                    <h2 id="modalTitle" class="modal-title">Remedy Title</h2>
                    <p id="modalDesc" style="color: #64748b; font-size: 18px; margin-bottom: 32px; line-height: 1.6;"></p>
                    
                    <div class="section-title">
                        <i class="fas fa-shopping-basket" style="color: #10b981;"></i> Ingredients
                    </div>
                    <ul id="modalIngredients" class="ingredients-list">
                        <!-- Ingredients -->
                    </ul>

                    <div class="section-title">
                        <i class="fas fa-fire" style="color: #f59e0b;"></i> Preparation
                    </div>
                    <div class="instruction-box">
                        <p id="modalInstructions"></p>
                    </div>
                </div>
            </div>
            
            <!-- Sticky Footer with Add Button -->
            <div class="modal-footer">
                <button class="slide-btn" onclick="addToMyRemedies()" style="width: 100%; justify-content: center; margin: 0;">
                    <i class="fas fa-plus-circle"></i> Add to My Dashboard
                </button>
                <p id="addSuccess" style="color: #10b981; font-weight: 700; margin-top: 12px; display: none; font-size: 14px; text-align: center; animation: fadeInUp 0.5s ease;"></p>
            </div>
        </div>
    </div>

    <!-- Data for JS -->
    <script>
        const remediesData = <?php echo json_encode($remedies); ?>;
        let slideIndex = 1;
        let autoSlideInterval;

        showSlides(slideIndex);
        startAutoSlide();

        function startAutoSlide() {
            autoSlideInterval = setInterval(() => {
                moveSlide(1, true); // true means it was triggered by auto-play
            }, 6000); // 6 seconds
        }

        function pauseAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        function resumeAutoSlide() {
            startAutoSlide();
        }

        function moveSlide(n, isAuto = false) {
            showSlides(slideIndex += n);
            if (!isAuto) {
                // If user clicks, we restart the timer
                pauseAutoSlide();
                startAutoSlide();
            }
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
            pauseAutoSlide();
            startAutoSlide();
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("slide");
            let dots = document.getElementsByClassName("dot");
            
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            
            for (i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
                slides[i].style.display = "none";  
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].classList.remove("active");
            }
            
            slides[slideIndex-1].classList.add("active");
            slides[slideIndex-1].style.display = "flex";  
            if (dots[slideIndex-1]) {
                dots[slideIndex-1].classList.add("active");
            }
        }

        function filterRemedies(category) {
            const cards = document.querySelectorAll('.remedy-card');
            cards.forEach(card => {
                if(category === 'all' || card.dataset.category === category) {
                    card.style.display = 'flex';
                    // Trigger reflow/animation
                    card.style.animation = 'none';
                    card.offsetHeight; /* trigger reflow */
                    card.style.animation = 'fadeIn 0.5s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Modal Logic
        const modal = document.getElementById('remedyModal');
        let currentRemedy = null;

        function openModal(index) {
            const data = remediesData[index];
            currentRemedy = data;
            
            const imgEl = document.getElementById('modalImg');
            imgEl.src = data.image;
            imgEl.onerror = function() {
                this.src = 'https://placehold.co/800x600/e2e8f0/475569?text=' + encodeURIComponent(data.title);
            };

            document.getElementById('modalCategory').textContent = data.category;
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalDesc').textContent = data.description;
            document.getElementById('modalInstructions').textContent = data.instructions;
            
            const ingList = document.getElementById('modalIngredients');
            ingList.innerHTML = '';
            data.ingredients.forEach(ing => {
                const li = document.createElement('li');
                li.innerHTML = '<i class="fas fa-check-circle"></i> ' + ing;
                ingList.appendChild(li);
            });

            document.getElementById('addSuccess').style.display = 'none';
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        function addToMyRemedies() {
            if(!currentRemedy) return;

            const btn = document.querySelector('.slide-btn[onclick="addToMyRemedies()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('remedy_name', currentRemedy.title);
            formData.append('remedy_notes', currentRemedy.description + '\n\nInstructions: ' + currentRemedy.instructions);

            fetch('../api/add_user_remedy.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const successMsg = document.getElementById('addSuccess');
                    successMsg.innerHTML = '<i class="fas fa-check-circle"></i> Added to your Dashboard!';
                    successMsg.style.display = 'block';
                    btn.innerHTML = '<i class="fas fa-check"></i> Added';
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1200);
                } else {
                    alert('Could not add remedy: ' + data.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        // Quick add remedy from card button
        function quickAddRemedyFromCard(name, description, instructions, btnElement) {
            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            btnElement.disabled = true;

            const formData = new FormData();
            formData.append('remedy_name', name);
            formData.append('remedy_notes', description + (instructions ? '\n\nInstructions: ' + instructions : ''));

            fetch('../api/add_user_remedy.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    btnElement.innerHTML = '<i class="fas fa-check-circle"></i> Added!';
                    btnElement.style.background = 'linear-gradient(135deg, #059669, #047857)';
                    
                    // Show toast notification
                    showToast('✓ Remedy added to your dashboard!', 'success');
                    
                    setTimeout(() => {
                        btnElement.innerHTML = originalText;
                        btnElement.disabled = false;
                        btnElement.style.background = '';
                    }, 2000);
                } else {
                    alert('Could not add remedy: ' + data.message);
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to add remedy. Please try again.');
                btnElement.innerHTML = originalText;
                btnElement.disabled = false;
            });
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                z-index: 9999;
                font-weight: 600;
                animation: slideInRight 0.3s ease;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Add CSS animations for toast
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
    <!-- Shared Footer -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>
