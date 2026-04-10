<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
$db_path = '../includes.dbconnection.php';
if(file_exists($db_path)) {
    include($db_path);
} else {
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Verify connection
if(!isset($dbh)) {
    die("Database connection variable not set. Please check your database connection file.");
}

// Check if admin is logged in
if (!isset($_SESSION['bpmsaid']) || strlen($_SESSION['bpmsaid']) == 0) {
    header('location:../index.php');
    exit();
}

// Handle Delete
if(isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    $sql = "DELETE FROM tblappointment WHERE ID=:rid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rid', $rid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Appointment deleted successfully');</script>";
    echo "<script>window.location.href = 'appointments.php'</script>";
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search and Sort
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ID';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Validate sort column
$allowed_sorts = ['ID', 'AptNumber', 'Name', 'AptDate', 'AptTime', 'Status', 'ApplyDate'];
if(!in_array($sort, $allowed_sorts)) {
    $sort = 'ID';
}

// Build query
$where_conditions = [];
if(!empty($search)) {
    $where_conditions[] = "(Name LIKE :search OR Email LIKE :search OR AptNumber LIKE :search OR PhoneNumber LIKE :search OR Services LIKE :search)";
}
if(!empty($status_filter)) {
    $where_conditions[] = "Status = :status";
}

$where = "";
if(count($where_conditions) > 0) {
    $where = "WHERE " . implode(" AND ", $where_conditions);
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM tblappointment $where";
$count_query = $dbh->prepare($count_sql);
if(!empty($search)) {
    $search_param = "%$search%";
    $count_query->bindParam(':search', $search_param, PDO::PARAM_STR);
}
if(!empty($status_filter)) {
    $count_query->bindParam(':status', $status_filter, PDO::PARAM_STR);
}
$count_query->execute();
$total_records = $count_query->fetch(PDO::FETCH_OBJ)->total;
$total_pages = ceil($total_records / $records_per_page);

// Fetch records
$sql = "SELECT * FROM tblappointment $where ORDER BY $sort $order LIMIT :limit OFFSET :offset";
$query = $dbh->prepare($sql);
if(!empty($search)) {
    $query->bindParam(':search', $search_param, PDO::PARAM_STR);
}
if(!empty($status_filter)) {
    $query->bindParam(':status', $status_filter, PDO::PARAM_STR);
}
$query->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Get status counts
$status_counts_sql = "SELECT Status, COUNT(*) as count FROM tblappointment GROUP BY Status";
$status_counts_query = $dbh->prepare($status_counts_sql);
$status_counts_query->execute();
$status_counts = $status_counts_query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    
    <style>
        .action-btn {
            padding: 5px 10px;
            margin: 0 2px;
        }
        .badge-status {
            font-size: 0.85em;
            padding: 5px 10px;
        }
        .status-filter-btn {
            margin: 2px;
        }
    </style>
</head>

<body>
    <?php
    if (file_exists('../navs/sidenavbar.php')) {
        include '../navs/sidenavbar.php';
    }
    ?>
    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="m3-display-medium">All Appointments</h1>
            </div>

            <!-- Status Filter Badges -->
            <div class="row mt-4">
                <div class="col-lg-12 mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?status=" class="btn-m3 <?php echo $status_filter == '' ? 'btn-m3-primary' : 'btn-m3-tonal'; ?> status-filter-btn">
                            All (<?php echo $total_records; ?>)
                        </a>
                        <?php foreach($status_counts as $status): ?>
                        <a href="?status=<?php echo urlencode($status->Status); ?>" 
                           class="btn-m3 <?php echo $status_filter == $status->Status ? 'btn-m3-primary' : 'btn-m3-tonal'; ?> status-filter-btn">
                            <?php echo htmlentities($status->Status); ?> (<?php echo $status->count; ?>)
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="container mb-3">
                    <div class="m3-card surface-container-high p-3">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <form method="GET" class="d-flex">
                                    <input type="hidden" name="status" value="<?php echo htmlentities($status_filter); ?>">
                                    <input class="form-control" type="search" name="search" 
                                           placeholder="Search appointments..." value="<?php echo htmlentities($search); ?>"
                                           aria-label="Search">
                                    <button class="btn btn-outline-success ms-2" type="submit">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <?php if(!empty($search)): ?>
                                    <a href="appointments.php?status=<?php echo urlencode($status_filter); ?>" class="btn btn-outline-secondary ms-2">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <div class="col-lg-6">
                                <form method="GET" class="d-flex justify-content-end">
                                    <input type="hidden" name="search" value="<?php echo htmlentities($search); ?>">
                                    <input type="hidden" name="status" value="<?php echo htmlentities($status_filter); ?>">
                                    <select name="sort" class="form-select me-2" style="width: auto;">
                                        <option value="ID" <?php echo $sort == 'ID' ? 'selected' : ''; ?>>ID</option>
                                        <option value="AptNumber" <?php echo $sort == 'AptNumber' ? 'selected' : ''; ?>>Apt Number</option>
                                        <option value="Name" <?php echo $sort == 'Name' ? 'selected' : ''; ?>>Name</option>
                                        <option value="AptDate" <?php echo $sort == 'AptDate' ? 'selected' : ''; ?>>Apt Date</option>
                                        <option value="Status" <?php echo $sort == 'Status' ? 'selected' : ''; ?>>Status</option>
                                        <option value="ApplyDate" <?php echo $sort == 'ApplyDate' ? 'selected' : ''; ?>>Apply Date</option>
                                    </select>
                                    <select name="order" class="form-select me-2" style="width: auto;">
                                        <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                                        <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                                    </select>
                                    <button type="submit" class="btn-m3 btn-m3-tonal ms-2">
                                        <i class="bi bi-sort-down"></i> Sort
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="m3-card secondary-container h-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover" style="background: transparent;">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Apt Number</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Service</th>
                                            <th scope="col">Apt Date</th>
                                            <th scope="col">Apt Time</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Apply Date</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if($query->rowCount() > 0) {
                                            $cnt = $offset + 1;
                                            foreach($results as $row) {
                                                // Status badge color
                                                $badge_class = 'bg-secondary';
                                                switch($row->Status) {
                                                    case 'Pending':
                                                        $badge_class = 'bg-warning text-dark';
                                                        break;
                                                    case 'Confirmed':
                                                        $badge_class = 'bg-info text-dark';
                                                        break;
                                                    case 'Completed':
                                                        $badge_class = 'bg-success';
                                                        break;
                                                    case 'Cancelled':
                                                        $badge_class = 'bg-danger';
                                                        break;
                                                }
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $cnt; ?></th>
                                            <td><strong><?php echo htmlentities($row->AptNumber); ?></strong></td>
                                            <td><?php echo htmlentities($row->Name); ?></td>
                                            <td><?php echo htmlentities($row->Email); ?></td>
                                            <td><?php echo htmlentities($row->PhoneNumber); ?></td>
                                            <td><?php echo htmlentities($row->Services); ?></td>
                                            <td><?php echo htmlentities(date('M d, Y', strtotime($row->AptDate))); ?></td>
                                            <td><?php echo htmlentities($row->AptTime); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badge_class; ?> badge-status">
                                                    <?php echo htmlentities($row->Status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlentities(date('M d, Y', strtotime($row->ApplyDate))); ?></td>
                                            <td>
                                                <a href="viewappointment.php?viewid=<?php echo $row->ID; ?>" 
                                                   class="btn-m3-icon btn-m3-icon-info action-btn" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="editappointment.php?editid=<?php echo $row->ID; ?>" 
                                                   class="btn-m3-icon btn-m3-icon-primary action-btn" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="appointments.php?delid=<?php echo $row->ID; ?>" 
                                                   class="btn-m3-icon btn-m3-icon-danger action-btn" 
                                                   onclick="return confirm('Are you sure you want to delete this appointment?');" 
                                                   title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php 
                                            $cnt++;
                                            }
                                        } else { 
                                        ?>
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">
                                                <em>No appointments found.</em>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if($total_pages > 1): ?>
            <div class="row mt-4 d-flex justify-content-center">
                <div class="col-lg-auto">
                    <div class="m3-card surface-container-high p-3 mb-0">
                        <nav aria-label="Page navigation" class="d-flex justify-content-center">
                            <ul class="pagination mb-0">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&status=<?php echo urlencode($status_filter); ?>" 
                                       aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for($i = $start_page; $i <= $end_page; $i++): 
                                ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&status=<?php echo urlencode($status_filter); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <!-- Next Button -->
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&status=<?php echo urlencode($status_filter); ?>" 
                                       aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <div class="text-center mt-2 small">
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo $total_records; ?> appointments
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>