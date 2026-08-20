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
    <title>Scentified | The Essence of Luxury</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Cinzel:wght@400..700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --charcoal: #1c1c1c;
            --gold: #daa520;
            --light-gold: #f0e68c;
            --white: #ffffff;
            --background-light: #f5f5f5; /* Soft, warm background */
            --luxury-gradient: linear-gradient(135deg, #0a0a0a 0%, #3a2c0f 100%);
        }

        body {
            font-family: 'Playfair Display', serif;
            background-color: var(--background-light);
            color: var(--charcoal);
            line-height: 1.8;
            overflow-x: hidden;
        }

        /* NAVBAR */
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
        .navbar-custom .nav-link:hover { color: var(--gold) !important; }
        .navbar-custom .nav-link.active-link { color: var(--gold) !important; }

        .navbar-brand-container {
            display: flex;
            align-items: center;
        }
        .navbar-logo {
            max-height: 40px;
            margin-right: 10px;
        }
        .navbar-brand-text {
            color: var(--gold) !important;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            }

            /* Hide hero title and tagline but keep the Discover button visible */
            .hero-title,
            .hero-tagline {
                display: none !important;
            }

            /* Remove blur from hero background and keep a gentle brightness */
            .hero-bg {
                filter: brightness(0.6) !important;
                transform: scale(1.1) !important;
                transition: transform 0.1s ease-out, filter 0.1s ease-out;
            }
        

        /* HERO SECTION (Parallax) */
        .hero-section {
            height: 85vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            inset: 0;
            background: url('1.png') center/cover no-repeat;
            filter: blur(6px) brightness(0.4);
            transform: scale(1.1);
            transition: filter 0.1s ease-out;
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
            text-shadow: 3px 3px 6px rgba(0,0,0,0.9);
        }
        .hero-tagline {
            font-size: 1.6rem;
            margin-bottom: 2.5rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
        }

        .btn-gold-outline {
            color: var(--gold);
            border: 2px solid var(--gold);
            padding: 1rem 2.5rem;
            background: rgba(0,0,0,0.3);
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.4s ease;
            border-radius: 0;
        }
        .btn-gold-outline:hover {
            background: var(--gold);
            color: var(--charcoal);
            box-shadow: 0 0 25px rgba(218,165,32,0.6);
        }

        /* --- INFINITE SLIDER STYLES --- */
        
        /* Define the continuous scroll animation */
        @keyframes scroll {
            /* FIXED: Width of 1 item (600px) + margin (15px) = 615px. 9 unique images in the set. */
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-615px * 9)); }
        }

        .infinite-slider {
            white-space: nowrap;
            overflow: hidden;
            position: relative;
            padding: 30px 0;
            margin: 40px 0;
        }

        .infinite-slider-track {
            display: inline-block;
            animation: scroll 35s linear infinite; /* 35s speed */
        }
        
        .infinite-slider-track:hover {
            animation-play-state: paused; /* Pause animation on hover */
        }
        
        /* Image styling - FIXED to explicit sizes for layout stability */
        .gallery-img {
            width: 600px; /* Set to requested large size */
            height: 400px; /* Set to requested large size (3:2 aspect ratio) */
            object-fit: cover;
            display: inline-block;
            margin: 0; /* margin moved to wrapper */
            border-bottom: 3px solid var(--gold);
            border-radius: 4px 4px 0 0; /* Add slight rounding to top */
            
            /* Transition for the hover effect */
            transition: transform 0.3s ease-out, box-shadow 0.3s ease;
            
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Wrapper for individual slider items so we can position overlays */
        .slider-item-wrapper {
            display: inline-block;
            margin: 0 15px; /* spacing between items */
            position: relative;
        }

        /* Overlay CTA centered over the first image */
        .overlay-cta {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 4;
            background: var(--gold);
            color: var(--charcoal);
            padding: 0.85rem 1.6rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: none;
            border: 2px solid var(--gold);
            border-radius: 4px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }

        .overlay-cta:hover {
            background: transparent;
            color: var(--gold);
            box-shadow: 0 0 20px rgba(218,165,32,0.35);
        }

        /* Hover State for Pop-up Effect - FIXED to proper scale */
        .gallery-img:hover {
            transform: scale(1.05) translateY(-5px); /* Scale up and lift slightly */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5); /* Deeper shadow for 3D pop */
            cursor: pointer;
        }

        /* --- FEATURES SECTION --- */
        .features-section {
            background: var(--white);
            padding: 60px 0;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            border-top: 3px solid var(--gold);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 15px;
        }

        .feature-title {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            color: var(--charcoal);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .feature-text {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* --- TESTIMONIALS SECTION --- */
        .testimonials-section {
            background: var(--background-light);
            padding: 60px 0;
        }

        .testimonials-title {
            font-family: 'Cinzel', serif;
            text-align: center;
            font-size: 2.5rem;
            color: var(--charcoal);
            margin-bottom: 50px;
            font-weight: 700;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 15px;
        }

        .testimonial-card {
            background: var(--white);
            padding: 30px;
            border-left: 4px solid var(--gold);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateX(5px);
        }

        .testimonial-text {
            font-style: italic;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .testimonial-author {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-weight: 700;
        }

        .stars {
            color: var(--gold);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        /* --- CTA SECTION --- */
        .cta-section {
            background: var(--luxury-gradient);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .cta-title {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--light-gold);
            font-weight: 700;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }

        .btn-gold-filled {
            background: var(--gold);
            color: var(--charcoal);
            border: 2px solid var(--gold);
            padding: 1rem 2.5rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.4s ease;
            border-radius: 0;
            display: inline-block;
            text-decoration: none;
        }

        .btn-gold-filled:hover {
            background: transparent;
            color: var(--gold);
            box-shadow: 0 0 25px rgba(218,165,32,0.6);
        }

        /* --- PARALLAX GALLERY SECTION --- */
        .parallax-gallery-section {
            padding: 60px 0;
            background: var(--white);
        }

        .gallery-section-title {
            font-family: 'Cinzel', serif;
            text-align: center;
            font-size: 2.5rem;
            color: var(--charcoal);
            margin-bottom: 50px;
            font-weight: 700;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 15px;
        }

        .parallax-image-wrapper {
            position: relative;
            height: 400px;
            overflow: hidden;
            margin-bottom: 40px;
            border-bottom: 3px solid var(--gold);
            border-radius: 4px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .parallax-image {
            position: absolute;
            width: 100%;
            height: 120%; /* Image is taller than wrapper for movement */
            object-fit: cover;
            top: 0;
            left: 0;
            /* Smooth transition for parallax movement */
            will-change: transform; 
            transition: transform 0.1s linear; 
        }

        .parallax-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .parallax-text {
            font-family: 'Cinzel', serif;
            color: var(--light-gold);
            font-size: 2rem;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
            text-align: center;
            z-index: 3;
            font-weight: 700;
        }

        /* --- OUR COLLECTIONS SECTION --- */
        .collections-section {
            padding: 60px 0;
            background: var(--white);
        }

        .collections-title {
            font-family: 'Cinzel', serif;
            text-align: center;
            font-size: 2.5rem;
            color: var(--charcoal);
            margin-bottom: 50px;
            font-weight: 700;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 15px;
        }
        
        /* CSS to center the images and control size: INCREASED max-width */
        .collection-image-wrapper {
            position: relative;
            max-width: 1100px; /* Increased from 900px to make images bigger */
            width: 90%; /* Responsive width on smaller screens */
            margin: 0 auto 30px auto; /* Center the block element horizontally */
            overflow: hidden;
            border-bottom: 3px solid var(--gold);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .collection-image {
            width: 100%; /* Ensure image fills its responsive container */
            height: auto;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .collection-image:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand navbar-brand-container" href="home.php">
                <img src="slogo.jpg" onerror="this.onerror=null;this.src='https://placehold.co/40x40/1c1c1c/daa520?text=S';" class="navbar-logo">
                <span class="navbar-brand-text">Scentified</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <!-- Updated: Removed Checkout link -->
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div id="hero-bg" class="hero-bg"></div>
        
        <div class="hero-content px-4">
            <h1 class="hero-title">The Essence of Timeless Elegance</h1>
            <p class="hero-tagline">Unveil the fragrance that defines your legacy.</p>

            <a href="shop.php" class="btn btn-gold-outline btn-lg">Discover The Collection</a>
        </div>
    </header>

    <section class="py-5">
        <div class="container-fluid p-0">

            <div class="infinite-slider">
                <div class="infinite-slider-track">
                    <div class="slider-item-wrapper">
                        <img src="1.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+01';" >
                        <a href="shop.php" class="overlay-cta">Discover The Collection</a>
                    </div>
                    <img src="2.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+02';" >
                    <img src="3.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+03';" >
                    <img src="4.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+04';" >
                    <img src="5.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+05';" >
                    <img src="6.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+06';" >
                    <img src="7.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+07';" >
                    <img src="8.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+08';" >
                    <img src="9.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+09';" >

                    <img src="1.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+01+DUP';" >
                    <img src="2.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+02+DUP';" >
                    <img src="3.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+03+DUP';" >
                    <img src="4.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+04+DUP';" >
                    <img src="5.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+05+DUP';" >
                    <img src="6.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+06+DUP';" >
                    <img src="7.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+07+DUP';" >
                    <img src="8.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+08+DUP';" >
                    <img src="9.png" class="gallery-img" onerror="this.onerror=null;this.src='https://placehold.co/600x400/1c1c1c/daa520?text=SCENT+09+DUP';" >
                </div>
            </div>
        </div>
    </section>

    <section class="collections-section">
        <div class="container">
            <h2 class="collections-title">Our Collections</h2>
        </div>
        
        <!-- Images are centered via the CSS rule .collection-image-wrapper { margin: 0 auto; } -->
        <div class="container-fluid p-0">
            <div class="collection-image-wrapper">
                <img src="7.png" class="collection-image" onerror="this.onerror=null;this.src='https://placehold.co/1200x600/1c1c1c/daa520?text=Collection+7';">
            </div>

            <div class="collection-image-wrapper">
                <img src="8.png" class="collection-image" onerror="this.onerror=null;this.src='https://placehold.co/1200x600/1c1c1c/daa520?text=Collection+8';">
            </div>

            <div class="collection-image-wrapper">
                <img src="9.png" class="collection-image" onerror="this.onerror=null;this.src='https://placehold.co/1200x600/1c1c1c/daa520?text=Collection+9';">
            </div>
        </div>
    </section>
    
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-gem"></i></div>
                        <h3 class="feature-title">Premium Quality</h3>
                        <p class="feature-text">Crafted with the finest ingredients from around the world, each fragrance is a masterpiece of luxury and sophistication.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-leaf"></i></div>
                        <h3 class="feature-title">Natural Ingredients</h3>
                        <p class="feature-text">We use only natural and ethically sourced ingredients to ensure the highest quality and sustainability in every bottle.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-heart"></i></div>
                        <h3 class="feature-title">Timeless Appeal</h3>
                        <p class="feature-text">Our fragrances are designed to stand the test of time, becoming an essential part of your personal signature.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <h2 class="testimonials-title">What Our Customers Say</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <p class="testimonial-text">"Scentified has completely transformed my daily routine. The elegance and sophistication of their fragrances are unmatched. I feel like royalty every time I wear one."</p>
                        <p class="testimonial-author">- Sicks Sven.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <p class="testimonial-text">"The quality is exceptional. I've tried many luxury fragrances, but Scentified stands out for its longevity and exquisite scent profiles. Highly recommend!"</p>
                        <p class="testimonial-author">- Gabe Horn.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <p class="testimonial-text">"From the first spray, I knew this was special. The natural ingredients make such a difference. It's truly the essence of luxury at its finest."</p>
                        <p class="testimonial-author">- Nick Gurr.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <p class="testimonial-text">"Scentified's customer service is incredible, and the fragrances are absolutely divine. I've already ordered three times and I'm a loyal customer for life!"</p>
                        <p class="testimonial-author">- Bing Bong.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Discover Your Signature Scent</h2>
            <p class="cta-subtitle">Experience the luxury and elegance that defines Scentified. Your journey begins today.</p>
            <a href="shop.php" class="btn-gold-filled">Shop Now</a>
        </div>
    </section>

    <footer class="footer-custom py-5 mt-5">
        <div class="container text-white">
            <div class="text-center text-white-50 small">
                &copy; <?php echo $currentYear; ?> Scentified. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // --- Dynamic Parallax Scaling for Hero Image ---
        const heroBg = document.getElementById('hero-bg');
        // --- Parallax Gallery Images ---
        const parallaxWrappers = document.querySelectorAll('.parallax-image-wrapper');
        let isScrolling = false;

        function applyHeroParallax() {
            if (!heroBg) return;

            const scrollPosition = window.scrollY;
            // Scale up from 1.1 based on scroll position (adjust 0.0003 for stronger/weaker effect)
            const scaleFactor = 1.1 + (scrollPosition * 0.0003); 
            // Slightly reduce blur and brightness to make the content pop more as we scroll down
            const blurFactor = 6 - (scrollPosition * 0.005); 
            
            // Ensure blur doesn't go below 2px
            const clampedBlur = Math.max(2, blurFactor); 

            heroBg.style.transform = `scale(${scaleFactor})`;
            heroBg.style.filter = `blur(${clampedBlur}px) brightness(0.4)`;
        }

        function applyGalleryParallax() {
            parallaxWrappers.forEach(wrapper => {
                const img = wrapper.querySelector('.parallax-image');
                if (!img) return;

                const speed = parseFloat(wrapper.getAttribute('data-parallax-speed')) || 0.5;
                
                // Calculate position relative to the viewport center
                const rect = wrapper.getBoundingClientRect();
                const viewportCenter = window.innerHeight / 2;
                const elementCenter = rect.top + rect.height / 2;
                const distance = viewportCenter - elementCenter;
                
                // Apply subtle vertical translation based on distance from center
                // Multiplied by 0.1 to keep the effect subtle and contained within the 120% height
                img.style.transform = `translateY(${distance * speed * 0.1}px)`;
            });
        }

        // Use requestAnimationFrame for smooth, non-janky scrolling performance
        window.addEventListener('scroll', () => {
            if (!isScrolling) {
                window.requestAnimationFrame(() => {
                    applyHeroParallax();
                    applyGalleryParallax();
                    isScrolling = false;
                });
                isScrolling = true;
            }
        });

        // Apply initial states on load
        applyHeroParallax();
        applyGalleryParallax();

    </script>
</body>
</html>