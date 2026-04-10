<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
if(file_exists('../includes/dbconnection.php')) {
    include('includes/dbconnection.php');
} else {
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=jilbertsalon", "root", "");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Initialize variables for modal
$showSuccessModal = false;
$appointmentNumber = '';
$appointmentData = array(); // Store data for EmailJS
$errorMessage = '';

// Handle appointment submission
if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $services = $_POST['services'];
    $aptdate = $_POST['aptdate'];
    $apttime = $_POST['apttime'];
    
    // Validate inputs
    if(empty($name) || empty($phone) || empty($email) || empty($services) || empty($aptdate) || empty($apttime)) {
        $errorMessage = 'All fields are required!';
    } else {
        // Generate appointment number
        $aptnumber = mt_rand(100000000, 999999999);
        
        // Insert into database
        $sql = "INSERT INTO tblappointment (AptNumber, Name, Email, PhoneNumber, AptDate, AptTime, Services, Status) 
                VALUES (:aptnumber, :name, :email, :phone, :aptdate, :apttime, :services, 'Pending')";
        
        try {
            $query = $dbh->prepare($sql);
            $query->bindParam(':aptnumber', $aptnumber, PDO::PARAM_STR);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->bindParam(':aptdate', $aptdate, PDO::PARAM_STR);
            $query->bindParam(':apttime', $apttime, PDO::PARAM_STR);
            $query->bindParam(':services', $services, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            
            if($lastInsertId) {
                $showSuccessModal = true;
                $appointmentNumber = $aptnumber;
                
                // Store data for EmailJS
                $appointmentData = array(
                    'appointment_number' => $aptnumber,
                    'customer_name' => $name,
                    'customer_email' => $email,
                    'customer_phone' => $phone,
                    'service' => $services,
                    'appointment_date' => date('F d, Y', strtotime($aptdate)),
                    'appointment_time' => $apttime
                );
            } else {
                $errorMessage = 'Something went wrong. Please try again.';
            }
        } catch(PDOException $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Jilbert Salon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Jilbert-Salon/index.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    
    <style>
        .success-modal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .success-modal .modal-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .success-modal .modal-body {
            padding: 40px 30px;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            animation: scaleIn 0.5s ease-in-out;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .appointment-number {
            background: linear-gradient(135deg, #816D2D 0%, #b29f94 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            display: inline-block;
            margin: 20px 0;
        }

        .email-sending {
            font-size: 0.9rem;
            color: #0d6efd;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include 'navs/topnavbar.php'; ?>
    <div id="carousel" class="carousel slide page-banner" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="res/jilbertbg2.png" alt="First slide">
                <div class="carousel-caption ">
                    <div class="translate-middle">
                        <h1 class=" red-hat-display-head text-capitalize">BOOK APPOINTMENT</h1>
                        <div class="title-footer d-flex red-hat-display-font">
                            <p class=" text-uppercase"> HOME
                            <p class="mx-5">/</p>BOOK APPOINTMENT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid container-md align-items-center my-5">
        <div class="row text-center justify-content-center">
            <p class="display-6  red-hat-display-title">APPOINTMENT FORM</p>
            <p class="red-hat-display-dim">Book your appointment to save salon rush.</p>
        </div>
        <div class="container mb-5 ">
            <form method="POST">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input class="form-control" id="name" name="name" type="text" 
                               placeholder="Full Name" aria-label="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input class="form-control" id="phone" name="phone" type="tel" 
                               placeholder="Phone Number" aria-label="phone" 
                               pattern="[0-9]{10,11}" title="Please enter 10-11 digit phone number" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="services" class="form-label">Service</label>
                        <select class="form-select" aria-label="Select Services" id="services" name="services" required>
                            <option value="" selected>Select Service</option>
                            <?php
                            // Fetch services from database
                            try {
                                $sql = "SELECT ServiceName FROM tblservices ORDER BY ServiceName ASC";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                if($query->rowCount() > 0) {
                                    foreach($results as $row) {
                                        echo '<option value="'.htmlentities($row->ServiceName).'">'.htmlentities($row->ServiceName).'</option>';
                                    }
                                }
                            } catch(PDOException $e) {
                                echo '<option value="">Error loading services</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" id="email" name="email" type="email" 
                               placeholder="Email Address" aria-label="email" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="aptdate" class="form-label">Appointment Date</label>
                        <input class="form-control" id="aptdate" name="aptdate" type="date" 
                               aria-label="date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="apttime" class="form-label">Appointment Time</label>
                        <input class="form-control" id="apttime" name="apttime" type="time" 
                               aria-label="time" required>
                    </div>
                </div>
                <div class="d-flex justify-content-center my-5">
                    <button type="submit" name="submit" class="btn rounded-pill btn-grad">BOOK APPOINTMENT</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Appointment Booked Successfully!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <h4 class="mb-3 mt-3">Thank You!</h4>
                    <p class="text-muted mb-2">Your appointment has been successfully booked.</p>
                    <p class="text-muted mb-3">Please save your appointment number for reference:</p>
                    <div class="appointment-number">
                        <?php echo $appointmentNumber; ?>
                    </div>
                    <div class="email-sending" id="emailStatus">
                        <i class="bi bi-hourglass-split"></i> Sending confirmation email...
                    </div>
                    <p class="text-muted mt-3" id="emailConfirmation" style="display: none;">
                        <i class="bi bi-envelope-check me-2"></i>
                        Confirmation email sent successfully!
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='appointment.php'">
                        OK
                    </button>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3"><?php echo $errorMessage; ?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'navs/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        // Initialize EmailJS with your Public Key
        (function() {
            emailjs.init("ji__wW4dhZ78b6ydc"); // Replace with your EmailJS Public Key
        })();

        <?php if($showSuccessModal): ?>
        // Show success modal and send email
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Prepare email data
            const appointmentData = <?php echo json_encode($appointmentData); ?>;
            
            // Send email using EmailJS
            emailjs.send(
                'service_49ye3tt',      // Replace with your EmailJS Service ID
                'template_n85y91k',     // Replace with your EmailJS Template ID
                {
                    to_email: appointmentData.customer_email,
                    to_name: appointmentData.customer_name,
                    appointment_number: appointmentData.appointment_number,
                    customer_name: appointmentData.customer_name,
                    customer_phone: appointmentData.customer_phone,
                    service: appointmentData.service,
                    appointment_date: appointmentData.appointment_date,
                    appointment_time: appointmentData.appointment_time,
                    salon_name: 'Jilbert Salon',
                    salon_phone: '+63 991 260 9479',
                    salon_address: '911 Asingan st., Florida, United States of the Philippines'
                }
            ).then(
                function(response) {
                    console.log('Email sent successfully!', response.status, response.text);
                    document.getElementById('emailStatus').style.display = 'none';
                    document.getElementById('emailConfirmation').style.display = 'block';
                },
                function(error) {
                    console.error('Email sending failed...', error);
                    document.getElementById('emailStatus').innerHTML = 
                        '<i class="bi bi-exclamation-triangle text-warning"></i> Email could not be sent, but your appointment is confirmed!';
                }
            );
        });
        <?php endif; ?>

        <?php if(!empty($errorMessage)): ?>
        // Show error modal
        document.addEventListener('DOMContentLoaded', function() {
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
        <?php endif; ?>
    </script>
</body>

</html>