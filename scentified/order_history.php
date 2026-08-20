<?php
require_once 'config.php';
protect_page();

// Get current user ID and ensure it is treated as an integer (Fix for binding issue)
$user_id = (int)$_SESSION['user_id']; 

// Fetch user orders from the database
$conn = get_db_connection();
// NOTE: Assuming 'products' table has 'product_id' and 'name' columns, as implied by the join.
$stmt = $conn->prepare("
    SELECT o.order_id, o.total_amount, o.shipping_fee, o.payment_method, o.order_status, o.created_at,
           GROUP_CONCAT(CONCAT(oi.quantity, ' x ', p.name) SEPARATOR ', ') AS items
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$currentYear = date("Y");
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Scentified | Order History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Standard Navbar & Footer */
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

    /* Page Header */
    .page-hero {
        background: var(--luxury-gradient);
        color: var(--white);
        padding: 5rem 0;
        border-bottom: 3px solid var(--gold);
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }
    .page-hero h1 {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        color: var(--light-gold);
        letter-spacing: 2px;
    }
    .text-gold { color: var(--gold) !important; }

    /* Order Card Styling */
    .card-order {
        background: var(--white);
        border: 1px solid rgba(218, 165, 32, 0.3);
        border-left: 5px solid var(--gold);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s ease;
    }
    .card-order:hover {
        box-shadow: 0 8px 20px rgba(218, 165, 32, 0.2);
    }
    .card-order h5 {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        color: var(--charcoal);
        margin-bottom: 15px;
        border-bottom: 1px dashed rgba(28, 28, 28, 0.1);
        padding-bottom: 5px;
    }
    .card-order p {
        margin-bottom: 5px;
        font-size: 0.95rem;
    }
    .status-delivered { color: #28a745; font-weight: 700; } /* Green */
    .status-pending { color: var(--gold); font-weight: 700; } /* Gold */
    .status-cancelled { color: #dc3545; font-weight: 700; } /* Red */
    
    /* Custom style for requested price formatting (bold black Arial-style) */
    .order-price-font {
        font-family: Arial, sans-serif !important;
        color: var(--charcoal) !important;
        font-weight: 900 !important; /* Extra bold */
    }

    /* Responsive adjustments for Order Card layout */
    @media (max-width: 767px) {
        /* Stack price/status and details vertically */
        .card-order .row > div {
            text-align: left !important;
            margin-bottom: 15px;
        }
        /* Ensure the total amount section looks good when stacked */
        .card-order .col-md-3:nth-child(2) {
            border-top: 1px dashed rgba(28, 28, 28, 0.1);
            padding-top: 15px;
        }
    }

</style>
</head>
<body>

    <!-- Standard Navigation Bar -->
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
                    <!-- Links to Order Pages -->
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <!-- Removed checkout.php link -->
                    <li class="nav-item"><a class="nav-link active-link" href="order_history.php">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-hero text-center">
        <div class="container">
            <h1 class="display-4">Your Purchase History</h1>
            <p class="lead">Track the status of your recent Scentified orders.</p>
        </div>
    </header>

    <main class="py-5 flex-grow-1">
        <div class="container">
            <?php if(empty($orders)): ?>
                <div class="alert alert-info text-center shadow-sm">
                    <h4 class="alert-heading" style="font-family: 'Cinzel', serif;">No Orders Found</h4>
                    <p>It seems you haven't placed any orders yet. Visit the <a href="shop.php" class="alert-link text-gold">Shop Page</a> to explore our collection.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <?php foreach($orders as $order): ?>
                            <div class="card-order">
                                <!-- Responsive Row structure -->
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <h5 class="mb-2">Order #<?php echo $order['order_id']; ?></h5>
                                        <p class="mb-1"><strong>Items:</strong> <span class="text-muted"><?php echo htmlspecialchars($order['items']); ?></span></p>
                                        <p class="mb-1"><strong>Ordered At:</strong> <span class="text-muted"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></span></p>
                                        <p class="mb-0"><strong>Payment Method:</strong> <span class="text-muted"><?php echo ucfirst($order['payment_method']); ?></span></p>
                                    </div>
                                    <div class="col-6 col-md-3 text-start text-md-center">
                                        <p class="mb-1"><strong>Shipping:</strong> ₱<?php echo number_format($order['shipping_fee'], 2); ?></p>
                                        <p class="mb-0"><strong>Total:</strong> <span class="fw-bold fs-5 text-gold order-price-font">₱<?php echo number_format($order['total_amount'], 2); ?></span></p>
                                    </div>
                                    <div class="col-6 col-md-3 text-end text-md-end">
                                        <p class="mb-0"><strong>Status:</strong></p>
                                        <span class="fs-6 fw-bold <?php 
                                            $status = strtolower($order['order_status']);
                                            if ($status == 'delivered') echo 'status-delivered'; 
                                            else if ($status == 'cancelled') echo 'status-cancelled'; 
                                            else echo 'status-pending'; 
                                        ?>">
                                            <?php echo $order['order_status']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Standard Footer -->
    <footer class="footer-custom py-5 mt-5">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>