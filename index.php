<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['role'] : null;

// If logged in, redirect to appropriate dashboard
if ($isLoggedIn) {
    switch ($userRole) {
        case 'Admin':
            header("Location: /MedMS/src/dashboard/admin/index.php");
            break;
        case 'Doctor':
        case 'Nurse':
            header("Location: /MedMS/src/dashboard/medical/index.php");
            break;
        case 'Teacher':
        case 'Student':
            header("Location: /MedMS/src/dashboard/patient/index.php");
            break;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedMS - Medical Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/MedMS/styles/variables.css">
    <link rel="stylesheet" href="/MedMS/styles/global.css">
    
    <style>
        /* Landing Page Specific Styles */
        body {
            padding-top: 0;
            background-color: var(--primary-color);
        }
        
        .navbar {
            transition: all var(--transition-normal);
            padding: 1rem 0;
        }
        
        .navbar.scrolled {
            background-color: var(--header-bg) !important;
            box-shadow: var(--shadow-md);
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            font-size: 1.75rem;
            color: var(--text-primary) !important;
        }
        
        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            margin: 0 0.5rem;
        }
        
        a img {
            width: 60px;
            height: 60px;
        }

        .nav-link:hover {
            color: var(--text-primary) !important;
        }
        
        .hero {
            position: relative;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--primary-color) 100%);
            padding: 8rem 0 6rem;
            color: var(--text-primary);
            overflow: hidden;
        }
        
        .hero-shape {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }
        
        .hero-shape svg {
            display: block;
            width: calc(100% + 1.3px);
            height: 72px;
        }
        
        .hero-shape .shape-fill {
            fill: var(--secondary-color);
        }
        
        .section-heading {
            position: relative;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
        }
        
        .section-heading::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .feature-card {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: var(--border-radius-md);
            margin-bottom: 2rem;
            height: 100%;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-normal);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            background-color: var(--accent-color);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .feature-icon i {
            font-size: 2.5rem;
            color: var(--text-primary);
        }
        
        .about-section {
            background-color: var(--secondary-color);
        }
        
        .contact-form {
            background-color: var(--card-bg);
            border-radius: var(--border-radius-md);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }
        
        footer {
            background-color: var(--primary-color);
            color: var(--text-secondary);
        }
        
        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            transition: all var(--transition-fast);
        }
        
        .social-icon:hover {
            background-color: var(--info-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top bg-transparent">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" height="60" class="me-2">MedMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-lg-3" href="/MedMS/auth/login.php">Log In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" height="100" class="mb-4">
                    <h1 class="display-4 fw-bold mb-4">Medical Management System for Schools</h1>
                    <p class="lead mb-5">A comprehensive solution for managing health services, medical records, and prescriptions in educational institutions.</p>
                    <div>
                        <a href="/MedMS/auth/login.php" class="btn btn-primary btn-lg me-2">Log In</a>
                        <a href="/MedMS/auth/signup.php" class="btn btn-outline-light btn-lg">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-shape">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 mt-5" id="features">
        <div class="container">
            <h2 class="section-heading">Key Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4>Medical Staff Management</h4>
                        <p>Efficiently manage doctors and nurses, track their availability, and schedule appointments.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <h4>Electronic Health Records</h4>
                        <p>Securely store and access student and staff medical records, including history and medications.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-prescription"></i>
                        </div>
                        <h4>Prescription Management</h4>
                        <p>Create, manage, and track prescriptions and medications for students and staff.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>AI Integration</h4>
                        <p>Access AI-powered consultations for first aid advice and basic health information.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Statistics & Reports</h4>
                        <p>Generate detailed reports and visualize medical data to improve health services.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Secure Access</h4>
                        <p>Role-based access control ensures that users can only access appropriate information.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 about-section" id="about">
        <div class="container">
            <h2 class="section-heading">About MedMS</h2>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://source.unsplash.com/random/600x400/?healthcare" alt="About MedMS" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h3 class="mb-4">Why Choose MedMS?</h3>
                    <p>MedMS is designed specifically for educational institutions to streamline their medical management processes. Our system helps schools provide better healthcare services to students and staff.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Easy-to-use interface for all user roles</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Comprehensive medical record management</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Seamless communication between patients and medical staff</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Advanced reporting and analytics</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Secure and HIPAA-compliant data storage</li>
                    </ul>
                    <a href="auth/signup.php" class="btn btn-primary mt-3">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contact">
        <div class="container">
            <h2 class="section-heading">Contact Us</h2>
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <form class="contact-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Your Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="your.email@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Your Message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">MedMS</h5>
                    <p>Medical Management System for Schools</p>
                    <div class="d-flex mt-3">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none">Features</a></li>
                        <li class="mb-2"><a href="#about" class="text-secondary text-decoration-none">About</a></li>
                        <li class="mb-2"><a href="#contact" class="text-secondary text-decoration-none">Contact</a></li>
                        <li class="mb-2"><a href="/MedMS/auth/login.php" class="text-secondary text-decoration-none">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 School Street, City, Country</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 234 567 8901</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@medms.edu</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> MedMS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS for smooth scrolling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for all anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const navbarHeight = document.querySelector('.navbar').offsetHeight;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Change navbar background on scroll
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</body>
</html>
