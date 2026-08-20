<?php
require_once 'config.php';
block_authenticated_access(); 

$currentYear = date("Y");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | Welcome</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .navbar-custom .nav-link:hover, .footer-custom a:hover {
            color: var(--gold) !important;
        }
        

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
        

        .hero-section {
            flex-grow: 1; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            padding: 5rem 0;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1592914610354-fd354ea45e48?q=80&w=1920&auto=format&fit=crop&ixlib=rb-4.0.3') center center no-repeat;
            background-size: cover;
            filter: blur(6px) brightness(0.4);
            transform: scale(1.1); 
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2; 
            color: var(--white);
            max-width: 800px;
        }
        .hero-title {
            font-family: 'Cinzel', serif;
            font-size: 4.5rem;
            font-weight: 700;
            color: var(--light-gold);
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.9);
            margin-bottom: 1.5rem;
        }
        .hero-tagline {
            font-size: 1.6rem;
            margin-bottom: 2.5rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
        }


        .btn-auth {
            padding: 1rem 2.5rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.4s ease;
            border-radius: 0;
            margin: 0 10px;
        }
        .btn-login {
            color: var(--gold);
            border: 2px solid var(--gold);
            background-color: rgba(0,0,0,0.3);
        }
        .btn-login:hover {
            background-color: var(--gold);
            color: var(--charcoal);
            border-color: var(--gold);
            box-shadow: 0 0 20px rgba(218, 165, 32, 0.6);
        }
        .btn-register {
            background-color: var(--gold);
            color: var(--charcoal);
            border: 2px solid var(--gold);
        }
        .btn-register:hover {
            background-color: var(--charcoal);
            color: var(--gold);
            border-color: var(--gold);
            box-shadow: 0 0 20px rgba(218, 165, 32, 0.6);
        }


        @media (max-width: 768px) {
            .hero-title { font-size: 2.8rem; }
            .hero-tagline { font-size: 1.2rem; }
            .navbar-logo { max-height: 30px; }
            .navbar-brand-text { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand navbar-brand-container" href="index.php">
                <img src="slogo.jpg" alt="Scentified Luxury Perfume Logo" class="navbar-logo">
                <span class="navbar-brand-text">Scentified</span>
            </a>
        </div>
    </nav>


    <header class="hero-section">
        <div class="hero-content px-4">
            <h1 class="hero-title">Welcome to the World of Scentified</h1>
            <p class="hero-tagline">Access to our exclusive collections requires authentication.</p>
            <div class="mt-5">
                <a href="login.php" class="btn btn-auth btn-login btn-lg" role="button">Login</a>
                <a href="register.php" class="btn btn-auth btn-register btn-lg" role="button">Register</a>
            </div>
        </div>
    </header>


    <footer class="footer-custom py-5 mt-auto">
        <div class="container text-white">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="navbar-brand-text mb-3">Scentified</h5>
                    <p class="text-white-50">The Essence of Elegance. Crafted for your distinction.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif;">Quick Links</h5>
                    <ul class="list-unstyled">

                        <li><a href="login.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Our Story</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Help & Support</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
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
</body>
</html>