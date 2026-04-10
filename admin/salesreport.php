<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
// Try different paths
if(file_exists('../includes/dbconnection.php')) {
    include('../includes/dbconnection.php');
} elseif(file_exists(__DIR__ . '/../includes/dbconnection.php')) {
    include(__DIR__ . '/../includes/dbconnection.php');
} else {
    // Create connection directly
    try {
        $con = mysqli_connect("localhost", "root", "", "jilbertsalon");
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        // Also create PDO connection for prepared statements
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// If dbconnection.php uses mysqli, create PDO connection
if(!isset($dbh) && isset($con)) {
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("PDO connection failed: " . $e->getMessage());
    }
}

// Verify connection
if(!isset($dbh)) {
    die("Database connection could not be established. Please check your database configuration.");
}

// Check if admin is logged in
if (!isset($_SESSION['bpmsaid']) || strlen($_SESSION['bpmsaid']) == 0) {
    header('location:../admin/index.php');
    exit();
}

// Date filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'Completed';

// Build WHERE clause - Fixed to handle "All Status"
$where_conditions = [];
$params = [];

// Only add status filter if it's not empty (not "All Status")
if(!empty($status_filter)) {
    $where_conditions[] = "a.Status = :status";
    $params[':status'] = $status_filter;
}

if(!empty($start_date)) {
    $where_conditions[] = "a.AptDate >= :start_date";
    $params[':start_date'] = $start_date;
}

if(!empty($end_date)) {
    $where_conditions[] = "a.AptDate <= :end_date";
    $params[':end_date'] = $end_date;
}

$where_clause = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get sales data with service details - Fixed JOIN to include all appointments
$sql = "SELECT a.*, 
               COALESCE(s.Cost, 0) as Cost,
               COALESCE(s.ServiceName, a.Services) as ServiceName
        FROM tblappointment a 
        LEFT JOIN tblservices s ON TRIM(a.Services) = TRIM(s.ServiceName)
        $where_clause 
        ORDER BY a.AptDate DESC, a.AptTime DESC";
$query = $dbh->prepare($sql);
$query->execute($params);
$appointments = $query->fetchAll(PDO::FETCH_OBJ);

// Calculate totals
$total_revenue = 0;
$total_appointments = count($appointments);

foreach($appointments as $apt) {
    $total_revenue += (float)($apt->Cost ?? 0);
}

// Get summary by service - Fixed to properly group and handle all services
$summary_sql = "SELECT 
                    COALESCE(s.ServiceName, a.Services) as ServiceName,
                    COALESCE(s.Cost, 0) as Cost,
                    COUNT(a.ID) as count,
                    (COALESCE(s.Cost, 0) * COUNT(a.ID)) as revenue
                FROM tblappointment a 
                LEFT JOIN tblservices s ON TRIM(a.Services) = TRIM(s.ServiceName)
                $where_clause 
                GROUP BY COALESCE(s.ServiceName, a.Services), COALESCE(s.Cost, 0)
                ORDER BY revenue DESC";
$summary_query = $dbh->prepare($summary_sql);
$summary_query->execute($params);
$service_summary = $summary_query->fetchAll(PDO::FETCH_OBJ);

// Handle CSV Export
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="sales_report'. '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write UTF-8 BOM for proper Excel display
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write header
    fputcsv($output, ['Sales Report', 'From: ' . $start_date, 'To: ' . $end_date]);
    fputcsv($output, ['Status Filter:', empty($status_filter) ? 'All Status' : $status_filter]);
    fputcsv($output, []);
    fputcsv($output, ['Apt Number', 'Date', 'Time', 'Customer Name', 'Service', 'Price', 'Status']);
    
    // Write data
    foreach($appointments as $apt) {
        fputcsv($output, [
            $apt->AptNumber,
            $apt->AptDate,
            $apt->AptTime,
            $apt->Name,
            $apt->Services,
            '₱' . number_format($apt->Cost ?? 0, 2),
            $apt->Status
        ]);
    }
    
    // Write totals
    fputcsv($output, []);
    fputcsv($output, ['Total Appointments:', $total_appointments]);
    fputcsv($output, ['Total Revenue:', '₱' . number_format($total_revenue, 2)]);
    
    fclose($output);
    exit();
}

