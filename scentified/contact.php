<?php
require_once 'config.php';
protect_page();  

$message = '';
$messageType = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']) &&
        !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $message = "Thank you, " . htmlspecialchars($_POST['name']) . ". Your luxury inquiry has been received.";
            $messageType = 'success';
        } else {
            $message = "Error: Please provide a valid email address.";
            $messageType = 'danger';
        }
    } else {
        $message = "Error: All fields are required.";
        $messageType = 'danger';
    }
}
$currentYear = date("Y");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Cinzel:wght@400..700&display=swap" rel="stylesheet">
 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --charcoal: #1c1c1c;
            --gold: #daa520;
            --light-gold: #f0e68c;
            --white: #ffffff;
            --luxury-gradient: linear-gradient(135deg, #0a0a0a 0%, #3a2c0f 100%);
        }

        body {
            font-family: 'Playfair Display', serif;
            background-color: #f8f9fa;  
            color: var(--charcoal);
            line-height: 1.8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

 
        .navbar-custom, .footer-custom {
            background: var(--luxury-gradient);
            border-bottom: 1px solid var(--gold);
        }
        .navbar-custom .nav-link, .footer-custom .text-white {
            color: rgba(255, 255, 255, 0.85) !important;
            font-family: 'Cinzel', serif;
            letter-spacing: 1.5px;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover, .footer-custom a:hover { color: var(--gold) !important; }
        .navbar-custom .nav-link.active-link { color: var(--gold) !important; }
 
        .navbar-brand-container {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .navbar-logo {
            max-height: 40px;
            width: auto;
            margin-right: 10px;  
            transition: transform 0.3s ease;
        }
        .navbar-logo:hover {
            transform: scale(1.05);
        }
        .navbar-brand-text {
            color: var(--gold) !important;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
        }

        /* Responsive Headings */
        .contact-header {
            background: var(--luxury-gradient);
            color: var(--white);
            padding: 4rem 0; /* Adjusted padding for mobile */
            border-bottom: 3px solid var(--gold);
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .contact-header h1 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: var(--light-gold);
            letter-spacing: 2px;
            font-size: 2.5rem; /* Mobile Size */
        }
        @media (min-width: 768px) {
            .contact-header { padding: 5rem 0; }
            .contact-header h1 { font-size: 4rem; }
        }

         .form-card {
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2rem; /* Adjusted padding for mobile */
            background-color: var(--white);
            border-top: 5px solid var(--gold);
        }
        @media (min-width: 768px) {
            .form-card { padding: 3rem; }
        }
        
        .form-label { font-family: 'Cinzel', serif; font-weight: 600; color: var(--charcoal); }
        .form-control, .form-select {
            border-radius: 0;
            border: 1px solid #ced4da;
            padding: 0.8rem 1rem;
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25);
        }
        .btn-submit {
            background: var(--luxury-gradient);
            color: var(--gold);
            border: 1px solid var(--gold);
            padding: 1rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background: var(--gold);
            color: var(--charcoal);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.4);
        }
        .btn-outline-gold {
            color: var(--gold);
            border: 2px solid var(--gold);
            transition: all 0.3s ease;
            font-family: 'Cinzel', serif;
            font-weight: 600;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--charcoal);
        }


         .modal-header-custom { background: var(--luxury-gradient); color: var(--gold); }
        .modal-content { border: 2px solid var(--gold); border-radius: 0; }
        .text-gold { color: var(--gold) !important; }
        .map-iframe {
            border: 2px solid var(--gold);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .review-card {
             border: 1px solid rgba(218, 165, 32, 0.3);
             border-left: 5px solid var(--gold);
             transition: all 0.2s ease;
        }
        .review-card:hover {
             border-color: var(--gold);
             box-shadow: 0 5px 10px rgba(218, 165, 32, 0.1);
        }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
             <a class="navbar-brand navbar-brand-container" href="home.php">
                <img src="slogo.jpg" alt="Scentified Luxury Perfume Logo" class="navbar-logo" onerror="this.onerror=null;this.src='https://placehold.co/40x40/1c1c1c/daa520?text=S';">
                <span class="navbar-brand-text">Scentified</span>
            </a>
 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="color: var(--gold);"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link active-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <!-- Updated: Removed Checkout link -->
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="contact-header text-center">
        <div class="container">
            <h1 class="display-4">A Dialogue of Elegance</h1>
            <p class="lead text-white-50">We invite your inquiries and bespoke requests.</p>
        </div>
    </header>

    <main class="flex-grow-1">
        <!-- Contact Form Section -->
        <section class="py-5 mt-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card form-card">
                            <div class="card-body">
                                <h3 class="text-center mb-5" style="font-family: 'Cinzel', serif;">Send Us Your Inquiry</h3>

                                <?php if ($message): ?>
                                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form id="contactForm" method="POST" action="contact.php" class="needs-validation" novalidate>
                                    <div class="row g-3">
                                        <!-- Mobile-First: col-12 always, col-md-6 on medium screens -->
                                        <div class="col-12 col-md-6">
                                            <label for="name" class="form-label">Full Name <span class="text-gold">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                            <div class="invalid-feedback">Required.</div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="email" class="form-label">Email Address <span class="text-gold">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback">Valid email required.</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="subject" class="form-label">Subject</label>
                                            <select class="form-select" id="subject" name="subject">
                                                <option value="General">General Inquiry</option>
                                                <option value="Bespoke">Bespoke Fragrance Request</option>
                                                <option value="Order">Order Status</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="message" class="form-label">Message <span class="text-gold">*</span></label>
                                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                            <div class="invalid-feedback">Message required.</div>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-submit w-100 btn-lg">Submit Inquiry</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map and Reviews Section -->
        <section class="py-5 bg-white">
            <div class="container">
                <!-- Columns stack on mobile (default col-12) and go side-by-side on large screens (col-lg-6) -->
                <div class="row g-5">
                    <!-- Location Map (Google Maps Like) -->
                    <div class="col-12 col-lg-6">
                        <h3 class="mb-4" style="font-family: 'Cinzel', serif; font-weight: 700;">Our Flagship Location</h3>
                        <div class="map-iframe">
                            <iframe 
                                width="100%" 
                                height="350" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q=145%20Luxury%20Lane,%20Perfume%20City,%2090210&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <p class="mt-3 text-muted">Our Main Headquarters</p>
                    </div>
                    
                    <!-- User Reviews and Feedback -->
                    <div class="col-12 col-lg-6">
                        <h3 class="mb-4" style="font-family: 'Cinzel', serif; font-weight: 700;">Customer Feedback</h3>
                        <div class="review-container">
                            
                            <!-- Mock Review 1 -->
                            <div class="review-card p-3 mb-3">
                                <div class="stars mb-2">
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                </div>
                                <p class="mb-1 fw-bold" style="font-family: 'Cinzel', serif; font-size: 1.1rem;">Aura Mystique is perfect!</p>
                                <p class="text-muted small mb-0">"The most sophisticated scent I've ever owned. Long-lasting and truly luxurious packaging." - Jane D.</p>
                            </div>
                            
                            <!-- Mock Review 2 -->
                            <div class="review-card p-3 mb-3">
                                <div class="stars mb-2">
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="fas fa-star text-gold"></i>
                                    <i class="far fa-star text-gold"></i>
                                </div>
                                <p class="mb-1 fw-bold" style="font-family: 'Cinzel', serif; font-size: 1.1rem;">Excellent service</p>
                                <p class="text-muted small mb-0">"Fast shipping and the gift wrap was beautiful. Only four stars as I wish the sample size was bigger!" - Mark S.</p>
                            </div>
                            
                            <a href="#" class="btn btn-sm btn-outline-gold mt-3">Submit Your Review</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" style="font-family: 'Cinzel', serif;">Inquiry Received</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-gold mb-3"></i>
                    <p class="lead">Thank you for reaching out.</p>
                    <p class="text-muted">A representative will review your request shortly.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-custom py-5 mt-auto">
        <div class="container text-white">
            <div class="row">
                <!-- Footer columns stacked on mobile -->
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h5 class="navbar-brand-text mb-3">Scentified</h5>
                    <p class="text-white-50">The Essence of Elegance. Crafted for your distinction.</p>
                </div>
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif;">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="home.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="about.php" class="text-white-50 text-decoration-none">Our Story</a></li>
                        <li><a href="faqs.php" class="text-white-50 text-decoration-none">Help &amp; Support</a></li>
                        <li><a href="contact.php" class="text-white-50 text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4">
                    <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif;">Connect</h5>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-envelope fa-fw text-gold me-2"></i> Email: scentified@gmail.com</li>
                        <li><i class="fas fa-phone fa-fw text-gold me-2"></i> Phone: +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt fa-fw text-gold me-2"></i> Address: 145 Luxury Lane, Perfume City, 90210</li>
                    </ul>
                    <p class="mt-3">
                        <a href="#" class="text-gold me-3" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-gold me-3" aria-label="Facebook"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-gold" aria-label="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    </p>
                </div>
            </div>
            <hr class="my-4" style="border-color: var(--gold); opacity: 0.3;">
            <div class="text-center text-white-50 small">
                &copy; <?php echo $currentYear; ?> Scentified. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        (function () {
            'use strict'
            const form = document.getElementById('contactForm');
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            <?php if ($messageType === 'success'): ?>
                document.addEventListener('DOMContentLoaded', function() {
                    successModal.show();
                    if (window.history.replaceState) window.history.replaceState(null, null, window.location.pathname);
                });
            <?php endif; ?>
        })();
    </script>
</body>
</html>