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
            <a href="index.php" class="btn btn-secondary">Login</a>
            <a href="register.php" class="btn btn-primary">Get Started</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-badge">Rocketing Efficiency to 200%</div>
        <h1>Managing School Fees<br><span style="color: var(--primary);">Made Effortless</span></h1>
        <p>The smartest way for schools and coaching centers to track, manage, and collect fees with professional digital receipts and real-time analytics.</p>
        
        <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 4rem;">
            <a href="register.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 100px;">
                Start Free Trial <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#features" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 100px;">
                View Features
            </a>
        </div>
    </section>

    <!-- Update the image path after generation -->
    <img src="assets/img/hero-preview.png" alt="FeeFlow Dashboard" class="hero-img">

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

    <footer>
        <p>&copy; <?php echo date('Y'); ?> FeeFlow. All rights reserved. Designed for Excellence.</p>
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
