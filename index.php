<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeeFlow - The Ultimate Fee Management System for Schools & Institutes</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #dc2626;
            --primary-light: #fee2e2;
            --dark-blue: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            overflow-x: hidden;
            padding-bottom: 0;
        }

        /* Navbar */
        .navbar {
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            z-index: 1000;
            border-bottom: 1px solid #f1f5f9;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        /* Hero Section */
        .hero {
            padding: 6rem 5% 10rem;
            text-align: center;
            background: radial-gradient(circle at top right, #fff1f2, transparent),
                        radial-gradient(circle at bottom left, #f8fafc, transparent);
            position: relative;
        }

        .hero-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--dark-blue);
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 3rem;
        }

        .hero-img {
            max-width: 1000px;
            width: 90%;
            margin: -6rem auto 0;
            border-radius: 2rem;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.15);
            position: relative;
            z-index: 10;
            border: 8px solid white;
            display: block;
        }

        /* Features */
        .features {
            padding: 10rem 5%;
            background: var(--dark-blue);
            color: white;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 2.5rem;
            border-radius: 1.5rem;
            transition: all 0.3s;
        }

        .feature-card:hover {
            background: rgba(255,255,255,0.06);
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .feature-card p {
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Stats */
        .stats-banner {
            background: var(--primary);
            color: white;
            padding: 4rem 5%;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
            text-align: center;
        }

        .stat-item h4 {
            font-size: 3rem;
            font-weight: 800;
        }

        .stat-item p {
            font-weight: 600;
            opacity: 0.9;
        }

        /* CTA */
        .cta-section {
            padding: 8rem 5%;
            text-align: center;
        }

        .cta-card {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #1e1b4b 100%);
            padding: 5rem 2rem;
            border-radius: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-card::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.2;
        }

        .cta-card h2 {
            font-size: 3rem;
            color: white;
            margin-bottom: 2rem;
        }

        footer {
            padding: 4rem 5%;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .hero { padding-top: 4rem; }
            .hero-img { margin-top: -3rem; }
            .cta-card h2 { font-size: 2rem; }
            .navbar { padding: 1rem 5%; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="#" class="logo">
            <i class="fas fa-wallet"></i> FeeFlow
        </a>
        <div class="nav-buttons">
            <a href="login.php" class="btn btn-secondary">Login</a>
            <a href="register.php" class="btn btn-primary">Get Started</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-badge">Rocketing Efficiency to 200%</div>
        <h1>Fee Management for <br><span style="color: var(--primary);">Schools & Coaching Centers</span></h1>
        <p>The smartest way for K-12 schools, coaching institutes, and training centers to track fees, manage students, and generate professional digital receipts.</p>
        
        <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 4rem;">
            <a href="register.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 100px;">
                Start Free Trial <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#pricing" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 100px;">
                View Pricing
            </a>
        </div>
    </section>

    <!-- Update the image path after generation -->
    <img src="assets/img/hero-preview.png" alt="FeeFlow Dashboard" class="hero-img">

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section" style="padding: 10rem 5%; background: white;">
        <div class="section-header">
            <h2 style="color: var(--dark-blue);">Simple, Transparent Pricing</h2>
            <p>Choose the plan that fits your institute's scale. No hidden fees.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem; max-width: 900px; margin: 0 auto;">
            <!-- Monthly Plan -->
            <div class="glass-card" style="padding: 3rem 2rem; border-radius: 2rem; position: relative;">
                <h3 style="margin-bottom: 1rem; color: var(--dark-blue);">Monthly Plan</h3>
                <div style="display: flex; align-items: baseline; gap: 4px; margin-bottom: 2rem;">
                    <span style="font-size: 3rem; font-weight: 800; color: var(--primary);">₹199</span>
                    <span style="color: var(--text-muted); font-weight: 600;">/month</span>
                </div>
                <ul style="list-style: none; padding: 0; margin-bottom: 3rem; text-align: left;">
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Full Feature Access</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Unlimited Students</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Professional Receipts</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> 24/7 Support</li>
                </ul>
                <a href="register.php" class="btn btn-secondary btn-block" style="border-radius: 100px; padding: 1rem;">Get Started</a>
            </div>

            <!-- Yearly Plan -->
            <div class="glass-card" style="padding: 3rem 2rem; border-radius: 2rem; border: 2px solid var(--primary); background: #fffbff; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 20px; right: -35px; background: var(--primary); color: white; padding: 5px 40px; transform: rotate(45deg); font-size: 0.75rem; font-weight: 800;">BEST VALUE</div>
                <h3 style="margin-bottom: 1rem; color: var(--dark-blue);">Yearly Professional</h3>
                <div style="display: flex; align-items: baseline; gap: 4px; margin-bottom: 1rem;">
                    <span style="font-size: 3rem; font-weight: 800; color: var(--primary);">₹1,999</span>
                    <span style="color: var(--text-muted); font-weight: 600;">/year</span>
                </div>
                <p style="color: var(--primary); font-weight: 700; font-size: 0.9rem; margin-bottom: 2rem;">Save ~₹400 per year!</p>
                <ul style="list-style: none; padding: 0; margin-bottom: 3rem; text-align: left;">
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Priority Support</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Custom Branding</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> Data Export Tools</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i> All Features Included</li>
                </ul>
                <a href="register.php" class="btn btn-primary btn-block" style="border-radius: 100px; padding: 1rem;">Go Yearly</a>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="section-header">
            <h2>Everything you need to scale</h2>
            <p style="color: #94a3b8;">Ditch the paperwork and embrace digital transformation.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Instant Collection</h3>
                <p>Record fee payments in seconds. Multiple payment method support with automated student ledger updates.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-file-invoice"></i></div>
                <h3>Smart Receipts</h3>
                <p>Generate professional PDF receipts instantly with your school's branding and digital signatures.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Advanced Analytics</h3>
                <p>Track your daily, monthly and annual collections with beautiful charts and data-driven insights.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-id-card"></i></div>
                <h3>ID Card Generator</h3>
                <p>Built-in high-quality ID card design and export tool for all your students.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users-gear"></i></div>
                <h3>Student Management</h3>
                <p>Manage thousands of student profiles, category-wise fee structures, and attendance effortlessly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Bank-Grade Security</h3>
                <p>Your data is encrypted and backed up daily. Secure login and specialized institute-wide access control.</p>
            </div>
        </div>
    </section>

    <!-- Targeted Institutes Section -->
    <section class="institutes-section" style="padding: 10rem 5%; background: #f8fafc;">
        <div class="section-header">
            <h2 style="color: var(--dark-blue);">The Perfect Fit for Every Institute</h2>
            <p>FeeFlow is designed to adapt to the unique needs of diverse educational and training centers.</p>
        </div>
        <div class="feature-grid">
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-atom" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">IIT-JEE & NEET</h3>
                <p style="color: var(--text-muted);">Manage high-volume fee installments and course materials effortlessly.</p>
            </div>
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-laptop-code" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Computer Centers</h3>
                <p style="color: var(--text-muted);">Track short-term course fees and certificate issuance dates easily.</p>
            </div>
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-music" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Music & Dance</h3>
                <p style="color: var(--text-muted);">Manage flexible class schedules and monthly training fees per student.</p>
            </div>
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-language" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">IELTS & English</h3>
                <p style="color: var(--text-muted);">Streamline registration fees and study material costs for language learners.</p>
            </div>
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-basketball-ball" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Sports Academies</h3>
                <p style="color: var(--text-muted);">Track membership subscriptions and equipment maintenance fees.</p>
            </div>
            <div class="glass-card" style="padding: 2rem; text-align: center;">
                <i class="fas fa-palette" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Art & Craft</h3>
                <p style="color: var(--text-muted);">Simplified fee collection for hobby classes and workshop sessions.</p>
            </div>
        </div>
    </section>

    <div class="stats-banner">
        <div class="stat-item">
            <h4>500+</h4>
            <p>Institutes Trusts Us</p>
        </div>
        <div class="stat-item">
            <h4>100k+</h4>
            <p>Receipts Generated</p>
        </div>
        <div class="stat-item">
            <h4>99.9%</h4>
            <p>Platform Uptime</p>
        </div>
        <div class="stat-item">
            <h4>24/7</h4>
            <p>Expert Support</p>
        </div>
    </div>

    <section class="cta-section">
        <div class="cta-card">
            <h2>Ready to transform your institute?</h2>
            <p style="margin-bottom: 3rem; font-size: 1.25rem;">Join hundreds of progressive educators who have optimized their operations with FeeFlow.</p>
            <a href="register.php" class="btn btn-primary" style="padding: 1.25rem 3.5rem; font-size: 1.2rem; border-radius: 100px; background: white; color: var(--dark-blue);">
                Create Account Now
            </a>
        </div>
    </section>

    <footer style="background: var(--dark-blue); color: white; padding: 5rem 5% 2rem; text-align: left;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; margin-bottom: 4rem;">
            <div>
                <a href="#" class="logo" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-wallet"></i> FeeFlow
                </a>
                <p style="color: #94a3b8; line-height: 1.6;">Empowering educational institutions with modern financial tools to simplify fee management and enhance transparency.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 1.5rem;">Quick Links</h4>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #94a3b8; text-decoration: none;">Home</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="#pricing" style="color: #94a3b8; text-decoration: none;">Pricing</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="register.php" style="color: #94a3b8; text-decoration: none;">Register</a></li>
                    <li style="margin-bottom: 0.75rem;"><a href="login.php" style="color: #94a3b8; text-decoration: none;">Login</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 1.5rem;">Contact Us</h4>
                <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem;">
                    <i class="fas fa-location-dot" style="color: var(--primary); margin-top: 4px;"></i>
                    <p style="color: #94a3b8;">OfferPlant Technologies Pvt. Ltd.<br>2nd Floor Godrej Building, Salempur Chapra Bihar 841301</p>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <i class="fas fa-envelope" style="color: var(--primary);"></i>
                    <a href="mailto:ask@offerplant.com" style="color: #94a3b8; text-decoration: none;">ask@offerplant.com</a>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <i class="fas fa-phone" style="color: var(--primary);"></i>
                    <a href="tel:+919431426600" style="color: #94a3b8; text-decoration: none;">+91 9431426600</a>
                </div>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 2rem; text-align: center; color: #64748b; font-size: 0.875rem;">
            <p>&copy; <?php echo date('Y'); ?> FeeFlow by OfferPlant. All rights reserved. Designed for Excellence.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
