<?php
// Place PHP at the very top of the file, before any HTML output
if (isset($_POST["submit1"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // Connect to database
    $con = mysqli_connect("sql205.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og") or die("Couldn't connect to server");

    // Insert data into table
    $query = mysqli_query($con, "INSERT INTO contact_as(name, email, subject, message) VALUES('$name', '$email', '$subject', '$message')");

    if ($query) {
        header('Location: thank_you.html');
        exit();
    } else {
        echo ("No record Added: " . mysqli_error($con));
    }

    mysqli_close($con);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AsanTravels</title>
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
    <style>
        :root {
            --brand-blue: #13357B;
            --brand-blue-dark: #0F2A62;
            --brand-gold: #dfc384;
            --brand-surface: #ffffff;
            --brand-muted: #6b7280;
            --brand-border: #e7ebf2;
        }

        body {
            background: var(--brand-surface);
            color: var(--brand-blue-dark);
        }

        .bg-breadcrumb {
            position: relative;
            background-image: url('img/breadcrumb-bg.jpg');
            background-size: cover;
            background-position: center;
            transform-origin: center center;
            min-height: 460px;
            display: flex;
            align-items: center;
            padding: 4rem 0;
            animation: kenBurns 18s ease-in-out infinite alternate;
        }

        .bg-breadcrumb::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(7, 10, 20, 0.75), rgba(19, 53, 123, 0.7));
        }

        .bg-breadcrumb .container {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Jost', sans-serif;
            font-size: clamp(2.6rem, 5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.02;
            color: #ffffff;
            margin-bottom: 1rem;
            letter-spacing: -0.03em;
            animation: fadeUp 0.9s 0.1s ease both;
            }

        .hero-title span {
            color: var(--brand-gold);
        }

        .hero-subtitle {
            max-width: 680px;
            margin: 0 auto 1.8rem;
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.8;
            animation: fadeUp 0.9s 0.2s ease both;
            }

        .hero-cta-group {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 1rem;
            animation: fadeUp 0.9s 0.4s ease both;
            }

        .btn-primary-alt,
        .btn-secondary-alt {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            padding: 0.95rem 1.75rem;
            border-radius: 999px;
            font-family: 'Jost', sans-serif;
            font-size: 0.92rem;
            font-weight: 700;
            text-transform: uppercase;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            text-decoration: none;
        }

        .btn-primary-alt {
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-blue-dark));
            color: #fff;
            box-shadow: 0 16px 28px rgba(19, 53, 123, 0.18);
        }

        .btn-primary-alt:hover {
            transform: translateY(-2px);
        }

        .btn-secondary-alt {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.32);
            backdrop-filter: blur(8px);
        }

        .btn-secondary-alt:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes kenBurns {
            from {
                transform: scale(1.0);
            }
            to {
                transform: scale(1.12);
            }
        }

        .section-title,
        .section-heading {
            font-family: 'Jost', sans-serif;
            font-weight: 800;
        }

        .section-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--brand-blue);
            margin-bottom: 0.8rem;
        }

        .section-heading {
            font-size: clamp(2rem, 3vw, 2.8rem);
            margin-bottom: 1rem;
            color: var(--brand-blue-dark);
        }

        .section-sub {
            font-family: 'Roboto', sans-serif;
            color: #526177;
            font-size: 1rem;
            max-width: 660px;
            margin: 0 auto 2rem;
            line-height: 1.75;
        }

        .contact-card {
            background: #ffffff;
            border: 1px solid var(--brand-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        }

        .contact-card .form-control {
            border: 1px solid var(--brand-border);
            border-radius: 16px;
            padding: 1rem 1.2rem;
            background: #f8fafc;
        }

        .contact-card .form-control:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.2rem rgba(19, 53, 123, 0.1);
        }

        .contact-card h3 {
            margin-bottom: 1rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .bg-breadcrumb {
                min-height: 420px;
                padding: 3rem 0;
            }

            .hero-title {
                font-size: clamp(2.2rem, 6vw, 3.2rem);
            }
        }
    </style>
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
        <a href="" class="navbar-brand p-0">
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
                <a href="Custom_Packages.html" class="nav-item nav-link">Custom_Packages</a>
                <a href="contact.php" class="nav-item nav-link active">Contact</a>
            </div>
        </div>
    </nav>