// Handle Print
$is_print = isset($_GET['print']) && $_GET['print'] == '1';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="\Jilbert-Salon\admin\printing.css">
    
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <style>
        .summary-card {
            transition: transform 0.2s;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: white !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>
    <?php if(!$is_print): ?>
    <?php include '../navs/sidenavbar.php'; ?>
    <?php endif; ?>

    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h1 class="m3-display-medium mb-0">Sales Report</h1>
                <div>
                    <button onclick="window.print()" class="btn-m3 btn-m3-tonal me-2">
                        <i class="bi bi-printer"></i> Print
                    </button>
                    <a href="?export=csv&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&status=<?php echo $status_filter; ?>" 
                       class="btn-m3 btn-m3-primary">
                        <i class="bi bi-file-earmark-excel"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Print Header (only visible when printing) -->
            <div class="d-none d-print-block text-center mb-4">
                <h2>Jilbert Salon - Sales Report</h2>
                <p>Period: <?php echo date('M d, Y', strtotime($start_date)); ?> to <?php echo date('M d, Y', strtotime($end_date)); ?></p>
                <p>Status: <?php echo empty($status_filter) ? 'All Status' : $status_filter; ?></p>
                <hr>
            </div>

            <!-- Filters -->
            <div class="row mt-4 no-print">
                <div class="col-lg-12">
                    <div class="m3-card surface-container-high p-4 mb-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?php echo $end_date; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="" <?php echo $status_filter == '' ? 'selected' : ''; ?>>All Status</option>
                                    <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn-m3 btn-m3-primary w-100">
                                    <i class="bi bi-funnel"></i> Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mt-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card success-container h-100 summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Revenue</h6>
                                    <h2 class="mb-0">₱<?php echo number_format($total_revenue, 2); ?></h2>
                                    <small><?php echo date('M d', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?></small>
                                </div>
                                <div>
                                    <i class="bi bi-currency-dollar fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card primary-container h-100 summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Appointments</h6>
                                    <h2 class="mb-0"><?php echo $total_appointments; ?></h2>
                                    <small>Status: <?php echo empty($status_filter) ? 'All' : $status_filter; ?></small>
                                </div>
                                <div>
                                    <i class="bi bi-calendar-check fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card secondary-container h-100 summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Average per Appointment</h6>
                                    <h2 class="mb-0">₱<?php echo $total_appointments > 0 ? number_format($total_revenue / $total_appointments, 2) : '0.00'; ?></h2>
                                    <small>Average revenue</small>
                                </div>
                                <div>
                                    <i class="bi bi-graph-up fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Summary -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="m3-card surface-container-high">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-bar-chart"></i> Revenue by Service
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" style="background:transparent;">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-end">Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(count($service_summary) > 0) {
                                            foreach($service_summary as $service): 
                                        ?>
                                        <tr>
                                            <td><?php echo htmlentities($service->ServiceName ?? 'Unknown Service'); ?></td>
                                            <td class="text-end">₱<?php echo number_format((float)($service->Cost ?? 0), 2); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?php echo (int)($service->count ?? 0); ?></span>
                                            </td>
                                            <td class="text-end"><strong>₱<?php echo number_format((float)($service->revenue ?? 0), 2); ?></strong></td>
                                        </tr>
                                        <?php 
                                            endforeach;
                                        } else {
                                            echo '<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">TOTAL REVENUE:</td>
                                            <td class="text-end">₱<?php echo number_format($total_revenue, 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Appointments -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="m3-card surface-container-high">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-list-ul"></i> Appointment Details
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" style="background:transparent;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Apt No.</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(count($appointments) > 0) {
                                            $cnt = 1;
                                            foreach($appointments as $apt): 
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt++; ?></td>
                                            <td><?php echo htmlentities($apt->AptNumber ?? 'N/A'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($apt->AptDate)); ?></td>
                                            <td><?php echo htmlentities($apt->AptTime ?? 'N/A'); ?></td>
                                            <td><?php echo htmlentities($apt->Name ?? 'N/A'); ?></td>
                                            <td><?php echo htmlentities($apt->Services ?? 'N/A'); ?></td>
                                            <td class="text-end">₱<?php echo number_format((float)($apt->Cost ?? 0), 2); ?></td>
                                            <td class="text-center">
                                                <?php 
                                                $badge_class = 'secondary';
                                                if($apt->Status == 'Completed') $badge_class = 'success';
                                                elseif($apt->Status == 'Cancelled') $badge_class = 'danger';
                                                elseif($apt->Status == 'Confirmed') $badge_class = 'primary';
                                                elseif($apt->Status == 'Pending') $badge_class = 'warning';
                                                ?>
                                                <span class="badge bg-<?php echo $badge_class; ?>">
                                                    <?php echo htmlentities($apt->Status ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php 
                                            endforeach;
                                        } else {
                                            echo '<tr><td colspan="8" class="text-center text-muted py-4">';
                                            echo '<i class="bi bi-inbox fs-1 d-block mb-2"></i>';
                                            echo 'No appointments found for the selected criteria';
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="6" class="text-end">TOTAL:</td>
                                            <td class="text-end">₱<?php echo number_format($total_revenue, 2); ?></td>
                                            <td class="text-center"><?php echo $total_appointments; ?> apts</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>