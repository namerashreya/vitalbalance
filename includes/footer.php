<!-- ========================================
   VITALBALANCE MODERN FOOTER COMPONENT
   Green + Beige Theme | Professional | SVG Icons
======================================== -->
<style>
    /* Modern Footer Styles */
    .vb-footer {
        background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
        color: var(--text-white);
        margin-top: 4rem;
        border-top: 3px solid var(--accent-gold);
    }

    .vb-footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 3rem 2rem 1.5rem;
    }

    .vb-footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-bottom: 2rem;
    }

    .vb-footer-section h3 {
        font-family: var(--font-heading);
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: var(--accent-gold);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .vb-footer-section h3 svg {
        width: 20px;
        height: 20px;
    }

    .vb-footer-section p {
        font-size: 14px;
        line-height: 1.8;
        color: var(--secondary-sage-light);
        margin-bottom: 1rem;
    }

    .vb-footer-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .vb-footer-links a {
        color: var(--secondary-sage-light);
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all var(--duration) var(--ease);
    }

    .vb-footer-links a:hover {
        color: var(--text-white);
        transform: translateX(5px);
    }

    .vb-footer-links svg {
        width: 16px;
        height: 16px;
        opacity: 0.7;
    }

    .vb-contact-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        color: var(--secondary-sage-light);
        font-size: 14px;
    }

    .vb-contact-item svg {
        width: 18px;
        height: 18px;
        color: var(--accent-gold);
    }

    .vb-social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .vb-social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-white);
        transition: all var(--duration) var(--ease);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .vb-social-link:hover {
        background: var(--accent-gold);
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .vb-social-link svg {
        width: 20px;
        height: 20px;
    }

    .vb-footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .vb-footer-copyright {
        font-size: 13px;
        color: var(--secondary-sage-light);
    }

    .vb-footer-legal {
        display: flex;
        gap: 1.5rem;
    }

    .vb-footer-legal a {
        font-size: 13px;
        color: var(--secondary-sage-light);
        text-decoration: none;
        transition: color var(--duration) var(--ease);
    }

    .vb-footer-legal a:hover {
        color: var(--text-white);
    }

    .vb-disclaimer {
        background: rgba(197, 160, 89, 0.1);
        border-left: 3px solid var(--accent-gold);
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        margin-top: 2rem;
    }

    .vb-disclaimer p {
        font-size: 12px;
        line-height: 1.6;
        color: var(--secondary-sage-light);
        margin: 0;
    }

    @media (max-width: 768px) {
        .vb-footer-content {
            padding: 2rem 1rem 1rem;
        }

        .vb-footer-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .vb-footer-bottom {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<!-- Footer -->
<footer class="vb-footer">
    <div class="vb-footer-content">
        <div class="vb-footer-grid">
            <!-- About Section -->
            <div class="vb-footer-section">
                <h3>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    About VitalBalance
                </h3>
                <p>
                    VitalBalance is your trusted companion for personalized wellness and chronic disease management. 
                    We combine modern technology with natural remedies to help you live a healthier, balanced life.
                </p>
                <div class="vb-social-links">
                    <a href="#" class="vb-social-link" aria-label="Facebook">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="vb-social-link" aria-label="Twitter">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="vb-social-link" aria-label="Instagram">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                        </svg>
                    </a>
                    <a href="#" class="vb-social-link" aria-label="LinkedIn">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="vb-footer-section">
                <h3>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Quick Links
                </h3>
                <ul class="vb-footer-links">
                    <li>
                        <a href="dashboard.php">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="my_diseases.php">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            My Diseases
                        </a>
                    </li>
                    <li>
                        <a href="health_records.php">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Health Records
                        </a>
                    </li>
                    <li>
                        <a href="remedies.php">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Natural Remedies
                        </a>
                    </li>
                    <li>
                        <a href="profile.php">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            My Profile
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div class="vb-footer-section">
                <h3>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Support
                </h3>
                <ul class="vb-footer-links">
                    <li>
                        <a href="#">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            FAQs
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="vb-footer-section">
                <h3>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact Information
                </h3>
                <div class="vb-contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    support@vitalbalance.com
                </div>
                <div class="vb-contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    +1 (555) 123-4567
                </div>
                <div class="vb-contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Healthcare District, Wellness City
                </div>
            </div>
        </div>

        <!-- Medical Disclaimer -->
        <div class="vb-disclaimer">
            <p>
                <strong>⚕️ Medical Disclaimer:</strong> VitalBalance is designed for informational and educational purposes only. 
                The content provided on this platform, including natural remedies and health tracking features, is not intended 
                to be a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of your 
                physician or other qualified health provider with any questions you may have regarding a medical condition.
            </p>
        </div>

        <!-- Footer Bottom -->
        <div class="vb-footer-bottom">
            <div class="vb-footer-copyright">
                © <?php echo date('Y'); ?> VitalBalance. All rights reserved. | Crafted with 🌿 for better health.
            </div>
            <div class="vb-footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
