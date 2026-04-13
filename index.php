<?php
if (isset($_POST["submit4"])) {
    $email = $_POST["email"];
    $con = mysqli_connect("localhost", "root", "", "asantravels_og") or die("Couldn't connect to server");
    $query = mysqli_query($con, "INSERT INTO subscribe(email) VALUES('$email')");
    if ($query) {
        header('Location: thank_you-s.html');
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
        <title>Travela - AsanTravels</title>
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

    <body class="index-page">

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Header Start -->
        <!-- Topbar Start -->
        <div class="container-fluid bg-primary px-5 d-none d-lg-block">
            <div class="row gx-0">
                <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                                 <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&utm_source=qr"><i class="fab fa-instagram"></i></a>
                                <a class="btn-square btn btn-primary rounded-circle mx-1" href="http://www.tiktok.com/@asantravels"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
               
            </div>
        </div>
        <!-- Topbar End -->

        <!-- Navbar & Hero Start -->
        <div class="container-fluid position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0 d-flex align-items-center">
                    <img src="img/asntravel_logo.png" alt="AsanTravels logo" class="navbar-logo" style="max-height:50px; display:inline-block; vertical-align:middle; margin-right:10px; border-radius:5px; box-shadow:0 2px 4px rgba(0,0,0,0.1); transition:transform 0.3s;">
                    <h1 class="m-0" style="display:inline-block; vertical-align:middle; color:#13357B; font-size:1.15rem; font-weight:700;"><i class="fa fa-map-marker-alt me-2" style="color:#13357B;"></i>AsanTravels</h1>
                </a>
                <button class="navbar-toggler navbar-toggler-custom" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.html" class="nav-item nav-link active">Home</a>
                        <a href="about.html" class="nav-item nav-link">About</a>
                        <a href="packages.html" class="nav-item nav-link">Packages</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                    </div>
                    
                </div>
            </nav>
            <!-- Header End -->

            <!-- Carousel Start -->
            <div class="carousel-header">
                <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
                        <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active">
                            <img src="img/carousel-2.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Explore Sri Lanka   </h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4">Unforgettable journeys, beautiful memories.!</h1>
                                    <p class="mb-5 fs-5"></p>Explore the wonders of Sri Lanka! We create unforgettable journeys, guiding you to breathtaking destinations, hidden gems, and unique experiences. Start your adventure today and let us make your travel dreams come true with friendly service and expert local knowledge.</p>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="img/carousel-1.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Explore Sri Lanka</h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4">Find Your Perfect Tour At AsanTravels</h1>
                                    <p class="mb-5 fs-5"> Experience Sri Lanka like never before with AsanTravels. We offer personalized tours, expert local guidance, and unforgettable adventures. From stunning landscapes to cultural wonders, your perfect journey starts here. Book now and let us help you create memories that last a lifetime!</p>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="img/carousel-3.jpg" class="img-fluid" alt="Image">
                            <div class="carousel-caption">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Explore Sri Lanka</h4>
                                    <h1 class="display-2 text-capitalize text-white mb-4"> Explore, relax, and enjoy every moment.?</h1>
                                    <p class="mb-5 fs-5">Unlock the magic of Sri Lanka. From lush mountains to golden beaches, every journey is crafted for unforgettable memories. Discover hidden gems, rich culture, and breathtaking views. Start your adventure today and let your next story begin with us—Sri Lanka awaits! </p>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon btn bg-primary" aria-hidden="false"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon btn bg-primary" aria-hidden="false"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <!-- Carousel End -->


        <!-- About Start -->
        <div class="container-fluid about py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-5">
                        <div class="h-100" style="border: 50px solid; border-color: transparent #13357B transparent #13357B;">
                            <img src="img/asntravel logo.jpg" class="img-fluid w-100 h-100" alt="">
                        </div>
                    </div>
                    <div class="col-lg-7" style="background: linear-gradient(rgba(255, 255, 255, .8), rgba(255, 255, 255, .8)), url(img/asntravel logo.png);">
                        <h5 class="section-about-title pe-3">About Us</h5>
                        <h1 class="mb-4">Welcome to <span class="text-primary">AsanTravels</span></h1>
                        <p class="mb-4">At AsanTravels, you’re guided by Asanka Rajapakse, a passionate tour guide with over 10 years of experience and 700+ successful tours. Asanka’s deep knowledge and friendly approach make every journey personal and memorable. Fluent in English, he ensures you feel comfortable and informed from start to finish. Enjoy flexible tour plans, flight booking on request, handpicked hotels, customizable vehicles, and 24/7 service—all tailored to your needs. Whether you’re a first-time visitor or a seasoned explorer, AsanTravels is dedicated to making your Sri Lankan adventure truly special and hassle-free</p>
                        <p class="mb-4">Sri Lanka is a paradise island packed with breathtaking landscapes, golden beaches, and rich cultural heritage. From ancient temples and wildlife safaris to lush tea plantations and vibrant festivals, there’s something for everyone. Experience the warmth of Sri Lankan hospitality, taste delicious local cuisine, and discover hidden gems off the beaten path. Whether you crave adventure, relaxation, or cultural exploration, Sri Lanka offers unforgettable moments at every turn. With its unique blend of nature, history, and friendly locals, this tropical gem promises memories that last a lifetime. Your dream </p>
                        <div class="row gy-2 gx-4 mb-4">
                       
            </div>
        </div>
        <!-- About End -->

        <!-- Destination Start -->
        <div class="container-fluid destination py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 1100px;">
                    <h5 class="section-title px-3">Destination</h5>
                    <h1 class="mb-0">Popular Destination in Sri Lanka</h1>
                </div>
                <div class="tab-class text-center">
                    <ul class="nav nav-pills d-inline-flex justify-content-center mb-5">
                    
                       
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <div class="col-xl-8">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-1.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Southern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Southern Coast</h4>
                                                    <a href="Southern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-1.jpg" data-lightbox="destination-1"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-2.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Eastern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Eastern Coast</h4>
                                                    <a href="Eastern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-2.jpg" data-lightbox="destination-2"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-7.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Northern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Northern Coast</h4>
                                                    <a href="Northern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-7.jpg" data-lightbox="destination-7"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100 h-50" src="img/destination-8.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Western_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Western Coast</h4>
                                                    <a href="Western_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-8.jpg" data-lightbox="destination-8"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="destination-img h-100">
                                        <img class="img-fluid rounded w-100 h-100" src="img/destination-9.jpg" style="object-fit: cover; min-height: 300px;" alt="">
                                        <div class="destination-overlay p-4">
                                            <a href="Lanka's_Wild_Kingdom.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                            <h4 class="text-white mb-2 mt-3"> Lanka's Wild Kingdom</h4>
                                            <a href="Lanka's_Wild_Kingdom.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                        </div>
                                        <div class="search-icon">
                                            <a href="img/destination-9.jpg" data-lightbox="destination-4"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                        </div>
                                    </div>
                                </div>
                        
                        
                        
        <!-- Destination End -->

      
 

        <!-- review page start-->
<div class="container-fluid testimonial py-5">
    <div class="container py-5">
        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
            <h5 class="section-title px-3">Review Section</h5>
            <h1 class="mb-0">Our Clients Say!!!</h1>
        </div>

        <?php
        $conn = new mysqli("localhost", "root", "", "asantravels_og");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $result = $conn->query("SELECT * FROM reviews ORDER BY id DESC");
        ?>

        <div class="testimonial-carousel owl-carousel">
            <?php while ($row = $result->fetch_assoc()) { 
                $imagePath = "uploads/" . $row['filename'];
                if (!file_exists($imagePath) || empty($row['filename'])) {
                    $imagePath = "img/default-user.png"; // fallback image (make sure this file exists)
                }
            ?>
                <div class="testimonial-item text-center rounded shadow-lg pb-4 px-3 bg-white">
                    <div class="testimonial-comment bg-light rounded p-4 mb-4">
                        <p class="text-center mb-0 fst-italic">
                            "<?php echo htmlspecialchars($row['comments']); ?>"
                        </p>
                    </div>
                    <div class="testimonial-img p-1">
                        <img src="<?php echo $imagePath; ?>" 
                             class="img-fluid rounded-circle border border-3 border-primary shadow" 
                             style="width:100px;height:100px;object-fit:cover;" 
                             alt="Client Image">
                    </div>
                    <div style="margin-top: -30px;">
                        <h5 class="mb-1 text-dark"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="mb-2 text-muted small"><?php echo htmlspecialchars($row['email']); ?></p>
                        <div class="d-flex justify-content-center">
                            <?php for ($i = 0; $i < 5; $i++) { ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

        <!-- review page end--->


        
 <!-- Add review start--->

        <section id="review-section" style=" margin: 40px auto; font-family: Arial, sans-serif; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; color: #333; margin-bottom: 20px;">Submit Your Review</h2>
                <!-- Removed duplicate form tag and fixed action -->


<!--  Handle file upload--->
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";       // default XAMPP username
$password = "";           // default XAMPP password is blank
$dbname = "asantravels_og";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['submit'])) {
    // Collect and sanitize input values
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $comments = $conn->real_escape_string($_POST['comments'] ?? '');
    $filename = NULL;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);

        // Validate file type (allow images and pdfs)
        $fileType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            $message = "Error: Only JPG, PNG, GIF images and PDF files are allowed.";
        } else {
            // Prevent overwriting existing files
            $newFilename = time() . '_' . $fileName;
            $destPath = $uploadDir . $newFilename;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $filename = $newFilename; // Store the filename without escaping here; will escape in prepared statement
            } else {
                $message = "Error uploading the file.";
            }
        }
    }

    // If no upload errors, insert into database
    if (empty($message)) {
        // Prevent duplicate review spam
        $dupStmt = $conn->prepare("SELECT id FROM reviews WHERE name=? AND email=? AND comments=?");
        $dupStmt->bind_param("sss", $name, $email, $comments);
        $dupStmt->execute();
        $dupStmt->store_result();
        if ($dupStmt->num_rows > 0) {
            $message = "Error: Duplicate review detected. Please do not submit the same review multiple times.";
        } else {
            $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, comments, filename) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssiss", $name, $email, $rating, $comments, $filename);
            if ($stmt->execute()) {
                $message = "Review submitted successfully!";
            } else {
                $message = "Execute failed: " . $stmt->error;
            }
            $stmt->close();
        }
        $dupStmt->close();
    }

    
}

