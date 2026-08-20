<?php
require_once 'config.php';
protect_page(); // Ensure user is logged in

// Only admin can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: home.php");
    exit;
}

// --- Database Connection ---
$conn = get_db_connection();

// --- Handle Status Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = ($_POST['status'] === 'Delivered') ? 'Delivered' : 'Pending';

    // Securely update the order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    // No exit here, continue to fetch updated reports
}

// --- --- DATE HANDLING FOR DAILY REPORT --- ---
$selected_date = date('Y-m-d'); // Default to today's date

// Check if a specific date was requested via GET request (e.g., from the date picker form)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['report_date']) && !empty($_GET['report_date'])) {
    $input_date = trim($_GET['report_date']);
    // Validate the date format (YYYY-MM-DD)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input_date)) {
        $selected_date = $input_date;
    }
}
$daily_report_date = $selected_date; // The date used for the specialized daily report

/**
 * Fetches comprehensive sales data for a specific date.
 * @param mysqli $conn Database connection object.
 * @param string $date_str Date in YYYY-MM-DD format.
 * @return array Report data including totals, most sold product, and a product list.
 */
function fetch_daily_report_by_date($conn, $date_str) {
    $start_of_day = $date_str . ' 00:00:00';
    $end_of_day = $date_str . ' 23:59:59';
    
    $report_data = [
        'total_income' => 0.00,
        'total_units_sold' => 0,
        'most_sold_product' => ['name' => 'N/A', 'quantity' => 0],
        'products_sold_list' => [], // List of products sold with quantities
    ];

    // Query 1: Calculate Total Income and Total Units Sold for the day
    $summary_query = "
        SELECT 
            SUM(o.total_amount) AS total_income,
            SUM(oi.quantity) AS total_units_sold
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.created_at BETWEEN ? AND ?
    ";
    
    $stmt = $conn->prepare($summary_query);
    $stmt->bind_param("ss", $start_of_day, $end_of_day);
    $stmt->execute();
    $summary_result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($summary_result) {
        $report_data['total_income'] = $summary_result['total_income'] ?? 0.00;
        $report_data['total_units_sold'] = $summary_result['total_units_sold'] ?? 0;
    }
    
    // Query 2: Get detailed product breakdown and determine the most sold product
    $detail_query = "
        SELECT 
            p.name AS product_name,
            SUM(oi.quantity) AS total_quantity
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.created_at BETWEEN ? AND ?
        GROUP BY p.product_id, p.name
        ORDER BY total_quantity DESC
    ";
    
    $stmt = $conn->prepare($detail_query);
    $stmt->bind_param("ss", $start_of_day, $end_of_day);
    $stmt->execute();
    $detail_result = $stmt->get_result();
    $stmt->close();
    
    $is_first = true;
    while ($row = $detail_result->fetch_assoc()) {
        $report_data['products_sold_list'][] = $row;
        
        // Since the query is ordered by total_quantity DESC, the first row is the most sold.
        if ($is_first) {
            $report_data['most_sold_product']['name'] = htmlspecialchars($row['product_name']);
            $report_data['most_sold_product']['quantity'] = $row['total_quantity'];
            $is_first = false;
        }
    }

    return $report_data;
}

// Execute the new daily report function
$daily_report = fetch_daily_report_by_date($conn, $daily_report_date);


// --- --- EXISTING REPORTING QUERIES (KPIs) --- ---

// Helper function to execute queries for earnings and best sellers based on time interval
function fetch_report($conn, $interval_sql, $limit_best_sellers = true) {
    // 1. Total Earnings
    $earnings_query = "
        SELECT SUM(total_amount) AS total_earnings
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $interval_sql)
    ";
    $earnings_result = $conn->query($earnings_query)->fetch_assoc();
    $earnings = $earnings_result['total_earnings'] ?? 0;

    // 2. Most Sold Product
    $best_seller_query = "
        SELECT p.name AS product_name, SUM(oi.quantity) AS total_sold
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.created_at >= DATE_SUB(CURDATE(), INTERVAL $interval_sql)
        GROUP BY p.product_id, p.name
        ORDER BY total_sold DESC
        " . ($limit_best_sellers ? "LIMIT 1" : "LIMIT 5");

    $best_seller_result = $conn->query($best_seller_query);
    
    // If limiting to 1, return string, otherwise return array
    if ($limit_best_sellers) {
        $best_seller = $best_seller_result->fetch_assoc();
        return [
            'earnings' => $earnings,
            'product_name' => $best_seller['product_name'] ?? 'N/A',
            'total_sold' => $best_seller['total_sold'] ?? 0
        ];
    } else {
        return $best_seller_result;
    }
}

