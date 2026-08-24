<!-- ========================================
   VITALBALANCE MODERN HEADER COMPONENT
   Green + Beige Theme | SVG Icons | Sticky
======================================== -->
    <!-- Font for Logo -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
    /* Modern Header Styles */
    .vb-header {
        background: #1B3C1A; /* Deep Forest Green */
        color: var(--text-white);
        position: sticky;
        top: 0;
        z-index: var(--z-sticky);
        box-shadow: var(--shadow-md);
        border-bottom: 2px solid var(--accent-gold);
    }

    .vb-header-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0.8rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }

    .vb-logo-section {
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }

    .vb-logo-icon {
        width: 52px;
        height: 52px;
        background: var(--accent-gold);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .vb-logo-icon svg {
        width: 32px;
        height: 32px;
        fill: #1B3C1A;
    }

    .vb-logo-text {
        display: flex;
        flex-direction: column;
    }

    .vb-logo-title {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-white);
        line-height: 1.1;
    }

    .vb-logo-subtitle {
        font-size: 10px;
        color: #B4C7B2;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .vb-header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .vb-user-profile {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.4rem 1.2rem 0.4rem 0.6rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 40px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .vb-user-profile:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-2px);
    }

    .vb-user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: white;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .vb-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .vb-user-avatar .initials {
        font-weight: 700;
        color: #1B3C1A;
        font-size: 16px;
    }

    .vb-user-details {
        display: flex;
        flex-direction: column;
    }

    .vb-user-details h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-white);
        margin: 0;
        line-height: 1.2;
    }

    .vb-user-details p {
        font-size: 11px;
        color: #B4C7B2;
        margin: 0;
        font-weight: 500;
    }

    .vb-logout-btn {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.7rem 1.4rem;
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-white);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .vb-logout-btn:hover {
        background: #D32F2F; /* Red for logout hover */
        border-color: #D32F2F;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
    }

    .vb-logout-btn svg {
        width: 18px;
        height: 18px;
    }

    /* Modern Navigation Bar */
    .vb-nav {
        background: #FFFFFF;
        border-bottom: 2px solid #E5E9E2;
        position: sticky;
        top: 75px;
        z-index: 1010;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    .vb-nav-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: center; /* Center navigation items */
        gap: 0.5rem;
    }

    .vb-nav-link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 1.2rem 1.5rem;
        color: #5D725B;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        border-bottom: 4px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .vb-nav-link svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        opacity: 0.8;
    }

    .vb-nav-link:hover {
        color: #2D5A27;
        background: #F8FAF7;
    }

    .vb-nav-link.active {
        color: #2D5A27;
        border-bottom-color: #2D5A27;
    }

    .vb-nav-link.active svg {
        opacity: 1;
    }

    @media (max-width: 1024px) {
        .vb-nav-content {
            overflow-x: auto;
            justify-content: flex-start;
            scrollbar-width: none;
        }
        .vb-nav-content::-webkit-scrollbar { display: none; }
    }

    @media (max-width: 768px) {
        .vb-logo-subtitle { display: none; }
        .vb-user-details { display: none; }
        .vb-header-content { padding: 0.8rem 1rem; }
    }
</style>

<!-- Header -->
<header class="vb-header">
    <div class="vb-header-content">
        <div class="vb-logo-section">
            <div class="vb-logo-icon">
                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Rounded Gold Background -->
                    <rect width="100" height="100" rx="25" fill="#C5A065"/>
                    
                    <!-- Plant Stem -->
                    <path d="M50 82C50 82 50 60 52 25" stroke="#4a6b38" stroke-width="2" stroke-linecap="round"/>
                    
                    <!-- Leaves (Bright Green) -->
                    <path d="M52 70C35 65 30 55 40 50C48 46 52 60 52 70Z" fill="#8BC34A"/>
                    <path d="M52 60C69 55 74 45 64 40C56 36 52 50 52 60Z" fill="#8BC34A"/>
                    <path d="M52 50C38 45 34 35 42 30C48 27 52 40 52 50Z" fill="#8BC34A"/>
                    <path d="M52 40C66 35 70 25 62 20C56 18 52 30 52 40Z" fill="#8BC34A"/>
                    <path d="M52 25C45 15 50 10 55 15C58 18 55 25 52 25Z" fill="#8BC34A"/>
                </svg>
            </div>
            <div class="vb-logo-text">
                <div class="vb-logo-title">VitalBalance</div>
                <div class="vb-logo-subtitle">Personalized Wellness & Chronic Care</div>
            </div>
        </div>
        <div class="vb-header-right">
            <div class="vb-user-profile">
                <div class="vb-user-avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Profile">
                    <?php else: ?>
                        <span class="initials"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="vb-user-details">
                    <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            <a href="../logout.php" class="vb-logout-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="vb-nav">
    <div class="vb-nav-content">
        <a href="dashboard.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="my_diseases.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my_diseases.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            My Diseases
        </a>
        <a href="health_records.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'health_records.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Health Records
        </a>
        <a href="wellness_tracker.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'wellness_tracker.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            Wellness Tracker
        </a>
        <a href="remedies.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'remedies.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Browse Remedies
        </a>
        <a href="report.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>" target="_blank">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Health Report
        </a>
        <a href="profile.php" class="vb-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </a>
    </div>
</nav>