$conn->close();
?>



    <form method="POST" action="index1.php" enctype="multipart/form-data" style="max-width: 450px; margin: auto; padding: 30px; background: #f8f9fa; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); font-family: 'Roboto', sans-serif;">
    <h2 style="text-align: center; margin-bottom: 25px; color: #333;"></h2>

    <label for="file" style="font-weight: 600; color: #555; ">Upload File </label>
    <input type="file" id="file" name="file" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem;">

    <label for="name" style="font-weight: 600; color: #555;">Name</label>
    <input type="text" id="name" name="name" placeholder="Your name" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem;">

    <label for="email" style="font-weight: 600; color: #555;">Email</label>
    <input type="email" id="email" name="email" placeholder="Your email" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem;">

    <label for="rating" style="font-weight: 600; color: #555;">Rating</label>
    <select id="rating" name="rating" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem;">
        <option value="" disabled selected>Select rating</option>
        <option value="5">5 - Excellent</option>
        <option value="4">4 - Good</option>
        <option value="3">3 - Average</option>
        <option value="2">2 - Poor</option>
        <option value="1">1 - Terrible</option>
    </select>

    <label for="comments" style="font-weight: 600; color: #555;">Comments</label>
    <textarea id="comments" name="comments" placeholder="Your comments" required
              style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; resize: vertical; min-height: 120px; margin-bottom: 20px;"></textarea>

    <button type="submit" name="submit" style="width: 100%; background-color: #007BFF; color: #fff; padding: 14px; font-size: 1.1rem; border: none; border-radius: 6px; cursor: pointer; transition: 0.3s;">
        Submit Review       
    </button>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold; text-align: center; margin-top: 15px;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</form>

