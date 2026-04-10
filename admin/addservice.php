<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
// Since addservice.php is in admin/ folder and includes.dbconnection.php is in root
$db_path = '/includes/dbconnection.php';

if(file_exists($db_path)) {
    include($db_path);
} else {
    // Create inline connection if file doesn't exist
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Verify connection was established
if(!isset($dbh)) {
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
$addedServiceName = '';
$addedServiceCost = 0;

// Handle form submission
if (isset($_POST['submit'])) {
    $serviceName = $_POST['serviceName'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    // Validate inputs
    if (empty($serviceName) || empty($cost)) {
        $showErrorModal = true;
        $modalMessage = 'Service Name and Cost are required!';
    } else {
        try {
            // Insert into database
            $sql = "INSERT INTO tblservices (ServiceName, Description, Cost) VALUES (:serviceName, :description, :cost)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':serviceName', $serviceName, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':cost', $cost, PDO::PARAM_INT);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $showSuccessModal = true;
                $modalMessage = 'Service has been added successfully!';
                $addedServiceName = $serviceName;
                $addedServiceCost = $cost;
            } else {
                $showErrorModal = true;
                $modalMessage = 'Something went wrong. Please try again.';
            }
        } catch(PDOException $e) {
            $showErrorModal = true;
            $modalMessage = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="modal.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <?php 
    if(file_exists('../navs/sidenavbar.php')) {
        include '../navs/sidenavbar.php'; 
    }
    ?>
    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="m3-display-medium">Add Service</h1>
                <a href="manageservice.php" class="btn-m3 btn-m3-tonal">
                    <i class="bi bi-arrow-left-circle"></i> Back to Services
                </a>
            </div>
            <div class="container my-5" style="max-width: 600px; margin: 0 auto;">
                <div class="m3-card primary-container p-4">
                    <form method="POST">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="serviceName" class="form-label">
                                    <i class="bi bi-scissors me-2"></i>Service Name <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" id="serviceName" name="serviceName" type="text"
                                    placeholder="e.g., Haircut, Facial, Manicure" aria-label="serviceName" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="description" class="form-label">
                                    <i class="bi bi-card-text me-2"></i>Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="Enter service description (optional)"></textarea>
                                <small class="form-text text-muted">Provide a detailed description of the service.</small>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="cost" class="form-label">
                                    <i class="bi bi-cash-coin me-2"></i>Service Price (₱) <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" id="cost" name="cost" type="number" 
                                    placeholder="Enter price in pesos" aria-label="cost" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center my-4">
                            <button type="reset" class="btn-m3 btn-m3-tonal me-3">
                                <i class="bi bi-x-circle me-2"></i>Clear Form
                            </button>
                            <button type="submit" name="submit" class="btn-m3 btn-m3-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Service Added Successfully!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <h4 class="mb-3 mt-3"><?php echo $modalMessage; ?></h4>
                    <?php if(!empty($addedServiceName)): ?>
                    <p class="text-muted mb-2">Service details:</p>
                    <div class="service-name-box">
                        <?php echo htmlentities($addedServiceName); ?>
                    </div>
                    <p class="text-success mt-3">
                        <i class="bi bi-currency-exchange me-2"></i>
                        <strong>Price: ₱<?php echo number_format($addedServiceCost, 2); ?></strong>
                    </p>
                    <?php endif; ?>
                    <p class="text-muted mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        The service is now available for booking.
                    </p>
                    <div class="countdown">
                        <i class="bi bi-clock me-1"></i>
                        Redirecting to services page in <span id="countdown">3</span> seconds...
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="addservice.php" class="btn btn-secondary">
                        <i class="bi bi-plus-circle me-2"></i>Add Another Service
                    </a>
                    <a href="manageservice.php" class="btn btn-primary">
                        <i class="bi bi-list-ul me-2"></i>View All Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-x-circle-fill error-icon"></i>
                    <h5 class="mt-3 mb-3"><?php echo $modalMessage; ?></h5>
                    <p class="text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Please check your inputs and try again.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        <?php if($showSuccessModal): ?>
        // Show success modal
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Countdown timer
            let seconds = 3;
            const countdownElement = document.getElementById('countdown');
            
            const countdownInterval = setInterval(function() {
                seconds--;
                countdownElement.textContent = seconds;
                
                if(seconds <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'manageservice.php';
                }
            }, 1000);
        });
        <?php endif; ?>

        <?php if($showErrorModal): ?>
        // Show error modal
        document.addEventListener('DOMContentLoaded', function() {
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
        <?php endif; ?>

        // Form validation feedback
        document.getElementById('cost').addEventListener('input', function(e) {
            if(this.value < 0) {
                this.value = 0;
            }
        });
    </script>
</body>

</html>