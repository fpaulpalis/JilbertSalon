<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if(!isset($dbh)) {
    die("Database connection not established");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Jilbert Salon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Jilbert-Salon/index.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include 'navs/topnavbar.php'; ?>
    <div id="carousel" class="carousel slide page-banner" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="res/jilbertbg2.png" alt="First slide">
                <div class="carousel-caption ">

                    <div class="translate-middle">
                        <h1 class=" red-hat-display-head text-capitalize">SALON SERVICE</h1>
                        <div class="title-footer d-flex red-hat-display-font">
                            <p class="md text-uppercase"> HOME
                            <p class="mx-5">/</p>SERVICE LIST</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid container-md align-items-center my-5">
        <div class="row mb-5 text-center">
            <p class="display-6  red-hat-display-title">OUR SERVICE PRICES</p>
            <p class="red-hat-display-dim">Far far away, behind the word of mountains, far from the lands of Pozzorrubio
                and San Manuel </p>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Service Name</th>
                            <th scope="col">Service Price</th>
                            <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch services from database
                        try {
                            $sql = "SELECT * FROM tblservices ORDER BY ID ASC";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            $cnt = 1;
                            
                            if($query->rowCount() > 0) {
                                foreach($results as $row) {
                            ?>
                            <tr>
                                <th scope="row"><?php echo htmlentities($cnt); ?></th>
                                <td><?php echo htmlentities($row->ServiceName); ?></td>
                                <td>₱<?php echo htmlentities(number_format($row->Cost, 2)); ?></td>
                                <td><?php echo htmlentities($row->Description); ?></td>
                            </tr>
                            <?php 
                                $cnt++;
                                }
                            } else { 
                            ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <em>No services available at the moment.</em>
                                </td>
                            </tr>
                            <?php 
                            }
                        } catch(PDOException $e) {
                            ?>
                            <tr>
                                <td colspan="4" class="text-center text-danger">
                                    <em>Error fetching services: <?php echo $e->getMessage(); ?></em>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'navs/footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>