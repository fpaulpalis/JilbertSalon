<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
// Since addservice.php is in admin/ folder and includes.dbconnection.php is in root
$db_path = '/includes/dbconnection.php';

if (file_exists($db_path)) {
    include($db_path);
} else {
    // Create inline connection if file doesn't exist
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Verify connection was established
if (!isset($dbh)) {
    die("Database connection variable not set. Please check your database connection file.");
}

// Check if admin is logged in - Fixed the condition
if (!isset($_SESSION['bpmsaid']) || strlen($_SESSION['bpmsaid']) == 0) {
    header('location:../admin/index.php');
    exit();
}

// Initialize modal variables
$showSuccessModal = false;
$showErrorModal = false;
$modalMessage = '';
$deletedServiceName = '';

// Handle Delete
if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);

    try {
        // Get service name before deleting
        $sql_get = "SELECT ServiceName FROM tblservices WHERE ID=:rid";
        $query_get = $dbh->prepare($sql_get);
        $query_get->bindParam(':rid', $rid, PDO::PARAM_INT);
        $query_get->execute();
        $service = $query_get->fetch(PDO::FETCH_OBJ);

        if ($service) {
            $deletedServiceName = $service->ServiceName;

            // Delete the service
            $sql = "DELETE FROM tblservices WHERE ID=:rid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':rid', $rid, PDO::PARAM_INT);
            $query->execute();

            $showSuccessModal = true;
            $modalMessage = 'Service deleted successfully!';
        } else {
            $showErrorModal = true;
            $modalMessage = 'Service not found!';
        }
    } catch (PDOException $e) {
        $showErrorModal = true;
        $modalMessage = 'Error deleting service: ' . $e->getMessage();
    }
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search and Sort
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ID';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column
$allowed_sorts = ['ID', 'ServiceName', 'Cost', 'CreationDate'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'ID';
}