</div>
<!-- Navbar & Hero End -->

<!-- Hero Start -->
<section class="container-fluid bg-breadcrumb">
    <div class="container text-center text-white" style="max-width: 920px;">
        <div style="height:1.8rem;"></div>
        <h1 class="hero-title">Let’s plan your Sri Lanka journey together</h1>
        <p class="hero-subtitle">Questions, requests, or custom tour ideas? Our local travel team is ready to help you build a seamless, unforgettable experience.</p>
        <div class="hero-cta-group justify-content-center">
            <a href="#contact-form" class="btn-primary-alt">Send a Message</a>
            <a href="index1.php" class="btn-secondary-alt">Back to Home</a>
        </div>
    </div>
</section>
<!-- Hero End -->

<!-- Contact Start -->
<div class="container-fluid contact bg-light py-5">
    <div class="container py-5">
        <div class="text-center mx-auto mb-5" style="max-width: 900px;">
            <h5 class="section-title px-3 text-primary fw-bold">Contact Us</h5>
            <h1 class="mb-0">Contact For Any Query</h1>
        </div>
        <div class="row g-5 ">
            <div class="col-lg-3">
                <div class="bg-white rounded p-4 shadow-sm">
                    <div class="text-center mb-4">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h4 class="text-primary fw-bold">Address</h4>
                        <p class="mb-0">Negombo,<br> Sri Lanka</p>
                    </div>
                    <div class="text-center mb-4">
                        <i class="fas fa-phone-alt fa-3x text-primary mb-3"></i>
                        <h4 class="text-primary fw-bold">Mobile</h4>
                        <p class="mb-0">+94 76 208 7708</p>
                        <p class="mb-0">+94 77 344 5176</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-envelope-open fa-3x text-primary mb-3"></i>
                        <h4 class="text-primary fw-bold">Email</h4>
                        <p class="mb-0">asantravels@gmail.com</p>
                        <p class="mb-0">s.kavinuofficial@gmail.com</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <h3 class="mb-4">Send us a message</h3>
                <p class="mb-4 text-muted">
                    Please fill out the form below and we'll get back to you shortly.
                </p>

                <!-- FORM START -->
                <form id="contact-form" method="post" action="">
                    <div class="row g-3">
                        <div class="col-12">
                            <input type="text" class="form-control border-0" id="name" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-12">
                            <input type="email" class="form-control border-0" id="email1" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control border-0" id="subject1" name="subject" placeholder="Subject">
                        </div>
                        <div class="col-12">
                            <textarea class="form-control border-0" placeholder="Leave a message here" id="message" name="message" style="height: 160px"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" name="submit1" type="submit">Send Message</button>
                        </div>
                    </div>
                </form>
                <!-- FORM END -->
            </div>

            <div class="col-12 mt-5">
                <div class="rounded shadow-sm">
                    <iframe class="rounded w-100" style="height: 450px;"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126670.38189353577!2d79.85825320000001!3d7.18944845!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2ee9c6bb2f73b%3A0xa51626e908186f3e!2sNegombo!5e0!3m2!1sen!2slk!4v1751973013744!5m2!1sen!2slk"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

<!-- Footer Start -->
<div class="container-fluid footer py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="mb-4 text-white">Get In Touch</h4>
                    <p><i class="fas fa-home me-2"></i> Negombo, Sri lanka</p>
                    <p><i class="fas fa-envelope me-2"></i> asantravels@gmail.com</p>
                    <p><i class="fas fa-phone me-2"></i> +94 76 208 7707</p>
                    <p><i class="fab fa-whatsapp me-2"></i> +94 77 337 8244</p>
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
                    <a href="packages.html"><i class="fas fa-angle-right me-2"></i> Packages</a>
                    <a href="contact.php"><i class="fas fa-angle-right me-2"></i> Contact</a>
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
                Designed By <a class="text-white" href="https://htmlcodex.com">Kavinu Rajapakse</a>
            </div>
        </div>
    </div>
</div>
<!-- Copyright End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>

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