</section>

<style>
    form button:hover {
        background-color: #0056b3;
    }
    form input:focus, form select:focus, form textarea:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0,123,255,0.5);
    }
</style>

<!-- Add review end--->

        <!-- Subscribe Start -->
        <div class="container-fluid subscribe py-5">
            <div class="container text-center py-5">
                <div class="mx-auto text-center" style="max-width: 900px;">
                    <h5 class="subscribe-title px-3">Subscribe</h5>
                    <h1 class="text-white mb-4">Our Newsletter</h1>
                    <p class="text-white mb-5">Subscribe to stay updated with our latest travel deals, trending tour packages, and exclusive offers. Be the first to explore new destinations with AsanTravels!" </p>
                    <form method="POST" class="position-relative mx-auto" style="max-width: 500px;">
                        <input class="form-control border-primary rounded-pill w-100 py-3 ps-4 pe-5" name="email" type="email" placeholder="Your email" required>
                        <button type="submit" name="submit4" class="btn btn-primary rounded-pill position-absolute top-0 end-0 py-2 px-4 mt-2 me-2">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>


        <!-- Subscribe End -->



<!-- Footer Start -->
<div class="container-fluid footer py-5 bg-dark text-white">
    <div class="container py-5">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="mb-4 text-white">Get In Touch</h4>
                    <div>
                        <p class="mb-2"><i class="fas fa-home me-2"></i>Negombo, Sri Lanka</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i>asantravels@gmail.com</p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i>+94 76 208 7707</p>
                        <p class="mb-3"><i class="fab fa-whatsapp me-2"></i>+94 77 337 8244</p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <i class="fas fa-share fa-2x text-white me-2"></i>
                        <a class="btn btn-primary btn-sm rounded-circle mx-1" href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-primary btn-sm rounded-circle mx-1" href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&utm_source=qr"><i class="fab fa-instagram"></i></a>
                        <a class="btn btn-primary btn-sm rounded-circle mx-1" href="http://www.tiktok.com/@asantravels"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <!-- Company Links -->
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="mb-4 text-white">Company</h4>
                    <a class="text-white-50 mb-2 text-decoration-none" href="about.html"><i class="fas fa-angle-right me-2"></i>About</a>
                    <a class="text-white-50 mb-2 text-decoration-none" href="packages.html"><i class="fas fa-angle-right me-2"></i>Packages</a>
                    <a class="text-white-50 mb-2 text-decoration-none" href="contact.html"><i class="fas fa-angle-right me-2"></i>Contact</a>
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
                        <i class="fas fa-copyright me-2"></i><a class="text-white" href="">Site Name</a> AsanTravels
                    </div>
                    <div class="col-md-6 text-center text-md-start">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="text-white" Distributed By href="">Kavinu Rajapakse</a>
                    </div>
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