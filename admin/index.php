<?php
session_start();
error_reporting(0);

// Check if already logged in
if(isset($_SESSION['bpmsaid'])) {
    header('location:dashboard.php');
    exit();
}

// Initialize variable for modal
$showSuccessModal = false;
$adminUsername = '';

// Handle login submission
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hardcoded credentials
    $admin_username = 'admin';
    $admin_password = 'admin';
    
    // Validate credentials
    if($username === $admin_username && $password === $admin_password) {
        // Set session
        $_SESSION['bpmsaid'] = 1;
        $_SESSION['admin_username'] = $username;
        
        // Show success modal
        $showSuccessModal = true;
        $adminUsername = $username;
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Jilbert Salon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    

</head>

<body>
    <div class="parent d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="m3-card px-5 py-4 text-center mt-5 mb-5" style="width: 100%; max-width: 400px;">
            <p class="m3-display-small mb-2">Welcome</p>
            <p class="m3-body-large m3-text-dim mb-4">Please login to access the admin dashboard</p>
            <div class="text-start">
                <form method="POST">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" id="username" name="username" type="text" 
                                   aria-label="username" required autocomplete="username">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" id="password" name="password" type="password" 
                                   aria-label="password" required autocomplete="current-password">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5 mb-2">
                        <button type="submit" name="login" class="btn-m3 btn-m3-primary w-100 py-3">LOGIN</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Login Successful!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <h4 class="mb-3 mt-3">Welcome Back!</h4>
                    <p class="text-muted mb-3">You have successfully logged in as:</p>
                    <div class="welcome-message">
                        <i class="bi bi-person-circle me-2"></i><?php echo strtoupper($adminUsername); ?>
                    </div>
                    <p class="text-muted mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Redirecting to dashboard...
                    </p>
                    <div class="countdown">
                        <i class="bi bi-clock me-1"></i>
                        This window will close in <span id="countdown">3</span> seconds...
                    </div>
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
                    successModal.hide();
                    window.location.href = 'dashboard.php';
                }
            }, 1000);
        });
        <?php endif; ?>
    </script>
</body>

</html>