<?php
if (isset($_POST["submit2"])) {
    $con = mysqli_connect("localhost", "root", "", "asantravels_og");
    if (!$con) {
        die("Couldn't connect to server: " . mysqli_connect_error());
    }

    $Package_type = "Cultural Gems & Wildlife Wonders";

    // Get inputs (trimmed)
    $tripDays = (int)($_POST['tripDays'] ?? 0);
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $numAdults = (int)($_POST['numAdults'] ?? 1);
    $numChildren = (int)($_POST['numChildren'] ?? 0);
    $passengerInput = $numAdults + $numChildren;
    $roomOptions = trim($_POST['roomOptions'] ?? '');
    $optionalToursArr = $_POST['optionalTours'] ?? [];
    $optionalTours = is_array($optionalToursArr) ? implode(", ", $optionalToursArr) : (string)$optionalToursArr;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Calculation
    $base_price_per_person = 750.0;
    $base_price = $base_price_per_person * max(0, $passengerInput);

    $extras = 0.0;
    if (!empty($optionalToursArr) && is_array($optionalToursArr)) {
        foreach ($optionalToursArr as $tour) {
            if ($tour === "Colombo Street Food Tour") $extras += 95 * $passengerInput;
            if ($tour === "Safari Yala National Park") $extras += 135 * $passengerInput;
            if ($tour === "Colombo City by Tuk Tuk") $extras += 96 * $passengerInput;
        }
    }

    $total = $base_price + $extras;
    $arrival_payment = $total / 2.0;

    // Prepared statement insert
    $sql = "INSERT INTO booking (`Package`,`start_date`,`end_date`,`passengers`,`room_option`,`optional_tours`,`base_price`,`extras`,`total`,`pay_on_arrival`,`name`,`email`,`special_request`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        $err = mysqli_error($con);
        mysqli_close($con);
        die('Prepare failed: ' . $err);
    }

    // bind parameters
    mysqli_stmt_bind_param($stmt, 'sssissdddssss', $Package_type, $start_date, $end_date, $passengerInput, $roomOptions, $optionalTours, $base_price, $extras, $total, $arrival_payment, $name, $email, $message);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        // Save name to session and redirect
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['user_name'] = $name;
        header('Location: thank_you-booking.php?name=' . urlencode($name));
        exit();
    } else {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        die('Insert failed: ' . $err);
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Travela - Tourism Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">

     <style>
        body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #f8f8f8 0%, #e0ecff 100%);
    color: #222;
    min-height: 100vh;
    overflow-x: hidden;
}

h1, h2 {
    color: #0b3c5d;
    letter-spacing: 1px;
    font-weight: 700;
    text-shadow: 0 2px 12px rgba(11,60,93,0.08);
}

.section {
    margin-bottom: 2.5rem;
    background: rgba(255,255,255,0.85);
    box-shadow: 0 4px 32px rgba(11,60,93,0.08);
    border-radius: 16px;
    padding: 2rem 2.5rem 1.5rem 2.5rem;
    backdrop-filter: blur(5px);
    transition: box-shadow 0.3s;
}
.section:hover {
    box-shadow: 0 8px 40px rgba(11,60,93,0.18);
}

label {
    display: block;
    margin: 1.2rem 0 0.5rem;
    font-weight: 500;
    color: #0b3c5d;
    letter-spacing: 0.5px;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    width: 100%;
    padding: 0.7rem 1rem;
    border: none;
    border-radius: 8px;
    background: rgba(240,245,255,0.7);
    box-shadow: 0 2px 8px rgba(11,60,93,0.05);
    font-size: 1rem;
    margin-bottom: 0.5rem;
    transition: box-shadow 0.2s, background 0.2s;
}
input:focus, select:focus {
    outline: none;
    background: #fff;
    box-shadow: 0 0 0 2px #0b3c5d33;
}

input[type="checkbox"] {
    accent-color:rgba(19, 53, 123, .8);
    transform: scale(1.2);
    margin-right: 0.5rem;
}