// Build query
$where = "";
if (!empty($search)) {
    $where = "WHERE ServiceName LIKE :search OR Description LIKE :search OR Cost LIKE :search";
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM tblservices $where";
$count_query = $dbh->prepare($count_sql);
if (!empty($search)) {
    $search_param = "%$search%";
    $count_query->bindParam(':search', $search_param, PDO::PARAM_STR);
}
$count_query->execute();
$total_records = $count_query->fetch(PDO::FETCH_OBJ)->total;
$total_pages = ceil($total_records / $records_per_page);

// Fetch records
$sql = "SELECT * FROM tblservices $where ORDER BY $sort $order LIMIT :limit OFFSET :offset";
$query = $dbh->prepare($sql);
if (!empty($search)) {
    $query->bindParam(':search', $search_param, PDO::PARAM_STR);
}
$query->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="modal.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">


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
                <h1 class="m3-display-medium">All Services</h1>
                <a href="addservice.php" class="btn-m3 btn-m3-primary d-none d-md-inline-flex">
                    <i class="bi bi-plus-circle"></i> Add New Service
                </a>
            </div>

            <div class="row mt-4">
                <div class="container mb-3">
                    <div class="m3-card surface-container-high p-3">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <form method="GET" class="d-flex">
                                    <input class="form-control" type="search" name="search"
                                        placeholder="Search services..." value="<?php echo htmlentities($search); ?>"
                                        aria-label="Search">
                                    <button class="btn btn-outline-success ms-2" type="submit">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="manageservice.php" class="btn btn-outline-secondary ms-2">
                                            <i class="bi bi-x-circle"></i> Clear
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <div class="col-lg-6">
                                <form method="GET" class="d-flex justify-content-end">
                                    <input type="hidden" name="search" value="<?php echo htmlentities($search); ?>">
                                    <select name="sort" class="form-select me-2" style="width: auto;">
                                        <option value="ID" <?php echo $sort == 'ID' ? 'selected' : ''; ?>>ID</option>
                                        <option value="ServiceName" <?php echo $sort == 'ServiceName' ? 'selected' : ''; ?>>Name</option>
                                        <option value="Cost" <?php echo $sort == 'Cost' ? 'selected' : ''; ?>>Price
                                        </option>
                                        <option value="CreationDate" <?php echo $sort == 'CreationDate' ? 'selected' : ''; ?>>Date</option>
                                    </select>
                                    <select name="order" class="form-select me-2" style="width: auto;">
                                        <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Ascending
                                        </option>
                                        <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Descending
                                        </option>
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
                                            <th scope="col">Service Name</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Service Price</th>
                                            <th scope="col">Creation Date</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($query->rowCount() > 0) {
                                            $cnt = $offset + 1;
                                            foreach ($results as $row) {
                                                ?>
                                                <tr>
                                                    <th scope="row"><?php echo $cnt; ?></th>
                                                    <td><?php echo htmlentities($row->ServiceName); ?></td>
                                                    <td><?php echo htmlentities(substr($row->Description, 0, 50)) . (strlen($row->Description) > 50 ? '...' : ''); ?>
                                                    </td>
                                                    <td>₱<?php echo htmlentities(number_format($row->Cost, 2)); ?></td>
                                                    <td><?php echo htmlentities(date('M d, Y', strtotime($row->CreationDate))); ?>
                                                    </td>
                                                    <td>
                                                        <a href="editservice.php?editid=<?php echo $row->ID; ?>"
                                                            class="btn-m3-icon btn-m3-icon-primary action-btn" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <button type="button" class="btn-m3-icon btn-m3-icon-danger action-btn"
                                                            onclick="confirmDelete(<?php echo $row->ID; ?>, '<?php echo htmlentities(addslashes($row->ServiceName)); ?>')"
                                                            title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cnt++;
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <em>No services found.</em>
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

            <?php if ($total_pages > 1): ?>
                <div class="row mt-4 d-flex justify-content-center">
                    <div class="col-lg-auto">
                        <div class="m3-card surface-container-high p-3 mb-0">
                            <nav aria-label="Page navigation " class="d-flex justify-content-center">
                                <ul class="pagination mb-0">
                                    <!-- Previous Button -->
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"
                                            aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <?php
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);

                                    for ($i = $start_page; $i <= $end_page; $i++):
                                        ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Next Button -->
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"
                                            aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="text-center mt-2 small">
                                Showing <?php echo $offset + 1; ?> to
                                <?php echo min($offset + $records_per_page, $total_records); ?> of
                                <?php echo $total_records; ?> services
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade confirm-modal" id="confirmDeleteModal" tabindex="-1"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash-fill" style="font-size: 4rem; color: #dc3545;"></i>
                    <h5 class="mt-3 mb-3">Are you sure you want to delete this service?</h5>
                    <div class="service-name-box" id="serviceNameToDelete"></div>
                    <p class="text-danger mt-3">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>This action cannot be undone!</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Yes, Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Success!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <h4 class="mb-3 mt-3"><?php echo $modalMessage; ?></h4>
                    <?php if (!empty($deletedServiceName)): ?>
                        <p class="text-muted mb-2">Service deleted:</p>
                        <div class="service-name-box">
                            <?php echo htmlentities($deletedServiceName); ?>
                        </div>
                    <?php endif; ?>
                    <div class="countdown">
                        <i class="bi bi-clock me-1"></i>
                        This window will close in <span id="countdown">3</span> seconds...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-x-circle-fill error-icon"></i>
                    <h5 class="mt-3"><?php echo $modalMessage; ?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Mobile FAB for Add Service -->
    <div class="m3-fab-container d-md-none">
        <a href="addservice.php" class="btn-m3-fab">
            <span class="material-symbols-outlined">add</span>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        // Confirm Delete Function
        function confirmDelete(serviceId, serviceName) {
            document.getElementById('serviceNameToDelete').textContent = serviceName;
            document.getElementById('confirmDeleteBtn').href = 'manageservice.php?delid=' + serviceId;

            const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            confirmModal.show();
        }

        <?php if ($showSuccessModal): ?>
            // Show success modal
            document.addEventListener('DOMContentLoaded', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Countdown timer
                let seconds = 3;
                const countdownElement = document.getElementById('countdown');

                const countdownInterval = setInterval(function () {
                    seconds--;
                    countdownElement.textContent = seconds;

                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        successModal.hide();
                        window.location.href = 'manageservice.php';
                    }
                }, 1000);
            });
        <?php endif; ?>

        <?php if ($showErrorModal): ?>
            // Show error modal
            document.addEventListener('DOMContentLoaded', function () {
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            });
        <?php endif; ?>
    </script>
</body>

</html>