// Fetch data for KPI boxes (existing functionality)
$report_day = fetch_report($conn, '1 DAY', true);
$report_week = fetch_report($conn, '1 WEEK', true);
$report_month = fetch_report($conn, '1 MONTH', true);
$report_year = fetch_report($conn, '1 YEAR', true);


// --- Fetch Orders (for main table) ---
$orders_result = $conn->query("
    SELECT o.order_id, u.username AS customer_name, o.total_amount, o.order_status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
");


// --- Fetch Order Items with Product Names (for items table) ---
$order_items_result = $conn->query("
    SELECT oi.order_id, p.name AS product_name, oi.quantity, oi.price, (oi.quantity * oi.price) AS subtotal
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    ORDER BY oi.order_id DESC
");

// --- Most Bought Products (Top 5 Overall) ---
$most_bought_result = fetch_report($conn, '100 YEAR', false); // Use large interval to get overall top 5

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel — Orders & Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Using Cinzel font for admin panel headers, consistent with site theme -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; }
        h1, h2, h3, h4 { font-family: 'Cinzel', serif; color: #1c1c1c; }
        .navbar { background: #1c1c1c; }
        .navbar-brand, .nav-link { color: #daa520 !important; font-family: 'Cinzel', serif; }
        .nav-link:hover { color: #f0e68c !important; }
        .table thead th { background-color: #343a40; color: #fff; }
        .table-bordered { border-color: #dee2e6; }
        .container { max-width: 1200px; }
        /* Style for KPI Cards */
        .kpi-card {
            background-color: #ffffff;
            border-left: 5px solid #daa520; /* Gold accent default */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
        }
        .kpi-title { font-size: 0.9rem; color: #6c757d; font-family: sans-serif; }
        .kpi-value { font-size: 1.5rem; font-weight: 700; color: #1c1c1c; }
        .kpi-product { font-size: 1.1rem; font-weight: 600; color: #daa520; }
    </style>
</head>
<body>

    <!-- Admin Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid container">
            <a class="navbar-brand" href="admin.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="adminNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container my-5">
    <h1>Admin Dashboard — Sales Reporting</h1>

    <!-- --- DAILY SALES REPORT FEATURE --- -->
    <h2 class="mt-5 mb-3">Daily Sales Report</h2>

    <!-- Date Picker Form -->
    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" action="admin.php">
            <div class="row align-items-end g-3">
                <div class="col-md-3">
                    <label for="report_date" class="form-label fw-bold">Select Date</label>
                    <input type="date" id="report_date" name="report_date" class="form-control" 
                           value="<?= htmlspecialchars($daily_report_date) ?>" required>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-dark" style="background-color: #1c1c1c; border-color: #1c1c1c;">
                        View Report
                    </button>
                </div>
                <div class="col-md-5">
                    <p class="mt-2 text-muted small mb-0">
                        Report generated for: <strong class="text-dark"><?= date('F j, Y', strtotime($daily_report_date)) ?></strong>
                    </p>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Report Results Cards -->
    <div class="row">
        <!-- Total Income & Units Sold -->
        <div class="col-md-6 mb-4">
            <div class="kpi-card bg-success-subtle" style="border-left: 5px solid #198754;">
                <h3 class="kpi-title text-success">Total Income</h3>
                <div class="kpi-value">₱<?= number_format($daily_report['total_income'], 2) ?></div>
                <div class="kpi-product mt-2 text-dark">Total Products Sold: 
                    <span class="fw-bold text-success"><?= $daily_report['total_units_sold'] ?> unit(s)</span>
                </div>
            </div>
        </div>
        
        <!-- Most Sold Product -->
        <div class="col-md-6 mb-4">
            <div class="kpi-card bg-warning-subtle" style="border-left: 5px solid #ffc107;">
                <h3 class="kpi-title text-warning">Most Sold Product</h3>
                <div class="kpi-product" style="font-size: 1.5rem; color: #000;">
                    <?= $daily_report['most_sold_product']['name'] ?>
                </div>
                <div class="kpi-value" style="color: #495057; font-size: 1.2rem;">
                    Quantity: <strong class="text-warning-emphasis"><?= $daily_report['most_sold_product']['quantity'] ?> unit(s)</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Breakdown Table -->
    <h4 class="mt-3">Daily Product Sales Breakdown (List of Products Sold)</h4>
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered shadow-sm">
            <thead>
                <tr class="table-dark">
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($daily_report['products_sold_list']) > 0): ?>
                    <?php foreach ($daily_report['products_sold_list'] as $product): ?>
                        <tr class="<?= ($product['product_name'] === $daily_report['most_sold_product']['name']) ? 'table-warning fw-bold' : '' ?>">
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td><?= $product['total_quantity'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted fst-italic">No sales recorded on <?= date('F j, Y', strtotime($daily_report_date)) ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- --- END DAILY SALES REPORT FEATURE --- -->
    
    
    <!-- --- REPORTING KPIS: SALES AND BEST SELLERS BY TIME PERIOD --- -->
    <h2 class="mt-5 mb-4">Historical Performance Metrics</h2>

    <div class="row">
        <?php 
        $reports = ['Day' => $report_day, 'Week' => $report_week, 'Month' => $report_month, 'Year' => $report_year];
        foreach ($reports as $label => $report):
        ?>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-title">Total Earnings This <?= $label ?></div>
                <div class="kpi-value">₱<?= number_format($report['earnings'], 2) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row mt-3">
        <?php 
        foreach ($reports as $label => $report):
        ?>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-title">Best Seller This <?= $label ?> (Units Sold)</div>
                <div class="kpi-product"><?= htmlspecialchars($report['product_name']) ?></div>
                <div class="kpi-value" style="font-size: 1.2rem;"><?= $report['total_sold'] ?> unit(s)</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <!-- --- END REPORTING KPIS --- -->


    <!-- MOST BOUGHT PRODUCTS (Overall Top 5) -->
    <h2 class="mt-5">Most Bought Products (Top 5 Overall)</h2>
    <table class="table table-striped table-bordered shadow-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Sold</th>
            </tr>
        </thead>
        <tbody>
        <?php while($mb = $most_bought_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($mb['product_name']) ?></td>
                <td><?= $mb['total_sold'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- ORDERS TABLE -->
    <h2 class="mt-5">All Orders</h2>
    <table class="table table-bordered table-responsive shadow-sm">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total (₱)</th>
                <th>Status</th>
                <th>Date</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
        <?php while($o = $orders_result->fetch_assoc()): ?>
            <tr>
                <td><?= $o['order_id'] ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td>₱<?= number_format($o['total_amount'],2) ?></td>
                <td>
                    <?php 
                        $status_class = match($o['order_status']) {
                            'Delivered' => 'text-success fw-bold',
                            'Pending' => 'text-warning fw-bold',
                            default => 'text-info',
                        };
                    ?>
                    <span class="<?= $status_class ?>"><?= $o['order_status'] ?></span>
                </td>
                <td><?= $o['created_at'] ?></td>
                <td>
                    <form method="POST" action="admin.php" style="display:flex; gap:5px;">
                        <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="Pending" <?= $o['order_status']=='Pending'?'selected':'' ?>>Pending</option>
                            <option value="Delivered" <?= $o['order_status']=='Delivered'?'selected':'' ?>>Delivered</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- ORDER ITEMS TABLE -->
    <h2 class="mt-5">Order Items Breakdown</h2>
    <table class="table table-bordered table-responsive shadow-sm">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price (₱)</th>
                <th>Subtotal (₱)</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        // Reset pointer for re-displaying items if needed, or better: 
        // Re-run the query or use a new query for this specific section in a real app. 
        // Since the original code was not looping through these, I'll assume they are needed as-is.
        $order_items_result->data_seek(0);
        while($i = $order_items_result->fetch_assoc()): ?>
            <tr>
                <td><?= $i['order_id'] ?></td>
                <td><?= htmlspecialchars($i['product_name']) ?></td>
                <td><?= $i['quantity'] ?></td>
                <td>₱<?= number_format($i['price'],2) ?></td>
                <td>₱<?= number_format($i['subtotal'],2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Simple Footer Placeholder -->
<footer class="bg-dark text-white-50 py-3 mt-5">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> Admin Panel. All rights reserved.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>