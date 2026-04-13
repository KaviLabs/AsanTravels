<?php
$conn = new mysqli("localhost", "root", "", "asantravels_og");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/**
 * Note: Location names are now used directly from the database.
 * No normalization needed as database maintains canonical location names.
 */

/**
 * Fetch tours from custom_tours table
 */
$tours_result = $conn->query("SELECT * FROM custom_tours ORDER BY location, activity");
$tours_data = [];
$locations_set = [];

if ($tours_result) {
    while ($row = $tours_result->fetch_assoc()) {
        $norm = $row['location'] ?? '';
        $row['location'] = $norm;
        $tours_data[] = $row;
        if (!empty($norm)) {
            $locations_set[$norm] = true;
        }                    
    }
}
$locations = array_keys($locations_set);
sort($locations);

/**
 * Fetch locations from locations table with coordinates
 */
$locs_result = $conn->query("SELECT id, name, latitude as lat, longitude as lng FROM locations ORDER BY name");
$locs_data = [];
if ($locs_result) {
    while($row = $locs_result->fetch_assoc()){
        $locs_data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Custom Itinerary Planner - AsanTravels</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Custom tour itinerary planner for Sri Lanka travel" name="description">

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
        /* ───── iOS-Friendly Booking Page Style ───── */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0; padding: 0;
            background: linear-gradient(135deg, #f8f8f8 0%, #e0ecff 100%);
            color: #222;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2 {
            color: #0b3c5d;
            letter-spacing: 1px;
            font-weight: 700;
            text-shadow: 0 2px 12px rgba(11,60,93,0.08);
        }

        /* ── Section Cards (glass effect) ── */
        .section {
            margin-bottom: 2rem;
            background: rgba(255,255,255,0.88);
            box-shadow: 0 4px 32px rgba(11,60,93,0.08);
            border-radius: 18px;
            padding: 2rem 2.5rem 1.5rem 2.5rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .section:hover {
            box-shadow: 0 8px 40px rgba(11,60,93,0.16);
            transform: translateY(-2px);
        }

        /* ── Form Elements (iOS feel) ── */
        label {
            display: block;
            margin: 1.2rem 0 0.5rem;
            font-weight: 600;
            color: #0b3c5d;
            letter-spacing: 0.3px;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: rgba(248,249,255,0.8);
            box-shadow: 0 2px 8px rgba(11,60,93,0.04);
            font-size: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.25s cubic-bezier(.4,0,.2,1);
            -webkit-appearance: none;
            font-family: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            background: #fff;
            border-color: rgba(19, 53, 123, 0.6);
            box-shadow: 0 0 0 4px rgba(19, 53, 123, 0.12);
        }

        input[type="checkbox"] {
            accent-color: rgba(19, 53, 123, .8);
            transform: scale(1.25);
            margin-right: 0.5rem;
        }

        /* ── Layout Grid ── */
        .planner-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 30px;
            align-items: start;
        }

        /* ── Left Setup Panel ── */
        .setup-panel {
            background: rgba(255,255,255,0.92);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 12px 40px rgba(11,60,93,0.12);
            position: sticky;
            top: 100px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .setup-title {
            font-size: 22px;
            font-weight: 800;
            color: #0b3c5d;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            font-weight: 700; color: #333;
            margin-bottom: 8px; display: block; font-size: 14px;
        }

        .info-box {
            background: rgba(19, 53, 123, 0.06);
            border-left: 4px solid rgba(19, 53, 123, .8);
            padding: 16px;
            border-radius: 12px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .info-item span:last-child {
            color: rgba(19, 53, 123, .8);
            font-weight: 800;
        }

        /* ── Right Day Planner ── */
        .day-planner {
            background: rgba(255,255,255,0.92);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 12px 40px rgba(11,60,93,0.12);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .day-header {
            background: linear-gradient(135deg, rgba(19, 53, 123, .85) 0%, #3282b8 100%);
            color: white;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 24px rgba(19, 53, 123, 0.2);
        }
        .day-header-info h2 { font-size: 28px; font-weight: 800; margin-bottom: 4px; color: #fff; }
        .day-header-info p { opacity: 0.9; margin: 0; font-size: 0.95rem; }

        .day-progress {
            background: rgba(255,255,255,0.2);
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .day-progress.complete { background: #28a745; }

        /* ── Activity Cards ── */
        .activities-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        .activity-card {
            background: rgba(248,249,255,0.9);
            border: 2px solid #e9ecef;
            border-radius: 14px;
            padding: 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .activity-card:hover {
            border-color: rgba(19, 53, 123, .6);
            box-shadow: 0 8px 24px rgba(19, 53, 123, 0.12);
            transform: translateY(-3px);
        }
        .activity-card input[type="checkbox"] {
            position: absolute; top: 14px; right: 14px;
            width: 20px; height: 20px; cursor: pointer;
        }
        .activity-card input[type="checkbox"]:checked { accent-color: rgba(19, 53, 123, .8); }
        .activity-info { padding-right: 30px; }
        .activity-name { font-weight: 700; font-size: 15px; margin-bottom: 5px; color: #222; }
        .activity-description { font-size: 13px; color: #666; margin-bottom: 10px; line-height: 1.4; }
        .activity-price {
            font-size: 12px;
            background: linear-gradient(135deg, rgba(19, 53, 123, .8) 0%, #3282b8 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 600;
        }

        /* ── Selected Activities ── */
        .selected-activities {
            background: rgba(19, 53, 123, 0.04);
            border-radius: 14px;
            padding: 18px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }
        .selected-activities h5 {
            font-weight: 700;
            color: #0b3c5d;
            margin-bottom: 14px;
        }
        .selected-activity-item {
            background: white;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 3px solid #28a745;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .selected-activity-item:last-child { margin-bottom: 0; }
        .no-activities { color: #999; font-style: italic; padding: 20px; text-align: center; }

        /* ── Completion Message ── */
        .completion-message {
            background: #d4edda;
            color: #155724;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            display: none;
            font-weight: 500;
        }
        .completion-message.show { display: block; }

        /* ── Cost Summary ── */
        .cost-summary {
            background: linear-gradient(135deg, rgba(19, 53, 123, .85) 0%, #3282b8 100%);
            color: white;
            padding: 24px;
            border-radius: 14px;
            margin-top: 24px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(19, 53, 123, 0.2);
        }
        .cost-summary h4 { font-weight: 700; margin-bottom: 8px; color: #fff; }
        .cost-amount { font-size: 36px; font-weight: 800; }

        /* ── Buttons (iOS pill style) ── */
        .day-actions {
            display: flex;
            gap: 14px;
            margin-top: 28px;
        }
        .day-actions .btn {
            flex: 1;
            padding: 14px 20px;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
            font-size: 15px;
            -webkit-tap-highlight-color: transparent;
            min-height: 48px;
        }
        .day-actions .btn-primary {
            background: linear-gradient(90deg, rgba(19, 53, 123, .8), #3282b8 100%);
            color: white;
        }
        .day-actions .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(50,130,184,0.3);
        }
        .day-actions .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        .day-actions .btn-secondary {
            background: #e9ecef;
            color: #333;
        }
        .day-actions .btn-secondary:hover { background: #dde0e5; }
        .day-actions .btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ── Totals (booking page style) ── */
        .totals {
            background: rgba(11,60,93,0.06);
            padding: 2rem;
            border-radius: 18px;
            margin-top: 2rem;
            box-shadow: 0 4px 24px rgba(11,60,93,0.08);
            font-size: 1.05rem;
        }
        .totals h2 { color: rgba(19, 53, 123, .8); margin-bottom: 1rem; }

        /* ── Breadcrumb (booking page match) ── */
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

        /* ── Footer (booking page match) ── */
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

        /* ── Back to Top ── */
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

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .planner-grid { grid-template-columns: 1fr; }
            .setup-panel { position: static; }
            .activities-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .section, .totals {
                padding: 1.2rem 1rem;
                border-radius: 14px;
            }
            .setup-panel, .day-planner {
                padding: 20px;
                border-radius: 16px;
            }
            .footer {
                border-top-left-radius: 18px;
                border-top-right-radius: 18px;
            }
            .day-header { padding: 18px; }
            .day-header-info h2 { font-size: 22px; }
            span { color: #0b3c5d; }
        }
    </style>
</head>

<body>
    <form id="travelForm" method="POST" action="save_itinerary.php">

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

        <!-- Hero Breadcrumb (same as booking pages) -->
        <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="text-white display-3 mb-4" style="color: white;">🌍 Custom Itinerary Planner</h3>
                <p class="text-white mb-0" style="font-size: 1.15rem; opacity: 0.9;">Plan your perfect Sri Lanka trip — day by day, location by location</p>
                <ol class="breadcrumb justify-content-center mb-0"></ol>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container py-4">
            <div class="planner-grid">

                <!-- LEFT SIDEBAR - TRIP SETUP -->
                <div class="setup-panel">
                    <div class="setup-title">
                        <i class="fas fa-cog"></i> Trip Setup
                    </div>

                    <div class="form-group">
                        <label for="tripDays">Number of Days:</label>
                        <select id="tripDays" name="trip_days" class="form-select" required onchange="initializeDayPlanner(); calculateEndDate()">
                            <option value="">Select duration...</option>
                            <?php for($i = 1; $i <= 14; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> Day(s)</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" name="start_date" class="form-control" required onchange="calculateEndDate()">
                    </div>

                    <div class="form-group">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" name="end_date" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="numAdults">Adults:</label>
                        <input type="number" id="numAdults" name="num_adults" class="form-control" min="1" value="1" required onchange="updateCostSummary()">
                    </div>

                    <div class="form-group">
                        <label for="numChildren">Children:</label>
                        <input type="number" id="numChildren" name="num_children" class="form-control" min="0" value="0" onchange="updateCostSummary(); updateRoomOptions()">
                    </div>

                    <div class="form-group">
                        <label for="roomOptions">Room Combination:</label>
                        <select id="roomOptions" name="room_option" class="form-select" required onchange="updateCostSummary()">
                            <option value="">Select room combination...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="customerName">Full Name:</label>
                        <input type="text" id="customerName" name="name" class="form-control" required placeholder="Enter your name">
                    </div>

                    <div class="form-group">
                        <label for="customerEmail">Email:</label>
                        <input type="email" id="customerEmail" name="email" class="form-control" required placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="specialRequest">Special Request (Optional):</label>
                        <textarea id="specialRequest" name="special_request" class="form-control" rows="3" placeholder="Any special requirements?"></textarea>
                    </div>

                    <div class="info-box">
                        <div class="info-item">
                            <span><i class="fas fa-users me-2"></i> Total People:</span>
                            <span id="totalPeople">1</span>
                        </div>
                        <div class="info-item">
                            <span><i class="fas fa-calendar me-2"></i> Total Days:</span>
                            <span id="totalDays">-</span>
                        </div>
                        <div class="info-item">
                            <span><i class="fas fa-check-circle me-2"></i> Days Planned:</span>
                            <span id="daysPlanned">0</span>
                        </div>
                    </div>

                    <div class="cost-summary" id="costSummary" style="display: none;"></div>
                </div>

                <!-- RIGHT SIDE - DAY PLANNER -->
                <div class="day-planner">
                    <div class="completion-message" id="completionMessage">
                        <i class="fas fa-check-circle me-2"></i> 
                        <strong>Great!</strong> You've planned all days. Ready to book your trip!
                    </div>

                    <div id="dayPlannerContent">
                        <p class="text-muted text-center" style="padding: 40px 20px;">
                            <i class="fas fa-arrow-left me-2"></i> Select number of days to start planning
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </form>

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
        // Server data
        const tours = <?php echo json_encode($tours_data); ?>;
        const locations = <?php echo json_encode($locations); ?>;
        const locationCoords = <?php echo json_encode($locs_data); ?>;

        // Build index of coordinates: name -> {lat, lng}
        const LOC_INDEX = {};
        (locationCoords || []).forEach(l => {
            LOC_INDEX[l.name] = { lat: parseFloat(l.lat), lng: parseFloat(l.lng) };
        });

        let currentDay = 1;
        let totalDays = 0;
        let dayPlanData = {}; // Store selected activities per day

        // Haversine and travel estimation
        function haversineKm(a, b) {
            const toRad = d => d * Math.PI / 180;
            const R = 6371;
            const dLat = toRad(b.lat - a.lat), dLng = toRad(b.lng - a.lng);
            const lat1 = toRad(a.lat), lat2 = toRad(b.lat);
            const h = Math.sin(dLat/2)**2 + Math.cos(lat1)*Math.cos(lat2)*Math.sin(dLng/2)**2;
            return 2 * R * Math.asin(Math.sqrt(h));
        }
        function estimateDriveHours(km) {
            const avgSpeed = 45; // km/h for Sri Lanka roads
            return km / avgSpeed;
        }
        function getNearbyLocations(baseName, radiusKm = 80) {
            const base = LOC_INDEX[baseName];
            if (!base) return [];
            return (locationCoords || [])
                .filter(l => l.name !== baseName)
                .map(l => ({ name: l.name, km: haversineKm(base, { lat: parseFloat(l.lat), lng: parseFloat(l.lng) }) }))
                .filter(x => x.km <= radiusKm)
                .sort((a,b) => a.km - b.km)
                .map(x => x.name);
        }

        const MAX_CONSECUTIVE_HOP_HOURS = 6;
        const NEARBY_RADIUS_KM = 80;

        function evaluateDayHop(day) {
            if (day < 1 || day >= totalDays) return null;
            const locA = dayPlanData[day]?.location;
            const locB = dayPlanData[day+1]?.location;
            if (!locA || !locB || !LOC_INDEX[locA] || !LOC_INDEX[locB]) return null;

            const km = haversineKm(LOC_INDEX[locA], LOC_INDEX[locB]);
            const hrs = estimateDriveHours(km);
            return { km, hrs, locA, locB };
        }

        function suggestAlternativesForDay(day) {
            const res = evaluateDayHop(day);
            if (!res) return { impractical: false, suggestions: [] };
            const impractical = res.hrs > MAX_CONSECUTIVE_HOP_HOURS;
            return {
                impractical,
                evalRes: res,
                suggestions: impractical ? getNearbyLocations(res.locA, NEARBY_RADIUS_KM) : []
            };
        }

        function replaceNextDayLocation(day, newLoc) {
            if (day >= totalDays) return;
            dayPlanData[day+1].location = newLoc;
            dayPlanData[day+1].activities = []; // clear activities for the new location
            renderDayPlanner();
        }

        // Optional transport cost
        const TRANSPORT_COST_PER_KM = 0.6; // USD per km (adjust for your pricing)
        function computeTransportCostKm() {
            let kmTotal = 0;
            for (let day = 2; day <= totalDays; day++) {
                const prev = dayPlanData[day-1]?.location, curr = dayPlanData[day]?.location;
                if (prev && curr && LOC_INDEX[prev] && LOC_INDEX[curr]) {
                    kmTotal += haversineKm(LOC_INDEX[prev], LOC_INDEX[curr]);
                }
            }
            return kmTotal;
        }

        // Activity timeline helpers
        function getActivityDuration(activityId) {
            const t = tours.find(x => String(x.id) === String(activityId));
            return t && t.duration_minutes ? parseInt(t.duration_minutes) : 90; // default 90 min
        }
        function formatHM(totalMin) {
            const h = Math.floor(totalMin/60), m = totalMin%60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
        }
        function buildDayTimeline(day) {
            const loc = dayPlanData[day].location;
            const acts = dayPlanData[day].activities;
            if (!loc || acts.length === 0) return [];

            let minutesCursor = 9 * 60;  // 09:00 start
            const timeline = [];

            // Drive from previous day
            if (day > 1 && dayPlanData[day-1].location) {
                const prevLoc = dayPlanData[day-1].location;
                if (LOC_INDEX[prevLoc] && LOC_INDEX[loc]) {
                    const km = haversineKm(LOC_INDEX[prevLoc], LOC_INDEX[loc]);
                    const driveMin = Math.round(estimateDriveHours(km) * 60);
                    timeline.push({
                        type:'drive', from:prevLoc, to:loc, km:Math.round(km),
                        start:formatHM(minutesCursor), end:formatHM(minutesCursor+driveMin)
                    });
                    minutesCursor += driveMin + 30; // buffer
                }
            }

            // Activities with lunch near 13:00
            acts.forEach(a => {
                const dur = getActivityDuration(a.id);
                if (minutesCursor < 13*60 && (minutesCursor + dur) >= 13*60) {
                    timeline.push({ type:'break', label:'Lunch break', start:formatHM(minutesCursor), end:formatHM(minutesCursor+60) });
                    minutesCursor += 60;
                }
                timeline.push({ type:'activity', name:a.name, start:formatHM(minutesCursor), end:formatHM(minutesCursor+dur) });
                minutesCursor += dur + 20; // buffer
            });

            return timeline;
        }
        function renderTimelineSection(day) {
            const t = buildDayTimeline(day);
            if (!t.length) return '';
            const items = t.map(item => {
                if (item.type === 'drive') {
                    return `<div class="selected-activity-item">
                                <div><strong>Drive:</strong> ${item.from} → ${item.to} (${item.km} km)</div>
                                <div><span style="color:rgba(19,53,123,.8);">${item.start}–${item.end}</span></div>
                            </div>`;
                } else if (item.type === 'break') {
                    return `<div class="selected-activity-item" style="border-left-color:#ffc107;">
                                <div><strong>${item.label}</strong></div>
                                <div><span style="color:rgba(19,53,123,.8);">${item.start}–${item.end}</span></div>
                            </div>`;
                } else {
                    return `<div class="selected-activity-item">
                                <div><strong>${item.name}</strong><br><small style="color:#999;">${dayPlanData[day].location}</small></div>
                                <div><span style="color:rgba(19,53,123,.8);">${item.start}–${item.end}</span></div>
                            </div>`;
                }
            }).join('');
            return `<div class="selected-activities" style="border-left-color:rgba(19,53,123,.8);">
                        <h5><i class="fas fa-route me-2"></i> Day ${day} Timeline</h5>
                        ${items}
                    </div>`;
        }

        // Calculate end date automatically based on selected days
        function calculateEndDate() {
            const days = parseInt(document.getElementById('tripDays').value) || 0;
            const startDateInput = document.getElementById('startDate').value;

            if (!startDateInput || days === 0) {
                document.getElementById('endDate').value = '';
                return;
            }

            const startDate = new Date(startDateInput);
            if (isNaN(startDate)) {
                document.getElementById('endDate').value = '';
                return;
            }

            startDate.setDate(startDate.getDate() + (days - 1));

            const yyyy = startDate.getFullYear();
            const mm = String(startDate.getMonth() + 1).padStart(2, '0');
            const dd = String(startDate.getDate()).padStart(2, '0');

            document.getElementById('endDate').value = `${yyyy}-${mm}-${dd}`;
        }

        // Update room options based on passenger count
        function updateRoomOptions() {
            const adults = parseInt(document.getElementById('numAdults').value) || 1;
            const children = parseInt(document.getElementById('numChildren').value) || 0;
            const totalPassengers = adults + children;
            const roomSelect = document.getElementById('roomOptions');

            roomSelect.innerHTML = '<option value="">Select room combination...</option>';

            if (totalPassengers < 1) return;

            const maxDoubleRooms = Math.floor(totalPassengers / 2);
            for (let doubleRooms = 0; doubleRooms <= maxDoubleRooms; doubleRooms++) {
                const remaining = totalPassengers - (doubleRooms * 2);
                const singleRooms = remaining;

                if (singleRooms >= 0) {
                    const parts = [];
                    if (doubleRooms > 0) {
                        parts.push(`${doubleRooms} double room${doubleRooms > 1 ? 's' : ''}`);
                    }
                    if (singleRooms > 0) {
                        parts.push(`${singleRooms} single room${singleRooms > 1 ? 's' : ''}`);
                    }

                    const combination = parts.join(' and ');
                    const option = document.createElement('option');
                    option.value = combination;
                    option.textContent = combination;
                    roomSelect.appendChild(option);
                }
            }
        }

        function initializeDayPlanner() {
            totalDays = parseInt(document.getElementById('tripDays').value) || 0;
            if (totalDays === 0) return;

            document.getElementById('totalDays').textContent = totalDays;
            dayPlanData = {};
            
            for (let i = 1; i <= totalDays; i++) {
                dayPlanData[i] = { location: '', activities: [] };
            }

            currentDay = 1;
            renderDayPlanner();
            updateDaysPlanned();
        }

        function renderDayPlanner() {
            if (totalDays === 0) return;

            let html = `
                <div class="day-header">
                    <div class="day-header-info">
                        <h2>📅 Day ${currentDay} of ${totalDays}</h2>
                        <p>Select location and activities for this day</p>
                    </div>
                    <div class="day-progress ${dayPlanData[currentDay].activities.length > 0 ? 'complete' : ''}">
                        ${dayPlanData[currentDay].activities.length > 0 ? '✓ Planned' : 'Not Planned'}
                    </div>
                </div>

                <div class="location-section">
                    <label for="locationSelect${currentDay}">Choose Location:</label>
                    <select id="locationSelect${currentDay}" class="form-select" onchange="loadDayActivities(${currentDay})">
                        <option value="">Select a location...</option>
            `;

            locations.forEach(location => {
                const selected = dayPlanData[currentDay].location === location ? 'selected' : '';
                html += `<option value="${location}" ${selected}>${location}</option>`;
            });

            html += `</select></div>`;

            // Route feasibility and suggestions for next day
            if (currentDay < totalDays && dayPlanData[currentDay].location) {
                const result = suggestAlternativesForDay(currentDay);
                if (result.impractical) {
                    const evalRes = result.evalRes;
                    const btns = result.suggestions.length
                        ? result.suggestions.map(s => `
                            <button type="button" class="btn btn-sm btn-secondary ms-2" 
                                    onclick="replaceNextDayLocation(${currentDay}, '${s}')">
                                Use ${s}
                            </button>`).join('')
                        : `<span class="ms-2 text-muted">No alternatives within ${NEARBY_RADIUS_KM} km</span>`;
                    html += `
                        <div class="alert alert-warning mt-3" role="alert" style="border-left:4px solid #ffc107; border-radius:12px;">
                            <strong>Long travel:</strong> ${evalRes.locA} → ${evalRes.locB}
                            (~${evalRes.km.toFixed(0)} km, ~${evalRes.hrs.toFixed(1)} hrs).
                            Nearby options for Day ${currentDay + 1}: ${btns}
                        </div>
                    `;
                } else if (dayPlanData[currentDay+1]?.location) {
                    const evalRes = evaluateDayHop(currentDay);
                    if (evalRes) {
                        html += `
                            <div class="alert alert-success mt-3" role="alert" style="border-left:4px solid #28a745; border-radius:12px;">
                                <strong>Feasible:</strong> ${evalRes.locA} → ${evalRes.locB}
                                (~${evalRes.km.toFixed(0)} km, ~${evalRes.hrs.toFixed(1)} hrs)
                            </div>
                        `;
                    }
                }
            }

            // Activities grid
            if (dayPlanData[currentDay].location) {
                const locationActivities = tours.filter(t => t.location === dayPlanData[currentDay].location);
                
                if (locationActivities.length > 0) {
                    html += `<div class="activities-grid">`;
                    locationActivities.forEach(activity => {
                        const isChecked = dayPlanData[currentDay].activities.some(a => String(a.id) === String(activity.id));
                        const desc = (activity.description || '').toString();
                        const short = desc.length > 0 ? (desc.length > 60 ? desc.substring(0, 60) + '...' : desc) : '';
                        html += `
                            <div class="activity-card">
                                <input type="checkbox" value="${activity.id}" 
                                       data-adult="${activity.foreign_adult_usd || 0}"
                                       data-child="${activity.foreign_child_usd || 0}"
                                       data-name="${activity.activity}"
                                       data-location="${activity.location}"
                                       ${isChecked ? 'checked' : ''}
                                       onchange="updateDayActivities(${currentDay})">
                                <div class="activity-info">
                                    <div class="activity-name">${activity.activity}</div>
                                    <div class="activity-description">${short}</div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                } else {
                    html += `<p class="text-muted"><i class="fas fa-exclamation-circle me-2"></i> No activities available for this location.</p>`;
                }
            }

            // Selected activities summary
            html += `<div class="selected-activities">
                <h5><i class="fas fa-check-circle me-2"></i> Selected Activities for Day ${currentDay}</h5>
            `;

            if (dayPlanData[currentDay].activities.length === 0) {
                html += `<div class="no-activities">No activities selected yet</div>`;
            } else {
                dayPlanData[currentDay].activities.forEach(activity => {
                    html += `
                        <div class="selected-activity-item">
                            <div>
                                <strong>${activity.name}</strong><br>
                                <small style="color: #999;">${activity.location}</small>
                            </div>
                        </div>
                    `;
                });
            }

            html += `</div>`;

            // Timeline section removed (not shown to customers)

            // Navigation buttons
            html += `<div class="day-actions">`;
            
            if (currentDay > 1) {
                html += `<button type="button" class="btn btn-secondary" onclick="previousDay()">
                    <i class="fas fa-arrow-left me-2"></i> Previous Day
                </button>`;
            }

            if (currentDay < totalDays) {
                html += `<button type="button" class="btn btn-primary" onclick="nextDay()" ${dayPlanData[currentDay].activities.length === 0 ? 'disabled' : ''}>
                    Next Day <i class="fas fa-arrow-right ms-2"></i>
                </button>`;
            } else {
                html += `<button type="submit" form="travelForm" class="btn btn-primary" ${dayPlanData[currentDay].activities.length === 0 ? 'disabled' : ''}>
                    <i class="fas fa-check me-2"></i> Complete Booking
                </button>`;
            }

            html += `</div>`;

            document.getElementById('dayPlannerContent').innerHTML = html;
        }

        function loadDayActivities(day) {
            const selectElement = document.getElementById(`locationSelect${day}`);
            const location = selectElement.value;
            dayPlanData[day].location = location;
            dayPlanData[day].activities = [];
            renderDayPlanner();
        }

        function updateDayActivities(day) {
            const container = document.getElementById('dayPlannerContent');
            const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
            
            dayPlanData[day].activities = Array.from(checkboxes).map(cb => ({
                id: cb.value,
                name: cb.dataset.name,
                location: cb.dataset.location,
                adult: cb.dataset.adult,
                child: cb.dataset.child
            }));

            updateCostSummary();
            updateDaysPlanned();
            renderDayPlanner();
        }

        function nextDay() {
            if (currentDay < totalDays) {
                currentDay++;
                renderDayPlanner();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function previousDay() {
            if (currentDay > 1) {
                currentDay--;
                renderDayPlanner();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function updateDaysPlanned() {
            let plannedDays = 0;
            for (let i = 1; i <= totalDays; i++) {
                if (dayPlanData[i].activities.length > 0) {
                    plannedDays++;
                }
            }
            document.getElementById('daysPlanned').textContent = plannedDays;

            if (plannedDays === totalDays && totalDays > 0) {
                document.getElementById('completionMessage').classList.add('show');
            } else {
                document.getElementById('completionMessage').classList.remove('show');
            }
        }

        function updateCostSummary() {
            const adults = parseInt(document.getElementById('numAdults').value) || 1;
            const children = parseInt(document.getElementById('numChildren').value) || 0;
            const total = adults + children;

            const totalPeopleEl = document.getElementById('totalPeople');
            if (totalPeopleEl) totalPeopleEl.textContent = total;

            let totalCost = 0;
            for (let day = 1; day <= totalDays; day++) {
                if (dayPlanData[day] && dayPlanData[day].activities) {
                    dayPlanData[day].activities.forEach(activity => {
                        const adultCost = parseFloat(activity.adult) || 0;
                        const childCost = parseFloat(activity.child) || 0;
                        totalCost += (adults * adultCost) + (children * childCost);
                    });
                }
            }

            // Add transport cost
            const transportKm = computeTransportCostKm();
            totalCost += transportKm * TRANSPORT_COST_PER_KM;

            const totalCostEl = document.getElementById('totalCost');
            if (totalCostEl) totalCostEl.textContent = totalCost.toFixed(2);
            
            const costSummaryEl = document.getElementById('costSummary');
            if (costSummaryEl && totalDays > 0) {
                // Keep it hidden as per user request, but script is now safe
                costSummaryEl.style.display = 'none';
            }
        }

        // Initialize on page load
        document.getElementById('numAdults').addEventListener('change', updateCostSummary);
        document.getElementById('numAdults').addEventListener('change', updateRoomOptions);
        document.getElementById('numChildren').addEventListener('change', updateCostSummary);
        document.getElementById('numChildren').addEventListener('change', updateRoomOptions);

        // Initialize display
        updateRoomOptions();

        // ===== SMART PLANNER AI INTEGRATION =====
        /**
         * Check route feasibility with AI and get alternatives
         */
        async function checkRouteWithAI(fromLocation, toLocation) {
            try {
                const response = await fetch('smart_planner_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=suggest_alternative&current_location=${encodeURIComponent(fromLocation)}&next_location=${encodeURIComponent(toLocation)}`
                });
                return await response.json();
            } catch (error) {
                console.error('AI API Error:', error);
                return { error: error.message };
            }
        }

        /**
         * Analyze full itinerary with AI for optimization suggestions
         */
        async function analyzeFullItinerary() {
            try {
                const response = await fetch('smart_planner_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'analyze_itinerary', day_plan_data: dayPlanData })
                });
                return await response.json();
            } catch (error) {
                console.error('AI API Error:', error);
                return { error: error.message };
            }
        }

        // Handle form submission
        document.getElementById('travelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('customerName').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const roomOption = document.getElementById('roomOptions').value;

            if (!name || !email || !startDate || !endDate || !roomOption) {
                alert('❌ Please fill in all required fields (Name, Email, Start Date, End Date, Room Option)');
                return;
            }

            let allPlanned = true;
            for (let i = 1; i <= totalDays; i++) {
                if (dayPlanData[i].activities.length === 0 || !dayPlanData[i].location) {
                    allPlanned = false;
                    break;
                }
            }

            if (!allPlanned) {
                alert('❌ Please plan location and activities for all days before booking!');
                return;
            }

            // Create form data to send to booking summary page
            const formData = new FormData();
            formData.append('trip_days', document.getElementById('tripDays').value);
            formData.append('start_date', startDate);
            formData.append('end_date', endDate);
            formData.append('num_adults', document.getElementById('numAdults').value);
            formData.append('num_children', document.getElementById('numChildren').value);
            formData.append('room_option', roomOption);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('special_request', document.getElementById('specialRequest').value);
            formData.append('day_plan_data', JSON.stringify(dayPlanData));
            formData.append('transport_km_total', computeTransportCostKm().toFixed(1));

            // Post to booking_summary.php for review
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'booking_summary.php';
            
            Object.entries({
                'trip_days': document.getElementById('tripDays').value,
                'start_date': startDate,
                'end_date': endDate,
                'num_adults': document.getElementById('numAdults').value,
                'num_children': document.getElementById('numChildren').value,
                'room_option': roomOption,
                'name': name,
                'email': email,
                'special_request': document.getElementById('specialRequest').value,
                'day_plan_data': JSON.stringify(dayPlanData),
                'transport_km_total': computeTransportCostKm().toFixed(1)
            }).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    </script>

    <script>
    // Spinner (from main.js, isolated to avoid showSlides crash)
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

<?php $conn->close(); ?>
