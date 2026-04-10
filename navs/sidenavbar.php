<?php
// We no longer output head/meta tags in sidenav, this is usually a poor practice anyway. 
// Assuming the parent file includes styling. We just inject the nav structures.
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<!-- Navigation Rail (Desktop) -->
<nav class="nav-rail">
    <a href="#" class="mb-4">
        <img src="../res/jilbertlogo.png" alt="Logo" class="brand-logo">
    </a>

    <?php 
    $current = basename($_SERVER['PHP_SELF']); 
    ?>

    <a href="dashboard.php" class="nav-rail-item <?php echo ($current == 'dashboard.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">dashboard</span>
        </div>
        <span class="label">Home</span>
    </a>

    <a href="manageservice.php" class="nav-rail-item <?php echo ($current == 'manageservice.php' || $current == 'addservice.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">manufacturing</span>
        </div>
        <span class="label">Services</span>
    </a>

    <a href="appointments.php" class="nav-rail-item <?php echo ($current == 'appointments.php' || strpos($current, 'appointment') !== false) ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">event_list</span>
        </div>
        <span class="label">Bookings</span>
    </a>

    <a href="salesreport.php" class="nav-rail-item <?php echo ($current == 'salesreport.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">analytics</span>
        </div>
        <span class="label">Sales</span>
    </a>

    <!-- Spacer to push logout to bottom if we want, or just list it -->
    <div style="flex-grow: 1;"></div>

    <a href="logout.php" class="nav-rail-item">
        <div class="icon-container">
            <span class="material-symbols-outlined">logout</span>
        </div>
        <span class="label">Logout</span>
    </a>
</nav>

<!-- Bottom Navigation Bar (Mobile) -->
<nav class="bottom-nav-bar">
    <a href="dashboard.php" class="bottom-nav-item <?php echo ($current == 'dashboard.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">dashboard</span>
        </div>
        <span class="label">Home</span>
    </a>

    <a href="manageservice.php" class="bottom-nav-item <?php echo ($current == 'manageservice.php' || $current == 'addservice.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">manufacturing</span>
        </div>
        <span class="label">Services</span>
    </a>

    <a href="appointments.php" class="bottom-nav-item <?php echo ($current == 'appointments.php' || strpos($current, 'appointment') !== false) ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">event_list</span>
        </div>
        <span class="label">Bookings</span>
    </a>

    <a href="salesreport.php" class="bottom-nav-item <?php echo ($current == 'salesreport.php') ? 'active' : ''; ?>">
        <div class="icon-container">
            <span class="material-symbols-outlined">analytics</span>
        </div>
        <span class="label">Sales</span>
    </a>

    <a href="logout.php" class="bottom-nav-item">
        <div class="icon-container">
            <span class="material-symbols-outlined">logout</span>
        </div>
        <span class="label">Logout</span>
    </a>
</nav>