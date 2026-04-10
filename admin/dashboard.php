<?php
session_start();
error_reporting(0);
include('../includes/dbconnection.php');

// Query to get completed appointments
$query_completed = mysqli_query($con, "SELECT COUNT(*) as completed FROM tblappointment WHERE Status='Completed'");
$result_completed = mysqli_fetch_array($query_completed);
$completed_appointments = $result_completed['completed'];

// Query to get ongoing appointments (Confirmed and Pending)
$query_ongoing = mysqli_query($con, "SELECT COUNT(*) as ongoing FROM tblappointment WHERE Status='Confirmed' OR Status='Pending'");
$result_ongoing = mysqli_fetch_array($query_ongoing);
$ongoing_appointments = $result_ongoing['ongoing'];

// Query to get cancelled appointments
$query_cancelled = mysqli_query($con, "SELECT COUNT(*) as cancelled FROM tblappointment WHERE Status='Cancelled'");
$result_cancelled = mysqli_fetch_array($query_cancelled);
$cancelled_appointments = $result_cancelled['cancelled'];

// Query to get today's appointment list
$today = date('Y-m-d');
$query_today_list = mysqli_query($con, "SELECT Name, AptTime, Services, Status FROM tblappointment WHERE AptDate='$today' ORDER BY AptTime LIMIT 5");

// Query to get total appointments
$query_total = mysqli_query($con, "SELECT COUNT(*) as total FROM tblappointment");
$result_total = mysqli_fetch_array($query_total);
$total_appointments = $result_total['total'];

// Query for monthly trend (last 6 months)
$monthly_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $query_month = mysqli_query($con, "SELECT COUNT(*) as count FROM tblappointment WHERE DATE_FORMAT(AptDate, '%Y-%m') = '$month'");
    $result_month = mysqli_fetch_array($query_month);
    $monthly_data[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'count' => $result_month['count']
    ];
}

// Query for service popularity
$query_services = mysqli_query($con, "SELECT Services, COUNT(*) as count FROM tblappointment WHERE Status='Completed' GROUP BY Services ORDER BY count DESC LIMIT 5");
$service_data = [];
while ($row = mysqli_fetch_array($query_services)) {
    $service_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        .summary-card {
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .small-chart-container {
            position: relative;
            height: 250px;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include '../navs/sidenavbar.php'; ?>

    <div class="m3-main-content">
        <div class="container-fluid mb-5">
            <h1 class="m3-display-medium mb-4">Dashboard</h1>

            <!-- Summary Cards -->
            <div class="row mt-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card success-container h-100 summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Completed Appointments</h6>
                                    <h2 class="mb-0"><?php echo $completed_appointments; ?></h2>
                                    <small><i class="bi bi-check-circle"></i> All Time</small>
                                </div>
                                <div>
                                    <i class="bi bi-check-circle-fill fs-1"></i>
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
                                    <h6 class="card-title">Ongoing Appointments</h6>
                                    <h2 class="mb-0"><?php echo $ongoing_appointments; ?></h2>
                                    <small><i class="bi bi-clock-history"></i> Pending & Confirmed</small>
                                </div>
                                <div>
                                    <i class="bi bi-hourglass-split fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card error-container h-100 summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Cancelled Appointments</h6>
                                    <h2 class="mb-0"><?php echo $cancelled_appointments; ?></h2>
                                    <small><i class="bi bi-x-circle"></i> All Time</small>
                                </div>
                                <div>
                                    <i class="bi bi-x-circle-fill fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row mt-4">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="m3-card surface-container-high h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-graph-up"></i> Appointment Trends (Last 6 Months)
                            </h5>
                            <div class="chart-container">
                                <canvas id="appointmentTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card surface-container-high h-100 p-2">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-calendar-day"></i> Appointments Today
                                <span class="badge bg-dark ms-2"><?php echo date('d M Y'); ?></span>
                            </h5>
                            <div class="appointments-list" style="max-height: 300px; overflow-y: auto;">
                                <?php
                                if (mysqli_num_rows($query_today_list) > 0) {
                                    mysqli_data_seek($query_today_list, 0);
                                    while ($row = mysqli_fetch_array($query_today_list)) {
                                        $statusClass = '';
                                        switch ($row['Status']) {
                                            case 'Completed':
                                                $statusClass = 'status-completed';
                                                break;
                                            case 'Confirmed':
                                                $statusClass = 'status-confirmed';
                                                break;
                                            case 'Pending':
                                                $statusClass = 'status-pending';
                                                break;
                                            case 'Cancelled':
                                                $statusClass = 'status-cancelled';
                                                break;
                                        }

                                        echo '<div class="d-flex justify-content-between mb-2 align-items-start">';
                                        echo '<div class="flex-grow-1">';
                                        echo '<p class="mb-0"><strong>' . htmlentities($row['Name']) . '</strong> ';
                                        echo '<span class="status-badge ' . $statusClass . '">' . htmlentities($row['Status']) . '</span></p>';
                                        echo '<small class="text-muted">' . htmlentities($row['Services']) . '</small>';
                                        echo '</div>';
                                        echo '<div><small class="text-dark fw-bold">' . htmlentities($row['AptTime']) . '</small></div>';
                                        echo '</div>';
                                        echo '<hr class="my-2">';
                                    }
                                } else {
                                    echo '<div class="text-center py-4">';
                                    echo '<i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>';
                                    echo '<p class="text-muted mt-2">No appointments today</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="container d-flex justify-content-end">
                            <p class="card-text mt-3">
                                <i class="bi bi-list-ul"></i>
                                <a href="appointments.php" class="text-decoration-none">View all appointments...</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mt-4">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="m3-card surface-container-high h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-star-fill"></i> Top 5 Popular Services
                            </h5>
                            <div class="chart-container">
                                <canvas id="servicesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="m3-card surface-container-high h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-pie-chart-fill"></i> Appointment Status
                            </h5>
                            <div class="small-chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <strong>Total:</strong> <?php echo $total_appointments; ?> appointments
                                </small>
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

    <script>
        // Appointment Trend Chart (Line Chart)
        const trendCtx = document.getElementById('appointmentTrendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_data, 'month')); ?>,
                datasets: [{
                    label: 'Appointments',
                    data: <?php echo json_encode(array_column($monthly_data, 'count')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Services Chart (Bar Chart)
        const servicesCtx = document.getElementById('servicesChart').getContext('2d');
        const servicesChart = new Chart(servicesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($service_data, 'Services')); ?>,
                datasets: [{
                    label: 'Number of Bookings',
                    data: <?php echo json_encode(array_column($service_data, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Status Chart (Doughnut Chart)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Cancelled', 'Ongoing/Pending'],
                datasets: [{
                    data: [
                        <?php echo $completed_appointments; ?>,
                        <?php echo $cancelled_appointments; ?>,
                        <?php echo $ongoing_appointments; ?>
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(13, 110, 253, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(13, 110, 253, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>