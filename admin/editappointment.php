<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if (!isset($_SESSION['bpmsaid']) || strlen($_SESSION['bpmsaid']) == 0) {
    header('location:../index.php');
    exit();
}

$showSuccessModal = false;
$showErrorModal = false;
$modalMessage = "";
$aptNumber = "";

if(isset($_GET['editid'])) {
    $eid = intval($_GET['editid']);
    
    if(isset($_POST['submit'])) {
        $status = $_POST['status'];
        $remark = $_POST['remark'];
        
        try {
            $sql = "UPDATE tblappointment SET Status=:status, Remark=:remark, RemarkDate=CURRENT_TIMESTAMP WHERE ID=:eid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':remark', $remark, PDO::PARAM_STR);
            $query->bindParam(':eid', $eid, PDO::PARAM_INT);
            $query->execute();
            
            // Get appointment number for modal
            $apt_sql = "SELECT AptNumber FROM tblappointment WHERE ID=:eid";
            $apt_query = $dbh->prepare($apt_sql);
            $apt_query->bindParam(':eid', $eid, PDO::PARAM_INT);
            $apt_query->execute();
            $apt_result = $apt_query->fetch(PDO::FETCH_OBJ);
            $aptNumber = $apt_result->AptNumber;
            
            $showSuccessModal = true;
            $modalMessage = "Appointment has been updated successfully!";
        } catch(PDOException $e) {
            $showErrorModal = true;
            $modalMessage = "Error: " . $e->getMessage();
        }
    }
    
    $sql = "SELECT * FROM tblappointment WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if(!$result) {
        $showErrorModal = true;
        $modalMessage = "Appointment not found!";
    }
} else {
    header('location:appointments.php');
    exit();
}

$services_sql = "SELECT ServiceName FROM tblservices ORDER BY ServiceName ASC";
$services_query = $dbh->prepare($services_sql);
$services_query->execute();
$services = $services_query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>

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
    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="m3-display-medium">Edit Appointment</h1>
                <a href="appointments.php" class="btn-m3 btn-m3-tonal">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            
            <div class="container my-5" style="max-width: 800px; margin: 0 auto;">
                <div class="m3-card secondary-container mb-4">
                    <h5 class="m3-title-large mb-3">Current Appointment Information</h5>
                    <div class="card-body px-0 py-2">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Appointment #:</strong> <?php echo htmlentities($result->AptNumber ?? ''); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlentities($result->Name ?? ''); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlentities($result->Email ?? ''); ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Phone:</strong> <?php echo htmlentities($result->PhoneNumber ?? ''); ?></p>
                                <p><strong>Service:</strong> <?php echo htmlentities($result->Services ?? ''); ?></p>
                                <p><strong>Applied:</strong> <?php echo htmlentities(date('M d, Y', strtotime($result->ApplyDate ?? 'now'))); ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Apt Date:</strong> <?php echo htmlentities(date('M d, Y', strtotime($result->AptDate ?? 'now'))); ?></p>
                                <p><strong>Apt Time:</strong> <?php echo htmlentities($result->AptTime ?? ''); ?></p>
                                <p><strong>Current Status:</strong> 
                                    <span class="badge <?php 
                                    switch($result->Status ?? '') {
                                        case 'Pending': echo 'bg-warning text-dark'; break;
                                        case 'Confirmed': echo 'bg-info text-dark'; break;
                                        case 'Completed': echo 'bg-success'; break;
                                        case 'Cancelled': echo 'bg-danger'; break;
                                        default: echo 'bg-secondary';
                                    }
                                    ?>">
                                        <?php echo htmlentities($result->Status ?? ''); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="m3-card primary-container">
                    <h5 class="m3-title-large mb-3">Update Status & Remark</h5>
                    <div class="card-body px-0 py-2">
                        <form method="POST">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Pending" <?php echo ($result->Status ?? '') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo ($result->Status ?? '') == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Completed" <?php echo ($result->Status ?? '') == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo ($result->Status ?? '') == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <small class="text-muted">Select the current status of this appointment</small>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="remark" class="form-label">Remark / Notes</label>
                                    <textarea class="form-control" id="remark" name="remark" rows="4" 
                                              placeholder="Add notes about this appointment (optional)"><?php echo ($result->Remark ?? '') ? htmlentities($result->Remark) : ''; ?></textarea>
                                    <small class="text-muted">Add any additional notes or comments about this appointment</small>
                                </div>
                            </div>

                            <?php if(isset($result->RemarkDate) && $result->RemarkDate): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Last updated: <?php echo htmlentities(date('F d, Y h:i A', strtotime($result->RemarkDate))); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-center my-4">
                                <button type="submit" name="submit" class="btn-m3 btn-m3-primary px-5 py-3">
                                    <i class="bi bi-check-circle"></i> UPDATE APPOINTMENT
                                </button>
                            </div>
                        </form>
                    </div>
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
                    <?php if($aptNumber): ?>
                    <div class="appointment-number mt-3">
                        APPOINTMENT #<?php echo htmlentities($aptNumber); ?>
                    </div>
                    <?php endif; ?>
                    <p class="text-muted mt-3">The appointment status and remarks have been successfully updated.</p>
                    <div class="countdown mt-3">
                        <i class="bi bi-arrow-clockwise"></i> Redirecting in <span id="countdown">3</span> seconds...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="window.location.href='appointments.php'">
                        <i class="bi bi-arrow-left"></i> Go to Appointments List
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
                    <button type="button" class="btn btn-danger" onclick="window.location.href='appointments.php'">
                        <i class="bi bi-arrow-left"></i> Back to Appointments
                    </button>
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
                window.location.href = 'appointments.php';
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