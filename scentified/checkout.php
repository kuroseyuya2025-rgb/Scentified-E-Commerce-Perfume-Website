<?php
require_once 'config.php';
protect_page();

$currentYear = date("Y");

// --- START: Dynamic Product Fetch from Database (P2 Fix) ---
$conn = get_db_connection();
$products_db_result = $conn->query("SELECT product_id AS id, name, price FROM products");
$products_data = $products_db_result->fetch_all(MYSQLI_ASSOC);

// Helper function to find product details using the dynamic $products_data array
function find_product_by_id_checkout($products_array, $id) {
    foreach ($products_array as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}
// --- END: Dynamic Product Fetch ---

// Cart calculation
$cart_total = 0;
$cart_items = 0;
foreach ($_SESSION['cart'] ?? [] as $product_id => $quantity) {
    $product = find_product_by_id_checkout($products_data, $product_id);
    if ($product) {
        $cart_total += $product['price'] * $quantity;
        $cart_items += $quantity;
    }
}

// Shipping
$shipping = 100; 
$grand_total = $cart_total + $shipping;

// Ensure cart is not empty before proceeding
if (empty($_SESSION['cart'])) {
    // Immediate redirection if cart is empty.
    header('Location: shop.php');
    exit;
}

// Handle order submission (renamed to confirm_order for clarity)
$order_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    
    $errors = [];
    
    // Define a robust sanitize function
    function sanitize_checkout($input) {
        // Simple trim and htmlspecialchars for all inputs to prevent XSS
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Collect and sanitize all fields
    $first_name = sanitize_checkout($_POST['first_name'] ?? '');
    $last_name = sanitize_checkout($_POST['last_name'] ?? '');
    $email = sanitize_checkout($_POST['email'] ?? '');
    $phone = sanitize_checkout($_POST['phone'] ?? '');
    $address = sanitize_checkout($_POST['address'] ?? '');
    $city = sanitize_checkout($_POST['city'] ?? '');
    $zip = sanitize_checkout($_POST['zip'] ?? '');
    $payment_method = sanitize_checkout($_POST['payment_method'] ?? '');

    // --- Validation (P2 Fix: Added phone and ZIP validation) ---
    if (empty($first_name)) $errors[] = 'First name is required.';
    if (empty($last_name)) $errors[] = 'Last name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($address)) $errors[] = 'Address is required.';
    if (empty($city)) $errors[] = 'City is required.';
    if (empty($zip) || !preg_match('/^\d{4,10}$/', $zip)) $errors[] = 'Valid 4-10 digit ZIP code is required.';
    if (empty($phone) || !preg_match('/^(\+?\d{1,3}[\s-]?)?(\(?\d{3}\)?[\s-]?)?[\d\s-]{7,15}$/', $phone)) $errors[] = 'Valid phone number is required.';
    if (empty($payment_method)) $errors[] = 'Payment method is required.';
    if (empty($_SESSION['cart'])) $errors[] = 'Cart is empty.';

    if (empty($errors)) {
        // --- Database Insertion ---
        // Need to update the users table with the latest billing/contact info before inserting the order
        $user_id = $_SESSION['user_id'];
        $stmt_user = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, address = ? WHERE user_id = ?");
        // For simplicity, using address for the full combined address field
        $full_address = "$address, $city, $zip"; 
        $stmt_user->bind_param("sssssi", $first_name, $last_name, $email, $phone, $full_address, $user_id);
        $stmt_user->execute();
        $stmt_user->close();
        
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_fee, payment_method, order_status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("idds", $user_id, $grand_total, $shipping, $payment_method);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close(); 

        // Insert order items (using the dynamically fetched $products_data)
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = find_product_by_id_checkout($products_data, $product_id);
            if ($product) {
                $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                // Use the product's actual price at the time of order
                $stmt2->bind_param("iiid", $order_id, $product['id'], $quantity, $product['price']);
                $stmt2->execute();
                $stmt2->close();
            }
        }

        // Close DB connection after all insertions
        $conn->close();

        // Clear cart
        $_SESSION['cart'] = [];
        $order_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Order Placed Successfully!</strong> Thank you for your purchase. Redirecting to Order History...
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';

        header('refresh:3;url=order_history.php');
        exit;
    } else {
        $conn->close();
        $error_list = '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
        $order_message = '<div class="alert alert-danger"><strong>Error:</strong> The following issues need correction:' . $error_list . '</div>';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Scentified | Checkout</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
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
    
    .navbar-brand-container { display: flex; align-items: center; text-decoration: none; }
    .navbar-logo { max-height: 40px; width: auto; margin-right: 10px; transition: transform 0.3s ease; }
    .navbar-brand-text {
        color: var(--gold) !important;
        font-size: 1.8rem;
        font-weight: 700;
        font-family: 'Cinzel', serif;
        text-transform: uppercase;
    }
    
    /* Checkout Header */
    .checkout-header { 
        background: var(--luxury-gradient); 
        color: var(--light-gold); 
        padding: 4rem 0; 
        text-align: center;
        border-bottom: 3px solid var(--gold); 
    }
    .checkout-header h1 { font-family: 'Cinzel', serif; font-weight: 700; font-size: 2.5rem; }
    @media (min-width: 768px) {
        .checkout-header h1 { font-size: 4rem; }
    }
    .text-gold { color: var(--gold) !important; }

    /* Forms and Cards */
    .card-header { 
        background: var(--luxury-gradient); 
        color: var(--light-gold); 
    }
    .card-header h5 { font-family:'Cinzel', serif; font-size:1.3rem; }
    .form-label { font-family: 'Cinzel', serif; font-weight: 600; color: var(--charcoal); }
    .form-control, .form-select { border-radius: 0; padding: 0.8rem 1rem; }
    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25);
    }
    
    .btn-submit { 
        background: var(--gold); 
        color: var(--charcoal); 
        font-family: 'Cinzel', serif; 
        font-weight:700; 
        width:100%; 
        padding: 1rem;
        border-radius: 0;
        transition: all 0.3s ease;
    }
    .btn-submit:hover { 
        background: var(--charcoal); 
        color:var(--gold); 
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Order Summary */
    .order-summary { 
        background: var(--white); 
        padding:20px; 
        border-left:4px solid var(--gold); 
        border-radius:6px; 
        font-size:1.1rem; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .order-summary h5 { font-size:1.3rem; font-family:'Cinzel', serif; }
    .order-summary .p-3 { border-radius:4px; }

    /* Modal Styling */
    .modal-header-custom {
        background: var(--luxury-gradient);
        color: var(--light-gold);
        border-bottom: 2px solid var(--gold);
    }
    .modal-header-custom .btn-close {
        filter: invert(1);
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
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<header class="checkout-header">
    <div class="container">
        <h1>Secure Checkout</h1>
        <p>Complete your purchase</p>
    </div>
</header>

<main class="py-5 flex-grow-1">
<div class="container">
    <?php echo $order_message; ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h5>Billing Information</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" method="POST" action="checkout.php" novalidate>
                        <div class="row mb-3 g-3">
                            <!-- Mobile-First Columns -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address (Street/Line 1)</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                        <div class="row mb-3 g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" name="zip" required>
                            </div>
                        </div>
                        <h5 class="mt-4 mb-3">Payment Method</h5>
                        <div class="mb-3">
                            <!-- Payment Method Options -->
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="Credit/Debit Card" required>
                                <label class="form-check-label" for="credit_card">Credit/Debit Card</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="PayPal" required>
                                <label class="form-check-label" for="paypal">PayPal</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="Bank Transfer" required>
                                <label class="form-check-label" for="bank_transfer">Bank Transfer</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" required>
                                <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                            </div>
                        </div>
                        <button type="submit" id="placeOrderButton" class="btn btn-submit mt-4">Place Order</button>
                        <!-- Hidden field for final submission confirmation -->
                        <input type="hidden" name="confirm_order" id="confirmOrderHidden" value="0">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="order-summary sticky-top" style="top:80px;">
                <h5>Order Summary</h5>
                <div style="max-height:300px; overflow-y:auto; margin-bottom:20px;">
                    <?php foreach ($_SESSION['cart'] ?? [] as $product_id => $quantity): ?>
                        <?php 
                        $product = find_product_by_id_checkout($products_data, $product_id);
                        if ($product) {
                        ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($product['name']); ?> x<?php echo $quantity; ?></span>
                            <span>₱<?php echo number_format($product['price'] * $quantity, 2); ?></span>
                        </div>
                        <?php } ?>
                    <?php endforeach; ?>
                </div>
                <hr style="border-color:#daa520;opacity:0.3;">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>₱<?php echo number_format($cart_total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Shipping:</span>
                    <span>₱<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="p-3" style="background:#1c1c1c; color:#f0e68c; border-radius:4px; text-align:center;">
                    <h5 class="mb-1">Total Amount</h5>
                    <h3>₱<?php echo number_format($grand_total, 2); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<footer class="py-5 text-center text-white-50" style="background: linear-gradient(135deg, #0a0a0a 0%, #3a2c0f 100%);">
    &copy; <?php echo $currentYear; ?> Scentified. All rights reserved.
</footer>

<!-- Confirmation Modal Structure (Custom Pop-up) -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal-header-custom">
        <h5 class="modal-title" style="font-family: 'Cinzel', serif;">Simulated Payment Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Please enter a simulated confirmation code/transaction ID to finalize your order via **<span id="paymentMethodDisplay" class="fw-bold text-gold"></span>**.</p>
        <div class="mb-3">
          <label for="confirmationInput" class="form-label">Transaction ID / Confirmation Data:</label>
          <input type="text" class="form-control" id="confirmationInput" placeholder="Enter random numbers or letters" required>
        </div>
        <div id="confirmationError" class="text-danger small" style="display: none;">This field is required.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-submit" id="finalizePaymentButton">Finalize Payment</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('checkoutForm');
        const confirmOrderHidden = document.getElementById('confirmOrderHidden');
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const confirmationInput = document.getElementById('confirmationInput');
        const finalizePaymentButton = document.getElementById('finalizePaymentButton');
        const paymentMethodDisplay = document.getElementById('paymentMethodDisplay');
        const confirmationError = document.getElementById('confirmationError');

        // Payment methods that require the simulated confirmation pop-up
        const virtualPaymentMethods = ['Credit/Debit Card', 'PayPal', 'Bank Transfer'];

        // 1. Intercept the initial form submission
        form.addEventListener('submit', function(e) {
            
            // Trigger standard browser validation to ensure all required fields (including payment method) are filled.
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // If the form is already confirmed (after modal submission), let it pass to the server.
            if (confirmOrderHidden.value === '1') {
                return;
            }
            
            // Prevent standard submission to handle modal logic first.
            e.preventDefault();

            // Find the selected payment method (it must exist due to form.checkValidity())
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            const methodValue = selectedMethod.value;

            if (virtualPaymentMethods.includes(methodValue)) {
                // Stop, show modal for confirmation
                paymentMethodDisplay.textContent = methodValue;
                confirmationInput.value = ''; // Clear previous input
                confirmationError.style.display = 'none';
                confirmationModal.show();
            } else {
                // Cash on Delivery (COD) - Proceed directly
                confirmOrderHidden.value = '1';
                form.submit();
            }
        });

        // 2. Handle the "Finalize Payment" button click inside the modal
        finalizePaymentButton.addEventListener('click', function() {
            if (confirmationInput.value.trim() === '') {
                confirmationError.style.display = 'block';
                confirmationInput.focus();
                return;
            }

            // Simulated confirmation successful. Allow the form to submit.
            confirmationError.style.display = 'none';
            confirmationModal.hide();

            // Set hidden field and re-submit the original form
            confirmOrderHidden.value = '1';
            form.submit();
        });
        
        // Remove validation classes after the first failure (optional, but cleans up UI)
        form.addEventListener('change', function() {
            form.classList.remove('was-validated');
        });
    });
</script>
</body>
</html>