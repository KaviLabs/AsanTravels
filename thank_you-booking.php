<?php
session_start();

// Determine name from POST -> GET -> SESSION
$nameRaw = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nameRaw = $_POST['name'] ?? '';
} elseif (!empty($_GET['name'])) {
        $nameRaw = $_GET['name'];
} elseif (!empty($_SESSION['user_name'])) {
        $nameRaw = $_SESSION['user_name'];
}
$name = trim((string)$nameRaw);
$safeName = $name !== '' ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AsanTravels - Thank You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-map-marker-alt me-3"></i>AsanTravels</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.html" class="nav-item nav-link">About</a>
                    <a href="packages.html" class="nav-item nav-link">Packages</a>
                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Header Start -->
    <div class="container-fluid bg-breadcrumb">
        <div class="container text-center py-5" style="max-width: 900px;">
            <h3 class="text-white display-3 mb-4">Thank You!</h3>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active text-white">Thank You</li>
            </ol>
        </div>
    </div>
    <!-- Header End -->

    <!-- Thank You Content Start -->
    <div class="container-fluid py-5" style="background: linear-gradient(rgba(19, 53, 123, 0.3), rgba(19, 53, 153, 0.3)); object-fit: cover;">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <i class="bi bi-check-circle display-1 text-primary"></i>
                       <?php if ($safeName !== ''): ?>
                            <h1 class="display-1 mb-4 text-dark">Thank You <?php echo $safeName; ?>!</h1>
                        <?php else: ?>
                            Thank you !
                        <?php endif; ?>
                    
                    <h4 class="mb-4 text-dark">
                       
                       <p class="mb-4 text-dark"> Thank you so much for booking with us,!</p>
                        
                    </h4>
                    <p class="mb-4 text-dark">Stay tuned with AsanTravels for the hottest travel deals, trending tours, and exclusive offers just for you! 🌟✈ </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php">Back To Home</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Thank You Content End -->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>