.totals {
    background: rgba(11,60,93,0.08);
    padding: 2rem 2rem 1.5rem 2rem;
    border-radius: 18px;
    margin-top: 2rem;
    box-shadow: 0 6px 36px rgba(11,60,93,0.10);
    font-size: 1.1rem;
}
.totals h2 {
    color: rgba(19, 53, 123, .8);
    margin-bottom: 1rem;
}

button {
    background: linear-gradient(90deg, rgba(19, 53, 123, .8), #3282b8 100%);
    color: #fff;
    padding: 0.85rem 2.2rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    box-shadow: 0 2px 12px rgba(50,130,184,0.15);
    transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
}
button:hover {
    background: linear-gradient(90deg, rgba(19, 53, 123, .8), rgba(19, 53, 123, .8));
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 6px 24px rgba(50,130,184,0.22);
}



.breadcrumb {
    background: transparent;
    font-size: 1.1rem;
}
.breadcrumb-item a {
    color: #fff;
    text-decoration: underline dotted;
    transition: color 0.2s;
}
.breadcrumb-item.active {
    color: #ffe082;
    font-weight: 600;
}

.footer {
    background: linear-gradient(90deg, rgba(19, 53, 123, .8) 0%, black);
    color: #fff;
    border-top-left-radius: 32px;
    border-top-right-radius: 32px;
    box-shadow: 0 -4px 24px rgba(11,60,93,0.12);
}
.footer a {
    color: #ffe082;
    text-decoration: none;
    transition: color 0.2s;
}
.footer a:hover {
    color: #fff;
    text-shadow: 0 2px 8px #ffe08266;
}
.footer-item {
    margin-bottom: 2rem;
}
.footer-bank-card a {
    margin-right: 0.5rem;
    transition: transform 0.2s;
}
.footer-bank-card a:hover {
    transform: scale(1.15) rotate(-5deg);
}

::-webkit-scrollbar {
    width: 10px;
    background: #e0ecff;
}
::-webkit-scrollbar-thumb {
    background: linear-gradient(120deg, #3282b8 0%,rgba(19, 53, 123, .8));
    border-radius: 8px;
}

#spinner {
    z-index: 9999;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(6px);
}

.back-to-top {
    position: fixed;
    bottom: 32px;
    right: 32px;
    background: linear-gradient(135deg, #3282b8 0%, rgba(19, 53, 123, .8));
    color: #fff;
    border-radius: 50%;
    box-shadow: 0 4px 24px rgba(50,130,184,0.22);
    transition: background 0.3s, transform 0.2s;
}
.back-to-top:hover {
    background: #ffe082;
    color: rgba(19, 53, 123, .8);
    transform: scale(1.1) rotate(-10deg);
}

@media (max-width: 768px) {
    .section, .totals {
        padding: 1rem 0.7rem;
        border-radius: 10px;
    }
    .footer {
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
    }

    span{

    color: #0b3c5d;

    }
}

    </style>
</head>

<body>
    <form method="POST" action="Book the Cultural Gems & Wildlife Wonders.php">
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="container-fluid position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="#" class="navbar-brand p-0">
                    <h1 class="m-0"><i class="fa fa-map-marker-alt me-3"></i>AsanTravels</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>
            </nav>
        </div>

        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4" style="color: white;">Book the Cultural Gems & Wildlife Wonders</h3>
                <ol class="breadcrumb justify-content-center mb-0"></ol>
            </div>
        </div>

        <div class="container"><br>
            <div class="section"><br>
                <h2 class="bdate">Book Your Date</h2>
                <label for="tripDays">Number of Days:</label>
                <select id="tripDays" name="tripDays" required>
                    <option value="">Select number of days</option>
                    <option value="1">1 Day</option>
                    <option value="2">2 Days</option>
                    <option value="3">3 Days</option>
                    <option value="4">4 Days</option>
                    <option value="5">5 Days</option>
                    <option value="6">6 Days</option>
                    <option value="7">7 Days</option>
                    <option value="8">8 Days</option>
                    <option value="9">9 Days</option>
                    <option value="10">10 Days</option>
                    <option value="11">11 Days</option>
                    <option value="12">12 Days</option>
                    <option value="13">13 Days</option>
                    <option value="14">14 Days</option>
                </select>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required />
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" readonly />
            </div>

            <div class="section">
                <h2>Customizable Room & Passenger</h2>
                <label for="numAdults">Number of Adults:</label>
                <input type="number" id="numAdults" name="numAdults" min="1" value="1"
                    placeholder="Enter number of adults" required />
                <label for="numChildren">Number of Children:</label>
                <input type="number" id="numChildren" name="numChildren" min="0" value="0"
                    placeholder="Enter number of children" required />
                <label for="roomOptions">Room Combination:</label>
                <select id="roomOptions" name="roomOptions" required>
                    <option value="">Select room combination</option>
                </select>
            </div>

            <div class="section">
                <h2>Optional Tours</h2>
                <label><input type="checkbox" class="tour" name="optionalTours[]" value="Colombo Street Food Tour"> Colombo
                    Street Food Tour (+$95 pp)</label>
                <label><input type="checkbox" class="tour" name="optionalTours[]" value="Safari Yala National Park"> Safari
                    Yala National Park (+$135 pp)</label>
                <label><input type="checkbox" class="tour" name="optionalTours[]" value="Colombo City by Tuk Tuk"> Colombo City
                    by Tuk Tuk (+$96 pp)</label>
            </div>

            <div class="totals">
                <h2>Order Summary</h2>
                <p><strong>Base Price:</strong> $<span id="base_price">0.00</span></p>
                <p><strong>Extras:</strong> $<span id="extras">0.00</span></p>
                <p><strong>Total:</strong> $<span id="total">0.00</span></p>
                <p><strong>Pay on Arrival:</strong> $<span id="arrival_payment">0.00</span></p>
            </div>

            <div class="section">
                <button type="button" onclick="calculateTotal(event)">Recalculate Total</button>
            </div>

          <div class="container-fluid booking py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h1 class="text-white mb-4">Online Booking</h1>
                <p class="text-white mb-4">Experience the wonders of Sri Lanka with our user-friendly online booking system, designed to make your travel planning effortless and enjoyable—reserve your adventure today and unlock a world of breathtaking landscapes, vibrant wildlife, and unforgettable memories, all waiting for you in this magical island paradise. </p>
                <p class="text-white mb-4">Secure your spot on an extraordinary journey across Sri Lanka by booking online with us—enjoy seamless reservations, instant confirmations, and expert support as you prepare to explore ancient ruins, pristine beaches, lush rainforests, and the unique culture that makes this destination truly special for every traveler.</p>
            </div>
            <div class="col-lg-6">
                <h1 class="text-white mb-3">Confirm Your Booking</h1>
                <!-- Removed inner form tag here -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <span style="color: white;">Your Name</span>
                        <div class="form-floating">
                            <input type="text" class="form-control bg-white border-0" id="name" name="name" placeholder="Your Name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <span style="color: white;">Your Email</span>
                        <div class="form-floating">
                            <input type="email" class="form-control bg-white border-0" id="email" name="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6" id="enter_number" style="display: none;"></div>
                    <div class="col-12">
                        <span style="color: white;">Special Request</span>
                        <div class="form-floating">
                            <textarea class="form-control bg-white border-0" id="message" name="message" placeholder="Special Request" style="height: 100px"></textarea><br>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary text-white w-100 py-3" type="submit" name="submit2">Confirm Booking</button>
                    </div>
                </div>
                <!-- End of inner form wrapper -->
            </div>
        </div>
    </div>
</div>

    </form>

    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <script>
        // ========== Function 1: Auto-Calculate End Date ==========
        function calculateEndDate() {
            const days = parseInt(document.getElementById('tripDays').value) || 0;
            const startDateInput = document.getElementById('start_date').value;
            
            if (!startDateInput || days === 0) {
                document.getElementById('end_date').value = '';
                return;
            }
            
            const startDate = new Date(startDateInput);
            startDate.setDate(startDate.getDate() + (days - 1));
            
            const yyyy = startDate.getFullYear();
            const mm = String(startDate.getMonth() + 1).padStart(2, '0');
            const dd = String(startDate.getDate()).padStart(2, '0');
            
            document.getElementById('end_date').value = `${yyyy}-${mm}-${dd}`;
        }

        // ========== Function 2: Auto-Populate Room Options ==========
        function updateRoomOptions() {
            const adults = parseInt(document.getElementById('numAdults').value) || 1;
            const children = parseInt(document.getElementById('numChildren').value) || 0;
            const totalPassengers = adults + children;
            const roomSelect = document.getElementById('roomOptions');
            
            roomSelect.innerHTML = '<option value="">Select room combination...</option>';
            
            if (totalPassengers === 0) return;

            const maxDoubleRooms = Math.floor(totalPassengers / 2);
            for (let doubleRooms = 0; doubleRooms <= maxDoubleRooms; doubleRooms++) {
                const singleRooms = totalPassengers - (doubleRooms * 2);
                
                const parts = [];
                if (doubleRooms > 0) parts.push(`${doubleRooms} double room${doubleRooms > 1 ? 's' : ''}`);
                if (singleRooms > 0) parts.push(`${singleRooms} single room${singleRooms > 1 ? 's' : ''}`);
                
                const option = document.createElement('option');
                option.value = parts.join(' and ');
                option.textContent = parts.join(' and ');
                roomSelect.appendChild(option);
            }
        }

        // ========== Function 3: Calculate Total Price ==========
        function calculateTotal(event) {
            if (event) event.preventDefault();

            const adults = parseInt(document.getElementById('numAdults').value) || 0;
            const children = parseInt(document.getElementById('numChildren').value) || 0;
            const passengerCount = adults + children;
            
            if (passengerCount < 1) {
                alert('Please enter at least 1 passenger.');
                return;
            }

            const basePricePerPassenger = 750;
            const basePrice = basePricePerPassenger * passengerCount;

            const tourCheckboxes = document.querySelectorAll('.tour');
            let extraToursTotal = 0;
            tourCheckboxes.forEach(cb => {
                if (cb.checked) {
                    if (cb.value === "Colombo Street Food Tour") extraToursTotal += 95 * passengerCount;
                    else if (cb.value === "Safari Yala National Park") extraToursTotal += 135 * passengerCount;
                    else if (cb.value === "Colombo City by Tuk Tuk") extraToursTotal += 96 * passengerCount;
                }
            });

            const subtotal = basePrice + extraToursTotal;
            const payOnArrival = subtotal / 2;

            document.getElementById('base_price').textContent = basePrice.toFixed(2);
            document.getElementById('extras').textContent = extraToursTotal.toFixed(2);
            document.getElementById('total').textContent = subtotal.toFixed(2);
            document.getElementById('arrival_payment').textContent = payOnArrival.toFixed(2);
        }

        // ========== Event Listeners ==========
        // Auto-calculate end date when days or start date changes
        document.getElementById('tripDays').addEventListener('change', calculateEndDate);
        document.getElementById('start_date').addEventListener('change', calculateEndDate);

        // Auto-populate rooms and recalculate price when adults/children change
        document.getElementById('numAdults').addEventListener('change', function() {
            updateRoomOptions();
            calculateTotal(null);
        });
        document.getElementById('numChildren').addEventListener('change', function() {
            updateRoomOptions();
            calculateTotal(null);
        });

        // Recalculate price when optional tours change
        document.querySelectorAll('.tour').forEach(checkbox => {
            checkbox.addEventListener('change', () => calculateTotal(null));
        });

        // ========== Initialize on Page Load ==========
        window.onload = function () {
            updateRoomOptions();
            calculateTotal(null);
        };
    </script>

    <script src="js/main.js"></script>
</body>

</html>
