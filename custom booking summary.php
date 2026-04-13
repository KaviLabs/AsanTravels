<?php
session_start();

$conn = new mysqli("localhost", "root", "", "asantravels_og");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get POST data
$tripDays = $_POST['trip_days'] ?? 0;
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$numAdults = $_POST['num_adults'] ?? 1;
$numChildren = $_POST['num_children'] ?? 0;
$roomOption = $_POST['room_option'] ?? '';
$customerName = $_POST['name'] ?? '';
$customerEmail = $_POST['email'] ?? '';
$specialRequest = $_POST['special_request'] ?? '';
$dayPlanDataJson = $_POST['day_plan_data'] ?? '{}';
$transportKmTotal = $_POST['transport_km_total'] ?? '0';

$dayPlanData = json_decode($dayPlanDataJson, true) ?: [];

// Calculate totals
$basePricePerAdult = 750;
$basePricePerChild = 500;
$transportCostPerKm = 0.6;

// Route analysis disabled
$routeAnalysis = [];
$activityAdjustments = [];
$optimizedDayPlanData = $dayPlanData;
$hasLongRoutes = false;

// Removed route feasibility analysis (SmartTravelPlanner)

$totalPassengers = $numAdults + $numChildren;
$baseActivityCost = 0;
$activityBreakdown = [];
$tourAdjustmentSuggestions = []; // New: Track which tours should be adjusted

// Parse day plan and calculate costs, and check for tour adjustments
foreach ($dayPlanData as $day => $plan) {
    if (isset($plan['activities']) && is_array($plan['activities'])) {
        // Check if this day has a long route to the next day
        $hasLongRouteAhead = isset($routeAnalysis[$day]) && !$routeAnalysis[$day]['feasible'];
        
        foreach ($plan['activities'] as $activity) {
            $adultCost = (float)($activity['adult'] ?? 0);
            $childCost = (float)($activity['child'] ?? 0);
            
            $activityTotal = ($numAdults * $adultCost) + ($numChildren * $childCost);
            $baseActivityCost += $activityTotal;
            
            $activityBreakdown[] = [
                'day' => $day,
                'name' => $activity['name'] ?? 'Unknown',
                'location' => $activity['location'] ?? 'Unknown',
                'adultPrice' => $adultCost,
                'childPrice' => $childCost,
                'adultCount' => $numAdults,
                'childCount' => $numChildren,
                'total' => $activityTotal
            ];
        }
    }
}

$transportCost = (float)$transportKmTotal * $transportCostPerKm;
$totalCost = $baseActivityCost + $transportCost;
$paymentOnArrival = $totalCost / 2;

