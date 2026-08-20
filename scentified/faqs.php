<?php
require_once 'config.php';
protect_page(); // ENFORCE LOGIN

$currentYear = date("Y");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | FAQs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .navbar-brand-container { display: flex; align-items: center; text-decoration: none; }
        .navbar-logo { max-height: 40px; margin-right: 10px; transition: transform 0.3s ease; }
        .navbar-logo:hover { transform: scale(1.05); }
        .navbar-brand-text {
            color: var(--gold) !important;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
        }

        .faqs-header {
            background: var(--luxury-gradient);
            color: var(--white);
            padding: 5rem 0;
            border-bottom: 3px solid var(--gold);
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .faqs-header h1 { font-family: 'Cinzel', serif; font-weight: 700; color: var(--light-gold); letter-spacing: 2px; }

        .accordion-item {
            border: none;
            border-bottom: 1px solid rgba(218, 165, 32, 0.3);
            background: transparent;
            margin-bottom: 1rem;
        }
        .accordion-button {
            font-family: 'Cinzel', serif;
            font-weight: 600;
            font-size: 1.1rem;
            background-color: transparent;
            color: var(--charcoal);
            padding: 1.5rem;
            box-shadow: none;
            border: 1px solid rgba(218, 165, 32, 0.5) !important;
            border-radius: 0.25rem !important;
        }
        .accordion-button:not(.collapsed) {
            color: var(--gold);
            background-color: #fcfcfc;
            box-shadow: none;
            border: 2px solid var(--gold) !important;
        }
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23daa520'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transition: transform 0.3s ease;
        }
        .accordion-body {
            padding: 1rem 1.5rem;
            color: var(--charcoal);
        }

        .text-gold { color: var(--gold) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand navbar-brand-container" href="home.php">
            <img src="slogo.jpg" alt="Scentified Logo" class="navbar-logo" onerror="this.onerror=null;this.src='https://placehold.co/40x40/1c1c1c/daa520?text=S';">
            <span class="navbar-brand-text">Scentified</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="color: var(--gold);"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                <li class="nav-item"><a class="nav-link active-link" href="faqs.php">FAQs</a></li>
                <!-- Updated: Removed Checkout link -->
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="faqs-header text-center">
    <div class="container">
        <h1 class="display-4">Frequently Asked Questions</h1>
        <p class="lead text-white-50">A resource to address your most valued inquiries.</p>
    </div>
</header>

<section class="py-5 my-5 flex-grow-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion accordion-flush" id="faqsAccordion">
                    <!-- FAQ items here (same as your original, no changes needed for content) -->
                    <?php
                    $faqs = [
                        ["What type of fragrances do you offer?", "We offer a comprehensive range including Floral, woody, citrus, oriental, and signature blends designed for all customers."],
                        ["Are your products safe for sensitive skin?", "Yes, our fragrances are formulated with skin-friendly ingredients. Conduct a patch-test if sensitive."],
                        ["How long do your perfumes last?", "Typically 6-12 hours depending on concentration, skin chemistry, and conditions."],
                        ["Do you offer cash-on-delivery (COD)?", "Yes, COD is available in selected regions; availability confirmed at checkout."],
                        ["Can I return or replace an item?", "Returns allowed within 7 days only if item is damaged or incorrect; must be unused in original packaging."],
                        ["Do you ship internationally?", "Delivery times and shipping costs vary by country."],
                        ["How can I track my order?", "Tracking number sent via email or available in customer dashboard."],
                        ["What payment methods do you accept?", "We accept major credit/debit cards, bank transfers, GCash, and COD where available."],
                        ["Do you offer gift packaging?", "Elegant gift packaging for birthdays, anniversaries, and special occasions."],
                        ["Can I request a custom scent?", "Limited custom-blend services available; contact bespoke services for details."]
                    ];

                    foreach ($faqs as $i => $faq) {
                        $collapseId = "collapse" . ($i+1);
                        $headingId = "heading" . ($i+1);
                        $collapsed = $i === 0 ? "" : "collapsed";
                        $show = $i === 0 ? "show" : "";
                        echo "<div class='accordion-item'>
                                <h2 class='accordion-header' id='$headingId'>
                                    <button class='accordion-button $collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#$collapseId'>
                                        {$faq[0]}
                                    </button>
                                </h2>
                                <div id='$collapseId' class='accordion-collapse collapse $show' data-bs-parent='#faqsAccordion'>
                                    <div class='accordion-body'>{$faq[1]}</div>
                                </div>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <li><a href="home.php" class="text-white-50 text-decoration-none">Home</a></li>
                    <li><a href="about.php" class="text-white-50 text-decoration-none">Our Story</a></li>
                    <li><a href="faqs.php" class="text-white-50 text-decoration-none">Help & Support</a></li>
                    <li><a href="contact.php" class="text-white-50 text-decoration-none">Contact Us</a></li>
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
        <div class="text-center text-white-50 small">&copy; <?php echo $currentYear; ?> Scentified. All rights reserved.</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>