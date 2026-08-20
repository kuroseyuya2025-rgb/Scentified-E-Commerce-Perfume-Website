<?php
require_once 'config.php';
protect_page();

$currentYear = date("Y");

// --- START: Dynamic Product Fetch from Database (P2 Fix) ---
$conn = get_db_connection();
$products_db_result = $conn->query("SELECT product_id AS id, name, price, description, img_url AS image FROM products");
$products = $products_db_result->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Helper function to find product details using the dynamic $products array
function find_product_by_id($products_array, $id) {
    foreach ($products_array as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}
// --- END: Dynamic Product Fetch ---


// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    // Check if the product ID is valid based on DB data
    if (find_product_by_id($products, $product_id)) {
        $quantity = max(1, min(intval($_POST['quantity'] ?? 1), 10));

        if (isset($_SESSION['cart'][$product_id])) {
            // Cap quantity at 10
            $_SESSION['cart'][$product_id] = min($_SESSION['cart'][$product_id] + $quantity, 10);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}


// Update cart quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, min(intval($_POST['quantity'] ?? 1), 10));

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}


// Remove from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    unset($_SESSION['cart'][intval($_GET['remove'])]);
    header('Location: shop.php');
    exit;
}


// Compute totals using dynamic product data
$cart_total = 0;
$cart_items = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = find_product_by_id($products, $product_id);
    if ($product) {
        $cart_total += $product['price'] * $quantity;
        $cart_items += $quantity;
    }
}


