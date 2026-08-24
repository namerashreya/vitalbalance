<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VitalBalance - An Integrated Wellness System for Personalized Chronic Disease Management and Natural Remedies">
    <title>VitalBalance | Your Health. Your Balance. Naturally.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system-v2.css">
    <style>
        /* ========================================
           VITALBALANCE DESIGN SYSTEM
           Theme: Green + Beige (Nature Health)
           Using variables from design-system-v2.css
        ======================================== */

        /* === TYPOGRAPHY === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg-beige);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 16px;
        }

        /* Headings */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-primary);
        }

        h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        h2 { font-size: 2.5rem; margin-bottom: 0.875rem; }
        h3 { font-size: 2rem; margin-bottom: 0.75rem; }
        h4 { font-size: 1.5rem; margin-bottom: 0.625rem; }
        h5 { font-size: 1.25rem; margin-bottom: 0.5rem; }
        h6 { font-size: 1rem; margin-bottom: 0.5rem; }

        p {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--text-secondary);
        }

        /* === BUTTON STYLES === */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: var(--text-white);
            box-shadow: 0 4px 16px rgba(46, 125, 50, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(46, 125, 50, 0.35);
        }

        .btn-secondary {
            background: var(--bg-white);
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }

        .btn-secondary:hover {
            background: var(--bg-light-green);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-green);
            border: 2px solid var(--secondary-sage);
        }

        .btn-outline:hover {
            background: var(--bg-light-green);
            border-color: var(--primary-green);
        }

        /* === CARD STYLES === */
        .card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        /* === INPUT STYLES === */
        .input-field {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #E0E7E4;
            border-radius: var(--radius-md);
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .input-field::placeholder {
            color: var(--text-muted);
        }

        /* === NAVBAR === */
        .navbar {
            background-color: var(--bg-white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: var(--shadow-md);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary-green);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 400;
            margin-top: -2px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-green);
        }

        .nav-link.active {
            color: var(--primary-green);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-green);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-green);
            cursor: pointer;
        }

        /* === HERO SECTION === */
        .hero {
            background: linear-gradient(135deg, var(--bg-light-green) 0%, var(--bg-beige) 100%);
            padding: 5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -15%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(165, 214, 167, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, 30px) rotate(5deg); }
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease;
        }

        .hero-content h1 .highlight {
            color: var(--primary-green);
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .trust-points {
            list-style: none;
            margin-bottom: 2.5rem;
            animation: fadeInUp 0.8s ease 0.4s backwards;
        }

        .trust-point {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 1.05rem;
            color: var(--text-primary);
        }

        .trust-icon {
            width: 48px;
            height: 48px;
            background: var(--bg-white);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-size: 1.25rem;
            box-shadow: var(--shadow-sm);
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.6s backwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-image {
            position: relative;
            animation: fadeIn 1s ease 0.4s backwards;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* === FOOTER === */
        .footer {
            background-color: var(--text-primary);
            color: var(--text-white);
            padding: 4rem 2rem 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-about h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--secondary-sage);
            margin-bottom: 1rem;
        }

        .footer-about p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            width: 42px;
            height: 42px;
            background: rgba(165, 214, 167, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-sage);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary-green);
            color: var(--text-white);
            transform: translateY(-3px);
        }

        .footer-links h4 {
            font-size: 1.1rem;
            color: var(--secondary-sage);
            margin-bottom: 1.25rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        .footer-links a:hover {
            color: var(--secondary-sage);
        }

        .footer-contact p {
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-contact i {
            color: var(--secondary-sage);
        }

        .footer-disclaimer {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            border-left: 4px solid var(--warning);
            margin-bottom: 2rem;
        }

        .footer-disclaimer h5 {
            color: var(--warning);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .footer-disclaimer p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* === RESPONSIVE DESIGN === */
        @media (max-width: 968px) {
            h1 { font-size: 2.5rem; }
            h2 { font-size: 2rem; }
            
            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background-color: var(--bg-white);
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: var(--shadow-md);
                padding: 2rem 0;
                gap: 1.5rem;
            }

            .nav-menu.active {
                left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-image {
                order: -1;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .trust-point {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="logo-section">
                <a href="#" class="logo">VitalBalance</a>
                <span class="tagline">Personalized Wellness & Chronic Care</span>
            </div>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#remedies" class="nav-link">Natural Remedies</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
                <li><a href="login.php" class="btn btn-primary" style="padding: 0.7rem 1.5rem;">Get Started</a></li>
            </ul>

            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1>
                    Your Health. <span class="highlight">Your Balance.</span> Naturally.
                </h1>
                
                <p class="hero-subtitle">
                    Empowering your wellness journey through personalized chronic disease management 
                    combined with evidence-based natural remedies. Experience holistic care that adapts to you.
                </p>

                <ul class="trust-points">
                    <li class="trust-point">
                        <div class="trust-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <span><strong>Personalized Wellness</strong> – Tailored health plans for your unique needs</span>
                    </li>
                    <li class="trust-point">
                        <div class="trust-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span><strong>Secure Health Records</strong> – Your data protected with industry-standard encryption</span>
                    </li>
                    <li class="trust-point">
                        <div class="trust-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <span><strong>Natural Lifestyle Support</strong> – Time-tested remedies backed by research</span>
                    </li>
                </ul>

                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                    <a href="#remedies" class="btn btn-secondary">
                        <i class="fas fa-spa"></i> Explore Remedies
                    </a>
                </div>
            </div>

            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=600&h=700&fit=crop&q=80" 
                     alt="Wellness and Natural Health" 
                     loading="lazy">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- About VitalBalance -->
            <div class="footer-about">
                <h3>VitalBalance</h3>
                <p>
                    An integrated wellness system dedicated to personalized chronic disease management 
                    and natural remedies. Combining modern healthcare with traditional wisdom for your optimal health.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#remedies">Natural Remedies</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>

            <!-- Support Links -->
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Accessibility</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-links footer-contact">
                <h4>Contact Info</h4>
                <p><i class="fas fa-envelope"></i> support@vitalbalance.com</p>
                <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Wellness Ave, Health City</p>
            </div>
        </div>

        <!-- Medical Disclaimer -->
        <div class="footer-disclaimer">
            <h5><i class="fas fa-exclamation-triangle"></i> Medical Disclaimer</h5>
            <p>
                VitalBalance does not provide medical diagnosis, treatment, or professional medical advice. 
                The information provided is for educational purposes only. Always consult a qualified healthcare 
                professional before making any health-related decisions or changes to your treatment plan.
            </p>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 VitalBalance. All rights reserved. | Designed for your wellness journey.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');

        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    navMenu.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
