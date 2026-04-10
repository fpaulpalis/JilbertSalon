<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="index.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

</head>

<body>
    <?php include 'navs/topnavbar.php'; ?>

    <div id="carouselExampleRide" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="2000">
                <img src="res/jilbertbg.png" class="d-block w-100" alt="...">
                <div class="carousel-caption ">

                    <div class="translate-middle">
                        <h1 class=" red-hat-display-head text-capitalize">JILBERT SALON
                            APPOINTMENT SCHEDULER </h1>
                        <p class="md text-uppercase red-hat-display-font">Your Types. Your Style. Your Color.</p>


                        <button type="button" class="btn rounded-pill btn-grad" onclick="window.location.href='appointment.php'">MAKE AN APPOINTMENT</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="2000">
                <img src="res/jilbertbg3 1.png" class="d-block w-100" alt="...">
                <div class="carousel-caption ">

                    <div class="translate-middle">
                        <h1 class=" red-hat-display-head text-capitalize">JILBERT SALON
                            APPOINTMENT SCHEDULER </h1>
                        <p class="md text-uppercase red-hat-display-font">Your Types. Your Style. Your Color.</p>


                        <button type="button" class="btn rounded-pill btn-grad" onclick="window.location.href='appointment.php'">MAKE AN APPOINTMENT</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="2000">
                <img src="res/jilbertbg4 1.png" class="d-block w-100" alt="...">
                <div class="carousel-caption ">

                    <div class="translate-middle">
                        <h1 class=" red-hat-display-head text-capitalize">JILBERT SALON
                            APPOINTMENT SCHEDULER </h1>
                        <p class="md text-uppercase red-hat-display-font">Your Types. Your Style. Your Color.</p>


                        <button type="button" class="btn rounded-pill btn-grad" onclick="window.location.href='appointment.php'">MAKE AN APPOINTMENT</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleRide" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleRide" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container-fluid container-md align-items-center">
        <div class="row mb-5">
            <div class="col-12 col-lg-4">
                <img class="d-block w-100" src="res/jilbert2.png" alt="First slide">
            </div>
            <div class="col-12 col-lg-8 align-self-center">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title red-hat-display-title">ABOUT US</h3>
                        <p class="card-text red-hat-display-dim">BEST EXPERIENCE EVER</p>
                        <p class="card-text red-hat-display-default">Our main focus is on quality and hygiene. Our
                            Parlour is well equipped
                            with advanced technology equipments and provides best quality services. Our staff is
                            well trained and experienced, offering advanced services in Skin, Hair and Body Shaping
                            that will provide you with a luxurious experience that leave you feeling relaxed and
                            stress free. The specialities in the parlour are, apart from regular bleachings and
                            Facials, many types of hairstyles, Bridal and cine make-up and different types of
                            Facials & fashion hair colourings.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php include 'navs/footer.php'; ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

</html>