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
        <link href="css/style.css?v=12" rel="stylesheet">
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
                <h3 class="text-white display-3 mb-4">Eastern Coast</h1>
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <!-- Gallery Start -->

        <!-- Gallery Start -->
        <div class="container-fluid gallery py-5 my-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h5 class="section-title px-3">Gallery</h5>
                <h1 class="display-5">Explore Eastern Coast</h1>
            </div>
            

        <?php
        // Connect to DB
        $conn = new mysqli("sql206.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $result = $conn->query("SELECT * FROM gallery WHERE title='Eastern_Coast'");
        ?>

        <div class="gallery-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <?php
                        $imagePath = "as_gallery/" . $row['image'];
                        if (!file_exists($imagePath)) {
                            $imagePath = "as_gallery/placeholder.jpg"; // fallback image
                        }
                    ?>
                    <div class="gallery-cell">
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($row['title'] ?: 'Gallery Image') ?>">
                        <div class="gallery-overlay">
                            <h5><?= htmlspecialchars($row['title'] ?: "Gallery Image") ?></h5>
                            <a href="<?= htmlspecialchars($imagePath) ?>" data-lightbox="gallery" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-plus"></i> View
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
        </div>
    </div>


        <!-- Gallery End -->

       
            <!-- Premium Footer Start -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="row g-5 pb-5">
                <div class="col-md-6 col-lg-4">
                    <span class="site-footer-brand">Asan<span>Travels</span></span>
                    <span class="site-footer-brand-sub">Your trusted partner for authentic, personalised Sri Lanka experiences. Expert local guide with 10+ years of unforgettable tours.</span>
                    <div class="site-footer-social">
                        <a href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&amp;utm_source=qr" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="http://www.tiktok.com/@asantravels" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h4>Get In Touch</h4>
                    <p><i class="fas fa-map-marker-alt me-2" style="color:#C9A84C;"></i>Negombo, Sri Lanka</p>
                    <p><i class="fas fa-envelope me-2" style="color:#C9A84C;"></i>asantravels@gmail.com</p>
                    <p><i class="fas fa-phone me-2" style="color:#C9A84C;"></i>+94 76 208 7707</p>
                    <p><i class="fab fa-whatsapp me-2" style="color:#C9A84C;"></i>+94 77 337 8244</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h4>Quick Links</h4>
                    <a href="about.html"><i class="fas fa-angle-right me-2"></i>About Us</a>
                    <a href="packages.html"><i class="fas fa-angle-right me-2"></i>Tour Packages</a>
                    <a href="Custom_Packages.html"><i class="fas fa-angle-right me-2"></i>Custom Tours</a>
                    <a href="contact.php"><i class="fas fa-angle-right me-2"></i>Contact</a>
                </div>
            </div>
            <div class="site-footer-divider">
                <p class="site-footer-copy">&copy; 2026 AsanTravels. All Rights Reserved. &nbsp;|&nbsp; Designed by <a href="#" style="color:rgba(201,168,76,0.6);text-decoration:none;">Kavinu Rajapakse</a></p>
            </div>
        </div>
    </footer>
    <!-- Premium Footer End -->

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