$shipping_cost = 100; // Standardized shipping cost (was 50 in original hardcoded logic)
$final_total = $cart_total + $shipping_cost;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Cinzel:wght@400..700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --charcoal: #1c1c1c;
            --gold: #daa520;
            --light-gold: #f0e68c;
            --white: #ffffff;
            --background-light: #f5f5f5;
            --luxury-gradient: linear-gradient(135deg, #0a0a0a 0%, #3a2c0f 100%);
        }

        body {
            font-family: 'Playfair Display', serif;
            background-color: var(--background-light);
            color: var(--charcoal);
            line-height: 1.8;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Nav & Footer Styling */
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
        .navbar-logo { max-height: 40px; width: auto; margin-right: 10px; transition: transform 0.3s ease; }
        .navbar-logo:hover { transform: scale(1.05); }
        .navbar-brand-text {
            color: var(--gold) !important;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
        }
        
        .text-gold { color: var(--gold) !important; }

        /* Page Header */
        .shop-header {
            background: var(--luxury-gradient);
            color: var(--white);
            padding: 5rem 0;
            border-bottom: 3px solid var(--gold);
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .shop-header h1 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: var(--light-gold);
            letter-spacing: 2px;
        }

        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .navbar-logo { max-height: 30px; }
            .navbar-brand-text { font-size: 1.4rem; }
            .shop-header h1 { font-size: 2.5rem; }
            /* Stack main content and cart vertically on small screens */
            .col-lg-7, .col-lg-5 {
                width: 100%;
                margin-bottom: 2rem;
            }
            .product-card .row > div { 
                /* Ensure image and text stack vertically */
                width: 100%;
            }
        }
        @media (min-width: 768px) and (max-width: 991px) {
            /* On tablet, show product cards in two columns */
            .product-item-col {
                width: 50%;
            }
        }


        /* --- PRODUCT CARD STYLING --- */
        .product-card {
            transition: all 0.3s ease;
            border: 1px solid var(--gold) !important; /* Lighter border, gold theme */
            border-radius: 8px;
            overflow: hidden;
            height: 100%; /* Use h-100 on the card itself */
        }
        .product-card:hover {
            border-bottom-color: var(--gold);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(218, 165, 32, 0.2) !important;
        }
        
        /* Fixed height for the card structure on large screens (desktop list view) */
        @media (min-width: 992px) {
             .product-card {
                 /* Set a uniform minimum height for the card itself in the list view */
                 min-height: 300px; 
             }
        }
        @media (min-width: 768px) and (max-width: 991px) {
             /* Set a uniform height for the card on tablet grid view */
             .product-card {
                 min-height: 500px;
             }
        }


        /* Enforce image wrapper height relative to card structure */
        .product-image-wrapper {
            background: var(--background-light);
            overflow: hidden;
            height: 100%; /* Take full height of its column (col-md-4) */
            /* Control max height of image area on desktop view */
            max-height: 300px; 
        }
        @media (max-width: 767px) {
            /* Reset max-height on mobile when stacked vertically */
            .product-image-wrapper {
                 max-height: 250px; 
            }
        }

        .product-image {
            transition: transform 0.3s ease;
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
        .product-card:hover .product-image {
            transform: scale(1.08);
        }
        
        /* Product Card Typography - Consolidated from inline styles */
        .product-name {
            font-family: 'Cinzel', serif;
            color: var(--charcoal);
            font-size: 1.3rem;
            font-weight: 700;
        }
        .product-description {
             color: var(--charcoal);
             font-size: 1.1rem;
             line-height: 1.6;
        }
        .product-price {
            color: var(--charcoal);
            font-weight: 700;
            font-size: 1.6rem;
        }

        /* Button Styling */
        .btn-outline-gold {
            color: var(--gold);
            border: 2px solid var(--gold);
            transition: all 0.3s ease;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: var(--charcoal);
        }
        .add-to-cart-btn {
            width: 100%;
            flex: 1;
        }

        /* --- CART STYLING --- */
        .cart-item {
            background: var(--background-light);
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .cart-items {
            max-height: 350px; 
            overflow-y: auto; 
            padding-right: 0.5rem;
        }
        
        /* Cart Typography - Consolidated from inline styles */
        .cart-item-name {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.15rem; 
            color: var(--charcoal); 
            font-weight: 700;
        }
        .cart-item-price-display {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--charcoal); 
            font-size: 1.45rem; 
            font-weight: 800;
        }
        .cart-total-line {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.15rem; 
            color: var(--charcoal); 
            font-weight: 600;
        }
        .cart-final-total-line {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.5rem; 
            color: var(--charcoal); 
            padding-top: 0.5rem; 
            border-top: 2px solid var(--gold);
            font-weight: 800;
        }

        /* --- QUANTITY CONTROL STYLING --- */
        .qty-control-group {
            display: flex; 
            gap: 0.5rem; 
            align-items: center; 
            margin-bottom: 0.5rem;
        }

        .qty-label {
            color: var(--charcoal); 
            font-size: 1rem; 
            margin: 0; 
            white-space: nowrap; 
            font-weight: 700;
        }

        .qty-btn {
            background: var(--gold); 
            color: var(--charcoal); 
            border: none; 
            width: 44px; 
            height: 44px; 
            border-radius: 50%; 
            cursor: pointer; 
            font-size: 1.25rem; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            transition: all 0.2s ease; 
            font-weight: bold;
            line-height: 1; /* Fix vertical alignment */
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
        }
        .qty-btn:hover {
             transform: scale(1.1); 
             box-shadow: 0 2px 8px rgba(218, 165, 32, 0.4);
        }

        .qty-input {
            width: 70px; 
            padding: 0.4rem; 
            font-size: 1.15rem; 
            border: 2px solid #ccc; 
            border-radius: 18px; 
            text-align: center; 
            transition: all 0.2s; 
            background: #fff; 
            font-weight: 700;
        }
        .qty-input:focus {
             outline: none; 
             border-color: var(--gold) !important; 
             box-shadow: 0 0 8px rgba(218, 165, 32, 0.3); 
             background: #fff;
        }
        
        /* Checkout button using defined class */
        .btn-gold {
             background: var(--gold); 
             color: var(--charcoal); 
             border: none; 
             padding: 12px; 
             font-family: 'Cinzel', serif; 
             border-radius: 6px; 
             transition: all 0.3s ease;
             font-weight: 700;
        }
        .btn-gold:hover {
             box-shadow: 0 10px 25px rgba(218,165,32,0.4); 
             transform: translateY(-2px);
             background: var(--gold);
             color: var(--charcoal);
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
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <!-- Shop active -->
                    <li class="nav-item"><a class="nav-link active-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="shop-header text-center">
        <div class="container">
            <h1 class="display-4">The Scentified Collection</h1>
            <p class="lead text-white-50">Explore our full range of luxury fragrances.</p>
        </div>
    </header>

    <main class="py-5 flex-grow-1">
        <div class="container">
            <!-- Swapped col-lg-7 and col-lg-5 for better flow on mobile (cart last) -->
            <div class="row g-5">
                
                <!-- Product List: Takes full width on mobile/tablet (col-12), wider on large screen (col-lg-7) -->
                <div class="col-lg-7 order-lg-1 mb-4 mb-lg-0">
                    <div class="row g-5 align-items-stretch"> <!-- Added align-items-stretch here -->
                        <?php if (empty($products)): ?>
                             <div class="col-12"><p class="text-center text-muted fs-4">No products found in the database. Please check your `products` table.</p></div>
                        <?php endif; ?>
                        
                        <?php foreach ($products as $product): ?>
                        <!-- Responsive Product Grid: col-12 on small, col-md-6 on medium, col-lg-12 on large -->
                        <div class="col-12 col-md-6 col-lg-12 product-item-col">
                            <!-- Ensure h-100 is used on the card -->
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <!-- Horizontal layout on desktop, stack on mobile. Used 'row' and 'g-0' on the card body -->
                                <div class="row g-0 h-100"> 
                                    <div class="col-md-4">
                                        <!-- Image wrapper takes full height of its column -->
                                        <div class="product-image-wrapper position-relative overflow-hidden">
                                            <!-- Image source updated here -->
                                            <img src="<?php echo htmlspecialchars($product['image'] ?? 'placeholder.png'); ?>" 
                                                class="card-img-top product-image" 
                                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                onerror="this.onerror=null;this.src='https://placehold.co/300x400/1c1c1c/daa520?text=<?php echo urlencode($product['name']); ?>';">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <!-- CRITICAL: Make card-body a flex column to push buttons/controls to the bottom -->
                                        <div class="card-body d-flex flex-column h-100"> 
                                            <h5 class="card-title product-name">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </h5>
                                            <p class="card-text flex-grow-1 product-description">
                                                <?php echo htmlspecialchars($product['description']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="h4 mb-0 product-price">
                                                    ₱<?php echo number_format($product['price'], 2); ?>
                                                </span>
                                            </div>
                                            <form method="POST" class="mt-4">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <div class="input-group" style="height: 2.5rem;">
                                                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="10">
                                                    <button class="btn btn-outline-gold add-to-cart-btn" type="submit" name="add_to_cart" style="font-size: 0.95rem; font-weight: 600;">
                                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cart Summary: Takes full width on mobile/tablet (col-12), narrower on large screen (col-lg-5) -->
                <div class="col-lg-5 order-lg-2">
                    <div class="card shadow-lg border-0 sticky-top" style="top: 80px; border-radius: 12px; overflow: hidden;">
                        <div class="card-header" style="background: var(--luxury-gradient); color: var(--light-gold); padding: 1.5rem;">
                            <h5 class="mb-0" style="font-family: 'Cinzel', serif; font-size: 1.3rem;">
                                <i class="fas fa-shopping-bag me-2"></i>Your Cart
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <?php if (empty($_SESSION['cart'])): ?>
                                <p class="text-muted text-center py-4">Your cart is empty</p>
                            <?php else: ?>
                                <div class="cart-items">
                                <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                                    <?php 
                                        $product = find_product_by_id($products, $product_id);
                                        if ($product) {
                                    ?>
                                    <div class="cart-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 cart-item-name">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h6>
                                                <form method="POST" class="quantity-form qty-control-group">
                                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                                    <input type="hidden" name="update_quantity" value="1">
                                                    <label class="qty-label">Qty:</label>
                                                    <button type="button" class="qty-btn qty-btn-minus">−</button>
                                                    <input type="number" name="quantity" class="qty-input" value="<?php echo $quantity; ?>" min="1" max="10">
                                                    <button type="button" class="qty-btn qty-btn-plus">+</button>
                                                </form>
                                                <div class="cart-item-price-display" data-price="<?php echo $product['price']; ?>">
                                                    ₱<?php echo number_format($product['price'] * $quantity, 2); ?>
                                                </div>
                                            </div>
                                            <a href="?remove=<?php echo $product_id; ?>" class="btn btn-sm btn-outline-danger ms-2">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                <?php endforeach; ?>
                                </div>
                                
                                <hr class="my-3" style="border-color: var(--gold); opacity: 0.3;">
                                
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2 cart-total-line">
                                        <span>Subtotal:</span>
                                        <span id="cart-subtotal" data-value="<?php echo $cart_total; ?>">₱<?php echo number_format($cart_total, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 cart-total-line">
                                        <span>Shipping:</span>
                                        <span id="cart-shipping" data-value="<?php echo $shipping_cost; ?>">₱<?php echo number_format($shipping_cost, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold cart-final-total-line">
                                        <span>Total:</span>
                                        <span id="cart-total">₱<?php echo number_format($final_total, 2); ?></span>
                                    </div>
                                </div>

                                <a href="checkout.php" class="btn btn-gold w-100">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
                        <li><a href="faqs.php" class="text-white-50 text-decoration-none">Help &amp; Support</a></li>
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
        (function(){
            const SHIPPING = <?php echo $shipping_cost; ?>;
            function formatCurrency(n){
                return '₱' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function updateTotals(){
                let subtotal = 0;
                document.querySelectorAll('.cart-item').forEach(item => {
                    const priceEl = item.querySelector('.cart-item-price-display');
                    const base = parseFloat(priceEl.getAttribute('data-price')) || 0;
                    const qty = parseInt(item.querySelector('.qty-input').value) || 1;
                    subtotal += base * qty;
                });
                const subtotalEl = document.getElementById('cart-subtotal');
                if(subtotalEl){
                    subtotalEl.dataset.value = subtotal;
                    subtotalEl.textContent = formatCurrency(subtotal);
                }
                const totalEl = document.getElementById('cart-total');
                if(totalEl){
                    totalEl.textContent = formatCurrency(subtotal + SHIPPING);
                }
            }

            document.querySelectorAll('.quantity-form').forEach(form => {
                const qtyInput = form.querySelector('.qty-input');
                const btnMinus = form.querySelector('.qty-btn-minus');
                const btnPlus = form.querySelector('.qty-btn-plus');
                const priceElement = form.closest('.cart-item').querySelector('.cart-item-price-display');
                const basePrice = parseFloat(priceElement.getAttribute('data-price')) || 0;
                let debounceTimer;

                function updatePrice(){
                    let qty = parseInt(qtyInput.value) || 1;
                    if(qty < 1) qty = 1;
                    if(qty > 10) qty = 10;
                    qtyInput.value = qty;
                    const newPrice = basePrice * qty;
                    priceElement.textContent = formatCurrency(newPrice);
                    updateTotals();
                }

                function scheduleSubmit(){
                    clearTimeout(debounceTimer);
                    // Submit the form to update PHP session state
                    debounceTimer = setTimeout(() => { form.submit(); }, 800);
                }

                btnMinus.addEventListener('click', (e) => {
                    e.preventDefault();
                    let val = parseInt(qtyInput.value) || 1;
                    if (val > 1) {
                        qtyInput.value = val - 1;
                        updatePrice();
                        scheduleSubmit();
                    }
                });

                btnPlus.addEventListener('click', (e) => {
                    e.preventDefault();
                    let val = parseInt(qtyInput.value) || 1;
                    if (val < 10) {
                        qtyInput.value = val + 1;
                        updatePrice();
                        scheduleSubmit();
                    }
                });

                // Prevent typing invalid values and clamp in real-time
                qtyInput.addEventListener('input', (e) => {
                    let v = e.target.value.replace(/[^0-9]/g, '');
                    if (v === '') { e.target.value = ''; return; }
                    if (v.length > 2) v = v.slice(0,2);
                    let iv = parseInt(v, 10) || 1;
                    if (iv > 10) iv = 10;
                    e.target.value = iv;
                    updatePrice();
                });

                qtyInput.addEventListener('change', (e) => {
                    let iv = parseInt(e.target.value) || 1;
                    if (iv < 1) iv = 1;
                    if (iv > 10) iv = 10;
                    e.target.value = iv;
                    updatePrice();
                    scheduleSubmit();
                });

                qtyInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);
                        form.submit();
                    }
                });

                // initialize line price and totals
                updatePrice();
            });

            // initial totals
            updateTotals();
        })();
    </script>
</body>
</html>