<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Travela - Tourism Website Template</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
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

        <!-- Topbar Start -->


       
       
        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4">Lanka's Wild Kingdom</h1>
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <!-- Gallery Start -->
        <div class="container-fluid gallery py-5 my-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Gallery</h5>
                <h1 class="display-5">Explore Lanka's Wild Kingdom</h1>
            </div>
            

        <?php
        // Connect to DB
        $conn = new mysqli("localhost", "root", "", "asantravels_og");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $result = $conn->query("SELECT * FROM gallery WHERE title='Lankas_Wild_Kingdom'");
        ?>

        <div class="container py-4">
            <div class="row g-3 justify-content-center">
                <?php while($row = $result->fetch_assoc()): ?>
                    <?php
                        $imagePath = "as_gallery/" . $row['image'];
                        if (!file_exists($imagePath)) {
                            $imagePath = "as_gallery/placeholder.jpg"; // fallback image
                        }
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">
                        <div class="gallery-item position-relative overflow-hidden rounded shadow-sm" style="max-width: 300px;">
                            <img src="<?= htmlspecialchars($imagePath) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($row['title'] ?: 'Gallery Image') ?>">
                            <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-center" style="background: rgba(0,0,0,0.6); opacity: 0; transition: 0.3s;">
                                <h5 class="text-white mb-2"><?= htmlspecialchars($row['title'] ?: "Gallery Image") ?></h5>
                                <a href="<?= htmlspecialchars($imagePath) ?>" data-lightbox="gallery" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-plus"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <!-- Gallery End -->

        <!-- Gallery End -->

       
        <!-- Footer Start -->
        <br><div class="container-fluid footer py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="mb-4 text-white">Get In Touch</h4>
                            <text=""><i class="fas fa-home me-2"></i> Negombo, Sri lanka <br>
                            <text=""><i class="fas fa-envelope me-2"></i> asantravels@gmail.com<br>
                            <text=""><i class="fas fa-phone me-2"></i> +94 76 208 7707 <br>
                            <text="" class="mb-3"><i class="fab fa-whatsapp me-2"></i> +94 77 337 8244 <br>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-share fa-2x text-white me-2"></i>
                                <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&utm_source=qr"><i class="fab fa-instagram"></i></a>
                                <a class="btn-square btn btn-primary rounded-circle mx-1" href="http://www.tiktok.com/@asantravels"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="mb-4 text-white">Company</h4>
                            <a href="about.html"><i class="fas fa-angle-right me-2"></i> About</a>
                            <a href="packages.html"><i class="fas fa-angle-right me-2"></i>Packages</a>
                            <a href="contact.html"><i class="fas fa-angle-right me-2"></i> Contact</a>

                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        
        <!-- Copyright Start -->
        <div class="container-fluid copyright text-body py-4">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 text-center text-md-end mb-md-0">
                        <i class="fas fa-copyright me-2"></i><a class="text-white" href="#">Site Name</a>, AsanTravels.
                    </div>
                    <div class="col-md-6 text-center text-md-start">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="text-white" >Kavinu Rajapakse</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
        <!-- JavaScript Libraries -->
         <script>
                    let slideIndex = 1;
                    showSlides(slideIndex);

                    function currentSlide(n) {
                    showSlides(slideIndex = n);
                    }

                    function showSlides(n) {
                    let slides = document.getElementsByClassName("slide");
                    let dots = document.getElementsByClassName("dot");
                    if (n > slides.length) {slideIndex = 1}
                    if (n < 1) {slideIndex = slides.length}
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].style.display = "none";
                    }
                    for (let i = 0; i < dots.length; i++) {
                        dots[i].className = dots[i].className.replace(" active", "");
                    }
                    slides[slideIndex-1].style.display = "block";
                    dots[slideIndex-1].className += " active";
                    }

         </script>




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