// Generate custom package name
$packageParts = [];
foreach ($dayPlanData as $day => $plan) {
    if (isset($plan['location']) && !empty($plan['location'])) {
        if (!in_array($plan['location'], $packageParts)) {
            $packageParts[] = $plan['location'];
        }
    }
}
$customPackageName = implode(' & ', $packageParts) ?: 'Custom Sri Lanka Tour';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Your Custom Tour Package - AsanTravels</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Custom tour booking summary for Sri Lanka travel" name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* ───── iOS-Friendly Booking Summary Style ───── */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0; padding: 0;
            background: linear-gradient(135deg, #f8f8f8 0%, #e0ecff 100%);
            color: #222;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5 {
            color: #0b3c5d;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        /* ── Package Card ── */
        .package-card {
            background: rgba(255,255,255,0.92);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 12px 48px rgba(11,60,93,0.1);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .package-name {
            background: linear-gradient(135deg, rgba(19, 53, 123, .85) 0%, #3282b8 100%);
            color: white;
            padding: 28px;
            border-radius: 16px;
            margin-bottom: 28px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(19, 53, 123, 0.2);
        }
        .package-name h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #fff;
        }
        .package-name p {
            opacity: 0.9;
            margin: 0;
            font-size: 15px;
            color: #fff;
        }

        /* ── Trip Details Grid ── */
        .trip-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 28px;
            padding-bottom: 28px;
            border-bottom: 2px solid rgba(11,60,93,0.08);
        }
        .detail-box {
            background: rgba(19, 53, 123, 0.04);
            padding: 18px;
            border-radius: 14px;
            border-left: 4px solid rgba(19, 53, 123, .7);
            transition: transform 0.2s ease;
        }
        .detail-box:hover {
            transform: translateY(-2px);
        }
        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 18px;
            font-weight: 800;
            color: #0b3c5d;
        }

        /* ── Customer Info ── */
        .customer-info {
            background: rgba(19, 53, 123, 0.04);
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 28px;
            border-left: 4px solid #0b3c5d;
        }
        .customer-info h4 {
            color: #0b3c5d;
            font-weight: 700;
            margin-bottom: 14px;
            font-size: 1.1rem;
        }
        .customer-item {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
        }
        .customer-item strong { color: #333; }

        /* ── Day Itinerary ── */
        .day-itinerary { margin-bottom: 28px; }
        .day-box {
            background: rgba(248,249,255,0.9);
            border-left: 4px solid rgba(19, 53, 123, .7);
            padding: 20px;
            margin-bottom: 14px;
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .day-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11,60,93,0.08);
        }
        .day-title {
            font-size: 17px;
            font-weight: 800;
            color: rgba(19, 53, 123, .8);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .day-location {
            background: linear-gradient(135deg, rgba(19, 53, 123, .85) 0%, #3282b8 100%);
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-weight: 700;
            display: inline-block;
            font-size: 0.95rem;
        }

        /* ── Activity List ── */
        .activity-list { margin-left: 16px; }
        .activity-item {
            background: white;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #28a745;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: transform 0.2s ease;
        }
        .activity-item:hover { transform: translateX(4px); }
        .activity-name {
            font-weight: 700;
            color: #333;
            font-size: 0.95rem;
        }

        /* ── Special Request ── */
        .special-request {
            background: rgba(40, 167, 69, 0.08);
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 28px;
        }
        .special-request h5 {
            color: #1b5e20;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .special-request p {
            color: #333;
            margin: 0;
            line-height: 1.6;
        }

        /* ── Pricing Info Banner ── */
        .pricing-banner {
            background: rgba(19, 53, 123, 0.06);
            border: 2px solid rgba(19, 53, 123, 0.15);
            border-radius: 14px;
            padding: 24px;
            margin: 24px 0;
            text-align: center;
        }
        .pricing-banner h5 {
            margin-top: 0;
            color: #0b3c5d;
            font-size: 1.1rem;
        }
        .pricing-banner p {
            margin: 10px 0 0;
            color: #0b3c5d;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* ── Action Buttons (iOS pill style) ── */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 28px;
        }
        .action-buttons .btn {
            padding: 16px 24px;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
            font-size: 15px;
            min-height: 52px;
            -webkit-tap-highlight-color: transparent;
        }
        .action-buttons .btn-primary {
            background: linear-gradient(90deg, rgba(19, 53, 123, .8), #3282b8 100%);
            color: white;
        }
        .action-buttons .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(50,130,184,0.3);
        }
        .action-buttons .btn-secondary {
            background: #e9ecef;
            color: #333;
        }
        .action-buttons .btn-secondary:hover {
            background: #dde0e5;
            transform: translateY(-1px);
        }

        /* ── Breadcrumb (booking page match) ── */
        .breadcrumb { background: transparent; font-size: 1.1rem; }
        .breadcrumb-item a { color: #fff; text-decoration: underline dotted; }
        .breadcrumb-item.active { color: #ffe082; font-weight: 600; }

        /* ── Footer ── */
        .footer {
            background: linear-gradient(90deg, rgba(19, 53, 123, .8) 0%, black);
            color: #fff;
            border-top-left-radius: 32px;
            border-top-right-radius: 32px;
            box-shadow: 0 -4px 24px rgba(11,60,93,0.12);
        }
        .footer a { color: #ffe082; text-decoration: none; }
        .footer a:hover { color: #fff; text-shadow: 0 2px 8px #ffe08266; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 10px; background: #e0ecff; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(120deg, #3282b8 0%, rgba(19, 53, 123, .8));
            border-radius: 8px;
        }

        /* ── Spinner ── */
        #spinner {
            z-index: 9999;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(6px);
        }

        /* ── Back to Top ── */
        .back-to-top {
            position: fixed; bottom: 32px; right: 32px;
            background: linear-gradient(135deg, #3282b8 0%, rgba(19, 53, 123, .8));
            color: #fff; border-radius: 50%;
            box-shadow: 0 4px 24px rgba(50,130,184,0.22);
            transition: background 0.3s, transform 0.2s;
        }
        .back-to-top:hover {
            background: #ffe082; color: rgba(19, 53, 123, .8);
            transform: scale(1.1) rotate(-10deg);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .package-card { padding: 20px; border-radius: 16px; }
            .trip-details { grid-template-columns: 1fr; }
            .action-buttons { grid-template-columns: 1fr; }
            .package-name h2 { font-size: 22px; }
            .package-name { padding: 20px; }
            .footer {
                border-top-left-radius: 18px;
                border-top-right-radius: 18px;
            }
            span { color: #0b3c5d; }
        }
    </style>
</head>

<body>
    <!-- Spinner -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <!-- Navbar (same as booking pages) -->
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

    <!-- Hero Breadcrumb -->
    <div class="container-fluid bg-breadcrumb">
        <div class="container text-center py-5" style="max-width: 900px;">
            <h3 class="text-white display-3 mb-4" style="color: white;">✅ Your Custom Package</h3>
            <p class="text-white mb-0" style="font-size: 1.15rem; opacity: 0.9;">Review your personalized Sri Lanka tour before confirming</p>
            <ol class="breadcrumb justify-content-center mb-0"></ol>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4" style="max-width: 900px;">

        <form id="confirmBookingForm" method="POST" action="booking_summary.php">
            <div class="package-card">
                <!-- Package Name -->
                <div class="package-name">
                    <h2>📦 <?php echo htmlspecialchars($customPackageName); ?></h2>
                    <p><?php echo $tripDays; ?> Days • <?php echo $totalPassengers; ?> Passengers</p>
                </div>

                <!-- Trip Details -->
                <div class="trip-details">
                    <div class="detail-box">
                        <div class="detail-label"><i class="fas fa-calendar me-2"></i>Start Date</div>
                        <div class="detail-value"><?php echo date('M d, Y', strtotime($startDate)); ?></div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label"><i class="fas fa-calendar me-2"></i>End Date</div>
                        <div class="detail-value"><?php echo date('M d, Y', strtotime($endDate)); ?></div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label"><i class="fas fa-users me-2"></i>Travelers</div>
                        <div class="detail-value"><?php echo $numAdults; ?> Adults + <?php echo $numChildren; ?> Children</div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label"><i class="fas fa-bed me-2"></i>Rooms</div>
                        <div class="detail-value"><?php echo htmlspecialchars($roomOption); ?></div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="customer-info">
                    <h4><i class="fas fa-user-circle me-2"></i>Guest Information</h4>
                    <div class="customer-item">
                        <strong>Name:</strong>
                        <span><?php echo htmlspecialchars($customerName); ?></span>
                    </div>
                    <div class="customer-item">
                        <strong>Email:</strong>
                        <span><?php echo htmlspecialchars($customerEmail); ?></span>
                    </div>
                </div>

                <!-- Special Request -->
                <?php if (!empty($specialRequest)): ?>
                <div class="special-request">
                    <h5><i class="fas fa-star me-2"></i>Special Request</h5>
                    <p><?php echo htmlspecialchars($specialRequest); ?></p>
                </div>
                <?php endif; ?>


                <!-- Day-by-Day Itinerary -->
                <div class="day-itinerary">
                    <h4 style="color: #0b3c5d; font-weight: 800; margin-bottom: 20px;">
                        <i class="fas fa-route me-2"></i>Day-by-Day Itinerary
                    </h4>

                    <?php foreach ($dayPlanData as $day => $plan): ?>
                        <div class="day-box">
                            <div class="day-title">
                                <i class="fas fa-calendar-check"></i> Day <?php echo $day; ?>
                                <?php 
                                    $dayDate = new DateTime($startDate);
                                    $dayDate->modify('+' . ($day - 1) . ' days');
                                    echo $dayDate->format('(M d)');
                                ?>
                            </div>

                            <?php if (!empty($plan['location'])): ?>
                                <div class="day-location">
                                    <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($plan['location']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($plan['activities']) && count($plan['activities']) > 0): ?>
                                <div class="activity-list">
                                    <?php foreach ($plan['activities'] as $activity): ?>
                                        <div class="activity-item">
                                            <div>
                                                <div class="activity-name"><?php echo htmlspecialchars($activity['name']); ?></div>
                                                <small style="color: #999;">
                                                    <?php echo htmlspecialchars($activity['location']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="color: #999; font-style: italic; padding: 15px;">
                                    No activities selected for this day
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pricing message -->
                <div class="pricing-banner">
                    <h5><i class="fas fa-envelope me-2"></i>Pricing Information</h5>
                    <p>
                        Thank you for creating your custom itinerary! Our team will contact you shortly with detailed pricing information based on your selections.
                    </p>
                </div>

                <!-- Hidden Fields to Pass Data -->
                <input type="hidden" name="trip_days" value="<?php echo htmlspecialchars($tripDays); ?>">
                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                <input type="hidden" name="num_adults" value="<?php echo htmlspecialchars($numAdults); ?>">
                <input type="hidden" name="num_children" value="<?php echo htmlspecialchars($numChildren); ?>">
                <input type="hidden" name="room_option" value="<?php echo htmlspecialchars($roomOption); ?>">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($customerName); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($customerEmail); ?>">
                <input type="hidden" name="special_request" value="<?php echo htmlspecialchars($specialRequest); ?>">
                <input type="hidden" name="day_plan_data" value="<?php echo htmlspecialchars($dayPlanDataJson); ?>">
                <input type="hidden" name="transport_km_total" value="<?php echo htmlspecialchars($transportKmTotal); ?>">
                <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($customPackageName); ?>">
                <input type="hidden" name="action" value="confirm_booking">

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i> Edit Trip
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-2"></i> Confirm & Book
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Back to top -->
    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- Scripts (same as booking pages) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <script>
        document.getElementById('confirmBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            fetch('save_itinerary.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Booking Confirmed!\n\nBooking ID: ' + data.booking_id + '\n\nThank you for booking with us!');
                    window.location.href = 'thank_you-booking.php?booking_id=' + data.booking_id + '&name=' + encodeURIComponent(document.querySelector('input[name="name"]').value);
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to save booking'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                alert('❌ Error: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>

    <script>
    (function($){
        setTimeout(function(){ if($('#spinner').length > 0) $('#spinner').removeClass('show'); }, 1);
        $(window).scroll(function(){
            if($(this).scrollTop() > 300) $('.back-to-top').fadeIn('slow');
            else $('.back-to-top').fadeOut('slow');
        });
        $('.back-to-top').click(function(){ $('html, body').animate({scrollTop:0}, 1500, 'easeInOutExpo'); return false; });
    })(jQuery);
    </script>
</body>
</html>
