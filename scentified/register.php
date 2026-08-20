<?php
require_once 'config.php';
block_authenticated_access();

$errors = [];
$success_message = '';
$currentYear = date("Y");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize input
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $dob = trim($_POST['dob'] ?? '');

    // 2. Validate required fields
    if (empty($first_name)) $errors['first_name'] = 'First Name is required.';
    if (empty($last_name)) $errors['last_name'] = 'Last Name is required.';
    if (empty($username)) $errors['username'] = 'Username is required.';
    if (empty($email)) $errors['email'] = 'Email is required.';
    if (empty($password)) $errors['password'] = 'Password is required.';
    if (empty($confirm_password)) $errors['confirm_password'] = 'Confirm Password is required.';

    // 3. Validate format and uniqueness
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check for unique username and email if no other errors exist
    if (empty($errors)) {
        // Check Username Uniqueness
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['username'] = 'This username is already taken.';
        }
        $stmt->close();

        // Check Email Uniqueness
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = 'This email is already registered.';
        }
        $stmt->close();
    }

    // 4. Register user if no errors
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Use prepared statement for secure insertion
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, mobile_number, address, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssss", $first_name, $last_name, $username, $email, $password_hash, $mobile_number, $address, $dob);

        if ($stmt->execute()) {
            $success_message = "Registration successful! You can now log in.";
            // Clear input variables after success to empty the form
            $first_name = $last_name = $username = $email = $password = $confirm_password = $mobile_number = $address = $dob = '';
        } else {
            $errors['db'] = 'Database error: Could not complete registration.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scentified | Register</title>
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
        }
        

        .navbar-brand-container {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .navbar-logo { max-height: 40px; margin-right: 10px; }
        .navbar-brand-text {
            color: var(--gold) !important;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
        }
        

        .auth-card {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            background-color: var(--white);
            border-top: 5px solid var(--gold);
        }
        @media (min-width: 768px) {
             .auth-card { padding: 3rem; }
        }
        .form-label { font-family: 'Cinzel', serif; font-weight: 600; color: var(--charcoal); }
        .form-control, .form-select { border-radius: 0; padding: 0.8rem 1rem; }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25);
        }
        
        .btn-register-submit {
            background: var(--luxury-gradient);
            color: var(--gold);
            border: 1px solid var(--gold);
            padding: 1rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }
        .btn-register-submit:hover {
            background: var(--gold);
            color: var(--charcoal);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.4);
        }
        .text-gold { color: var(--gold) !important; }

        @media (max-width: 768px) {
            .navbar-logo { max-height: 30px; }
            .navbar-brand-text { font-size: 1.4rem; }
            .auth-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand navbar-brand-container" href="index.php">
                <img src="slogo.jpg" alt="Scentified Luxury Perfume Logo" class="navbar-logo" onerror="this.onerror=null;this.src='https://placehold.co/40x40/1c1c1c/daa520?text=S';">
                <span class="navbar-brand-text">Scentified</span>
            </a>
        </div>
    </nav>

    <main class="py-5 flex-grow-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="card auth-card">
                        <div class="card-body">
                            <h3 class="text-center mb-5" style="font-family: 'Cinzel', serif;">Create Your Luxury Account</h3>

                            <?php if ($success_message): ?>
                                <div class="alert alert-success text-center mb-4"><?php echo $success_message; ?> <a href="login.php" class="alert-link">Login here.</a></div>
                            <?php endif; ?>
                            
                            <?php if (!empty($errors) && !$success_message): ?>
                                <div class="alert alert-danger mb-4">
                                    Please correct the following errors:
                                    <ul>
                                    <?php foreach ($errors as $field => $msg): ?>
                                        <li><?php echo htmlspecialchars($msg); ?></li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="register.php" novalidate>
                                <div class="row g-4">
                         
                                    <div class="col-12 col-md-6">
                                        <label for="first_name" class="form-label">First Name <span class="text-gold">*</span></label>
                                        <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" required>
                                        <div class="invalid-feedback"><?php echo $errors['first_name'] ?? ''; ?></div>
                                    </div>
                                   
                                    <div class="col-12 col-md-6">
                                        <label for="last_name" class="form-label">Last Name <span class="text-gold">*</span></label>
                                        <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" required>
                                        <div class="invalid-feedback"><?php echo $errors['last_name'] ?? ''; ?></div>
                                    </div>
                    
                                    <div class="col-12 col-md-6">
                                        <label for="username" class="form-label">Username <span class="text-gold">*</span></label>
                                        <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                                        <div class="invalid-feedback"><?php echo $errors['username'] ?? ''; ?></div>
                                    </div>
                                 
                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-gold">*</span></label>
                                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                        <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                                    </div>
                   
                                    <div class="col-12 col-md-6">
                                        <label for="password" class="form-label">Password <span class="text-gold">*</span></label>
                                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                        <div class="form-text">Min 8 characters.</div>
                                        <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
                                    </div>
                                   
                                    <div class="col-12 col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-gold">*</span></label>
                                        <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                                        <div class="invalid-feedback"><?php echo $errors['confirm_password'] ?? ''; ?></div>
                                    </div>
                                   
                                    <div class="col-12 col-md-6">
                                        <label for="mobile_number" class="form-label">Mobile Number (Optional)</label>
                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($mobile_number ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <label for="dob" class="form-label">Date of Birth (Optional)</label>
                                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($dob ?? ''); ?>">
                                    </div>
                                 
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address (Optional)</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-register-submit w-100 btn-lg">Create Account</button>
                                    </div>
                                    <div class="col-12 text-center pt-3">
                                        <p class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none text-gold">Login here</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Our Story</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Help & Support</a></li>
                        <li><a href="login.php" class="text-white-50 text-decoration-none">Contact Us</a></li>
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
</body>
</html>