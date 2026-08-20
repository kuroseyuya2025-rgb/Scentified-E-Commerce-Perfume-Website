<?php
require_once 'config.php';
block_authenticated_access();

$errors = [];
$email = '';
$currentYear = date("Y");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        // Fetch user data including password hash
        $stmt = $conn->prepare("SELECT user_id, password_hash, username FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $hashed_password = $user['password_hash'];

            if (password_verify($password, $hashed_password)) {

                $_SESSION['user_id'] = $user['user_id'];

                // Admin check: Hardcoded to the admin email used in your SQL dump.
                if ($email === 'admin@scentified.com') { 
                    $_SESSION['is_admin'] = true;
                    header("Location: admin.php");
                    exit;
                } else {
                    $_SESSION['is_admin'] = false;
                    header("Location: home.php");
                    exit;
                }

            } else {
                $errors['login'] = 'Invalid email or password.';
            }

        } else {
            $errors['login'] = 'Invalid email or password.';
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
    <title>Scentified | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Cinzel:wght@400..700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --charcoal: #1c1c1c;
            --gold: #daa520;
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
        .auth-card {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem; /* Mobile padding */
            background-color: var(--white);
            border-top: 5px solid var(--gold);
        }
        @media (min-width: 768px) {
             .auth-card { padding: 3rem; }
        }
        .form-label { font-family: 'Cinzel', serif; font-weight: 600; color: var(--charcoal); }
        .form-control { border-radius: 0; padding: 0.8rem 1rem; }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.25rem rgba(218, 165, 32, 0.25);
        }
        .btn-login-submit {
            background: var(--luxury-gradient);
            color: var(--gold);
            border: 1px solid var(--gold);
            padding: 1rem;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }
        .btn-login-submit:hover {
            background: var(--gold);
            color: var(--charcoal);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.4);
        }
        .text-gold { color: var(--gold) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="slogo.jpg" alt="Scentified Logo" style="height:40px; margin-right:10px;" onerror="this.onerror=null;this.src='https://placehold.co/40x40/1c1c1c/daa520?text=S';">
            <span class="text-gold">Scentified</span>
        </a>
    </div>
</nav>

<main class="py-5 flex-grow-1">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Uses col-12 for full mobile width, scales down to col-md-6 on medium screens -->
            <div class="col-12 col-md-6">
                <div class="card auth-card">
                    <div class="card-body">
                        <h3 class="text-center mb-5" style="font-family: 'Cinzel', serif;">Access Your Exclusive World</h3>

                        <?php if (isset($errors['login'])): ?>
                            <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($errors['login']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="login.php" novalidate>
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-login-submit w-100 btn-lg">Login</button>
                            </div>
                            <div class="text-center pt-4">
                                <p class="text-muted">Don't have an account? <a href="register.php" class="text-gold">Register here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer-custom py-5 mt-auto text-white text-center">
    &copy; <?php echo $currentYear; ?> Scentified. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>