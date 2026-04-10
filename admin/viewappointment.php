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

$showErrorModal = false;
$modalMessage = "";

if(isset($_GET['viewid'])) {
    $vid = intval($_GET['viewid']);
    
    $sql = "SELECT * FROM tblappointment WHERE ID=:vid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':vid', $vid, PDO::PARAM_INT);
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment</title>

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
                <h1 class="m3-display-medium">Appointment Details</h1>
                <a href="appointments.php" class="btn-m3 btn-m3-tonal">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            
            <div class="container my-5" style="max-width: 800px; margin: 0 auto;">
                <div class="m3-card secondary-container">
                    <div class="card-body p-4">
                        <h5 class="m3-title-large mb-4">Appointment #<?php echo htmlentities($result->AptNumber ?? ''); ?></h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Customer Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Name:</td>
                                        <td><?php echo htmlentities($result->Name ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Email:</td>
                                        <td><?php echo htmlentities($result->Email ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Phone:</td>
                                        <td><?php echo htmlentities($result->PhoneNumber ?? ''); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Appointment Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Service:</td>
                                        <td><?php echo htmlentities($result->Services ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Date:</td>
                                        <td><?php echo htmlentities(date('F d, Y', strtotime($result->AptDate ?? 'now'))); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Time:</td>
                                        <td><?php echo htmlentities($result->AptTime ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status:</td>
                                        <td>
                                            <?php
                                            $badge_class = 'bg-secondary';
                                            switch($result->Status ?? '') {
                                                case 'Pending': $badge_class = 'bg-warning text-dark'; break;
                                                case 'Confirmed': $badge_class = 'bg-info text-dark'; break;
                                                case 'Completed': $badge_class = 'bg-success'; break;
                                                case 'Cancelled': $badge_class = 'bg-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo htmlentities($result->Status ?? ''); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">Additional Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold" style="width: 200px;">Applied Date:</td>
                                        <td><?php echo htmlentities(date('F d, Y h:i A', strtotime($result->ApplyDate ?? 'now'))); ?></td>
                                    </tr>
                                    <?php if(isset($result->Remark) && $result->Remark): ?>
                                    <tr>
                                        <td class="fw-bold">Remark:</td>
                                        <td><?php echo htmlentities($result->Remark); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(isset($result->RemarkDate) && $result->RemarkDate): ?>
                                    <tr>
                                        <td class="fw-bold">Remark Date:</td>
                                        <td><?php echo htmlentities(date('F d, Y h:i A', strtotime($result->RemarkDate))); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="editappointment.php?editid=<?php echo $result->ID ?? ''; ?>" class="btn-m3 btn-m3-primary me-2">
                                <i class="bi bi-pencil-square"></i> Edit Appointment
                            </a>
                            <button type="button" class="btn-m3 btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade confirm-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="warning-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h4 class="mt-3">Are you sure you want to delete this appointment?</h4>
                    <div class="appointment-number mt-3">
                        APPOINTMENT #<?php echo htmlentities($result->AptNumber ?? ''); ?>
                    </div>
                    <p class="text-muted mt-3"><strong>Customer:</strong> <?php echo htmlentities($result->Name ?? ''); ?></p>
                    <p class="text-danger mt-3">
                        <i class="bi bi-info-circle"></i> This action cannot be undone!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <a href="appointments.php?delid=<?php echo $result->ID ?? ''; ?>" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Yes, Delete
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
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="error-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h4 class="mt-3"><?php echo $modalMessage; ?></h4>
                    <p class="text-muted mt-3">The appointment you're looking for could not be found.</p>
                    <div class="countdown mt-3">
                        <i class="bi bi-arrow-clockwise"></i> Redirecting in <span id="countdown">3</span> seconds...
                    </div>
                </div>
                <div class="modal-footer">
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
    <?php if($showErrorModal): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        
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
</script>

</html>