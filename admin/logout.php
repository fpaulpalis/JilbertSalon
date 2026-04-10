<?php
session_start();

// Handle logout action
if(isset($_POST['confirm_logout'])) {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    // Redirect to login page
    header('location: /Jilbert-Salon/admin/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    
    
</head>
<body>
    <!-- Logout Confirmation Modal -->
    <div class="modal fade show" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" style="display: block;" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="bi bi-box-arrow-right me-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="window.location.href='/Jilbert-Salon/admin/dashboard.php'"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle logout-icon"></i>
                    <h4 class="mb-3">Are you sure you want to logout?</h4>
                    <p class="text-muted">You will need to login again to access your account.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <form method="POST" class="d-inline">
                        <button type="submit" name="confirm_logout" class="btn btn-danger btn-logout me-2">
                            <i class="bi bi-box-arrow-right me-2"></i>Yes, Logout
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary btn-cancel" onclick="window.location.href='/Jilbert-Salon/admin/dashboard.php'">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>