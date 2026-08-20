<?php
require_once 'config.php';
protect_page();

$currentYear = date("Y");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | About Us</title>
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
            background-color: var(--white);
            color: var(--charcoal);
            line-height: 1.8;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Responsive Navbar & Footer */
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

        /* HERO SECTION WITH VIDEO BACKGROUND */
        .page-hero {
            position: relative;
            height: 65vh; /* Increased height for mobile */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 5rem 0;
            border-bottom: 3px solid var(--gold);
            overflow: hidden;
        }
        .page-hero video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            object-fit: cover; /* Ensures video covers the area entirely */
            z-index: 0;
            transform: translate(-50%, -50%);
            /* Apply blur and dimming filter */
            filter: blur(8px) brightness(0.6);
        }
        .page-hero .container {
            position: relative;
            z-index: 1;
            color: var(--white);
            text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
        }
        .page-hero h1 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: var(--light-gold);
            letter-spacing: 2px;
            font-size: 2.5rem; /* Mobile size */
        }
        .page-hero p { 
            font-style: italic; 
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        /* Desktop H1 size and Hero height adjustment */
        @media (min-width: 768px) {
            .page-hero {
                height: 80vh; /* Increased height for desktop/PC */
            }
            .page-hero h1 { font-size: 4rem; }
            .page-hero p { font-size: 1.5rem; }
        }

        /* Content Sections */
        .content-section { padding: 3rem 0; flex-grow: 1; }
        .content-section h2 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: var(--charcoal);
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            display: inline-block;
            font-size: 1.8rem; /* Mobile size */
        }
        .gold-separator {
            height: 3px; width: 60px; background: var(--luxury-gradient); margin: 0.5rem 0 1.5rem;
        }
        .about-img {
            border: 3px solid var(--gold);
            padding: 10px;
            background: var(--luxury-gradient);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .about-img img { display: block; width: 100%; height: auto; }

        /* Feature Icons on Mobile: Stack features horizontally on large screens, vertical on mobile */
        @media (max-width: 767px) {
            .content-section { padding: 3rem 0; }
            .content-section h2 { font-size: 2rem; }
            .feature-card { margin-bottom: 2rem; }
            .col-md-4 { margin-bottom: 1.5rem; } /* Add spacing between vertical stacks */
        }
        
        .text-gold { color: var(--gold) !important; }
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
                    <li class="nav-item"><a class="nav-link active-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <!-- Standardized Order Links -->
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-hero text-center">
        <!-- Video Background Element -->
        <video autoplay muted loop playsinline poster="bg_fallback.jpg">
            <!-- Assuming 'bg.mp4' is in the same directory -->
            <source src="bg.mp4" type="video/mp4">
            <!-- Fallback image if video fails -->
            <img src="https://placehold.co/1920x1080/1c1c1c/daa520?text=Scentified" alt="Scentified Luxury Video Background">
        </video>
        <div class="container">
            <h1 class="display-4">Our Legacy, Your Fragrance</h1>
            <p class="lead">Crafting distinction since our first bloom.</p>
        </div>
    </header>

    <section class="content-section">
        <div class="container">
            <!-- Mobile-First: Stacks image above text, aligns items to center on small screens -->
            <div class="row align-items-center g-5">
                <div class="col-12 col-lg-6 order-lg-2">
                    <h2>The Genesis of Scentified</h2>
                    <div class="gold-separator"></div>
                    <p class="lead text-muted">A singular vision to distill true luxury.</p>
                    <p>Our founders, master perfumers with generations of expertise, believed that a fragrance should tell a story of elegance, power, and timeless allure. We source the world's most exquisite, rare ingredients—from Calabrian bergamot to Madagascan vanilla—ensuring every note is of unparalleled purity.</p>
                    <p>Our commitment is to the art of fine fragrance, rejecting mass production for meticulous, small-batch crafting that honors tradition.</p>
                </div>
                <div class="col-12 col-lg-6 order-lg-1">
                    <div class="about-img">
                        <img src="https://images.unsplash.com/photo-1615634260167-c8cdede054de?q=80&w=800&auto=format&fit=crop" alt="Artisan perfumer working with ingredients" onerror="this.onerror=null;this.src='https://placehold.co/800x600/1c1c1c/daa520?text=Artisan+Crafting';">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                 <h2>Pillars of Excellence</h2>
                 <div class="gold-separator mx-auto"></div>
            </div>
            <div class="row text-center g-4">
                <!-- Columns stack on mobile (col-12) and go side-by-side on tablet/desktop (col-md-4) -->
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-certificate fa-3x mb-4 text-gold"></i>
                        <h5 style="font-family: 'Cinzel', serif;">Unrivaled Purity</h5>
                        <p class="text-muted">We use only natural absolutes and essential oils, ensuring a scent that evolves beautifully on the skin.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-gem fa-3x mb-4 text-gold"></i>
                        <h5 style="font-family: 'Cinzel', serif;">Artisan Craftsmanship</h5>
                        <p class="text-muted">Each bottle is hand-finished and aged to perfection, following centuries-old European traditions.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-leaf fa-3x mb-4 text-gold"></i>
                        <h5 style="font-family: 'Cinzel', serif;">Sustainable Luxury</h5>
                        <p class="text-muted">Our dedication to elegance is matched only by our commitment to ethical sourcing and environmental stewardship.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-custom py-5 mt-auto">
        <div class="container text-white">
            <div class="row">
                <!-- Standard Footer Columns: col-12 stacking on mobile, col-md-4 side-by-side on tablet/desktop -->
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h5 class="navbar-brand-text mb-3">Scentified</h5>
                    <p class="text-white-50">The Essence of Elegance. Crafted for your distinction.</p>
                </div>
                <div class="col-12 col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif;">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="home.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="about.php" class="text-white-50 text-decoration-none">Our Story</a></li>
                        <li><a href="faqs.php" class="text-white-50 text-decoration-none">Help & Support</a></li>
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>