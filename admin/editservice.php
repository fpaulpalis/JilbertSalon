<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
$db_path = '/includes/dbconnection.php';

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

if(!isset($dbh)) {
    die("Database connection variable not set. Please check your database connection file.");
}

if (!isset($_SESSION['bpmsaid']) || strlen($_SESSION['bpmsaid']) == 0) {
    header('location:../admin/index.php');
    exit();
}

$showSuccessModal = false;
$showErrorModal = false;
$modalMessage = "";
$serviceName = "";

if(isset($_GET['editid'])) {
    $eid = intval($_GET['editid']);
    
    if(isset($_POST['submit'])) {
        $serviceName = $_POST['serviceName'];
        $description = $_POST['description'];
        $cost = $_POST['cost'];
        
        if(empty($serviceName) || empty($cost)) {
            $showErrorModal = true;
            $modalMessage = "Service Name and Cost are required!";
        } else {
            try {
                $sql = "UPDATE tblservices SET ServiceName=:serviceName, Description=:description, Cost=:cost WHERE ID=:eid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':serviceName', $serviceName, PDO::PARAM_STR);
                $query->bindParam(':description', $description, PDO::PARAM_STR);
                $query->bindParam(':cost', $cost, PDO::PARAM_INT);
                $query->bindParam(':eid', $eid, PDO::PARAM_INT);
                $query->execute();
                
                $showSuccessModal = true;
                $modalMessage = "Service has been updated successfully!";
            } catch(PDOException $e) {
                $showErrorModal = true;
                $modalMessage = "Error: " . $e->getMessage();
            }
        }
    }
    
    $sql = "SELECT * FROM tblservices WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if(!$result) {
        $showErrorModal = true;
        $modalMessage = "Service not found!";
    }
} else {
    header('location:manageservice.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>

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
    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="m3-display-medium">Edit Service</h1>
                <a href="manageservice.php" class="btn-m3 btn-m3-tonal">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            
            <div class="container my-5" style="max-width: 600px; margin: 0 auto;">
                <div class="m3-card primary-container p-4">
                <form method="POST">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input class="form-control" id="serviceName" name="serviceName" type="text"
                                aria-label="serviceName" value="<?php echo htmlentities($result->ServiceName ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlentities($result->Description ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="cost" class="form-label">Cost</label>
                            <input class="form-control" id="cost" name="cost" type="number" aria-label="cost" 
                                min="0" step="1" value="<?php echo htmlentities($result->Cost ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5 mb-2">
                        <button type="submit" name="submit" class="btn-m3 btn-m3-primary w-100 py-3">
                            <i class="bi bi-check-circle"></i> UPDATE SERVICE
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="bi bi-check-circle-fill me-2"></i>Success!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="success-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4 class="mt-3"><?php echo $modalMessage; ?></h4>
                    <?php if($serviceName): ?>
                    <div class="service-name-box mt-3">
                        <?php echo htmlentities($serviceName); ?>
                    </div>
                    <?php endif; ?>
                    <div class="countdown mt-3">
                        <i class="bi bi-arrow-clockwise"></i> Redirecting in <span id="countdown">3</span> seconds...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="window.location.href='manageservice.php'">
                        <i class="bi bi-arrow-left"></i> Go to Services List
                    </button>
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
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="error-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h4 class="mt-3"><?php echo $modalMessage; ?></h4>
                    <p class="text-muted mt-3">Please try again or contact support if the problem persists.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php 
    if(file_exists('../navs/sidenavbar.php')) {
        include '../navs/sidenavbar.php'; 
    }
    ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

<script>
    <?php if($showSuccessModal): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            if(countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'manageservice.php';
            }
        }, 1000);
    });
    <?php endif; ?>

    <?php if($showErrorModal): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    });
    <?php endif; ?>
</script>

</html>