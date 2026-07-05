<?php
// ── save_itinerary.php ─────────────────────────────────────────────────────
// Saves a custom itinerary booking to the database.
// Called via AJAX (fetch) from custom booking summary.php
// Returns JSON response.

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "asantravels_og");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// ── Ensure booking table has the right columns ────────────────────────────
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS package_name VARCHAR(500) DEFAULT ''");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS num_adults INT DEFAULT 0");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS num_children INT DEFAULT 0");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

// ── Collect POST data ─────────────────────────────────────────────────────
$trip_days      = isset($_POST['trip_days'])      ? intval($_POST['trip_days'])      : 0;
$num_adults     = isset($_POST['num_adults'])     ? intval($_POST['num_adults'])      : 0;
$num_children   = isset($_POST['num_children'])   ? intval($_POST['num_children'])   : 0;
$start_date     = isset($_POST['start_date'])     ? trim($_POST['start_date'])        : '';
$end_date       = isset($_POST['end_date'])       ? trim($_POST['end_date'])          : '';
$room_option    = isset($_POST['room_option'])    ? trim($_POST['room_option'])       : '';
$name           = isset($_POST['name'])           ? trim($_POST['name'])              : '';
$email          = isset($_POST['email'])          ? trim($_POST['email'])             : '';
$special_request= isset($_POST['special_request'])? trim($_POST['special_request'])  : '';
$package_name   = isset($_POST['package_name'])  ? trim($_POST['package_name'])      : '';
$day_plan_json  = isset($_POST['day_plan_data'])  ? $_POST['day_plan_data']           : '{}';
$transport_km   = isset($_POST['transport_km_total']) ? floatval($_POST['transport_km_total']) : 0;

// ── Validate required fields ──────────────────────────────────────────────
if (empty($name) || empty($email) || empty($start_date) || empty($end_date)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (name, email, dates).']);
    exit;
}

// ── Parse day plan & compute totals ──────────────────────────────────────
$day_plan_data = json_decode($day_plan_json, true) ?: [];
$activity_names = [];
$total_cost = 0;

foreach ($day_plan_data as $day => $day_info) {
    if (!empty($day_info['activities']) && is_array($day_info['activities'])) {
        foreach ($day_info['activities'] as $activity) {
            $activity_names[] = $activity['name'] ?? '';
            $adult_price  = floatval($activity['adult']  ?? 0);
            $child_price  = floatval($activity['child']  ?? 0);
            $total_cost  += ($adult_price * $num_adults) + ($child_price * $num_children);
        }
    }
}

$transport_cost_per_km = 0.6;
$transport_cost = $transport_km * $transport_cost_per_km;
$total_cost    += $transport_cost;
$pay_on_arrival = round($total_cost / 2, 2);

if (empty($activity_names)) {
    echo json_encode(['success' => false, 'message' => 'No activities selected. Please plan at least one day.']);
    exit;
}

// Build package name from locations if not provided
if (empty($package_name)) {
    $locations = [];
    foreach ($day_plan_data as $plan) {
        if (!empty($plan['location']) && !in_array($plan['location'], $locations)) {
            $locations[] = $plan['location'];
        }
    }
    $package_name = implode(' & ', $locations) ?: 'Custom Sri Lanka Tour';
}

// Build activity summary for optional_tours column (first 3 + count)
$acts_display = array_slice($activity_names, 0, 3);
$acts_str = implode(', ', $acts_display);
if (count($activity_names) > 3) {
    $acts_str .= ' and ' . (count($activity_names) - 3) . ' more';
}

$total_passengers = $num_adults + $num_children;
$extras = 0;

// ── Insert into database ──────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO booking 
        (Package, package_name, start_date, end_date, passengers, num_adults, num_children,
         room_option, optional_tours, base_price, extras, total, pay_on_arrival,
         name, email, special_request, status, booking_date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);
    exit;
}

$base_price = $total_cost; // activity + transport cost
$day_plan_json_escaped = $day_plan_json; // store full JSON

$stmt->bind_param(
    'ssssiiissddddsss',
    $package_name,        // Package (short)
    $package_name,        // package_name (full)
    $start_date,
    $end_date,
    $total_passengers,
    $num_adults,
    $num_children,
    $room_option,
    $day_plan_json_escaped,  // optional_tours = full JSON
    $base_price,
    $extras,
    $total_cost,
    $pay_on_arrival,
    $name,
    $email,
    $special_request
);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    $stmt->close();
    $conn->close();
    echo json_encode([
        'success'    => true,
        'message'    => 'Booking saved successfully!',
        'booking_id' => $booking_id,
        'total_cost' => number_format($total_cost, 2)
    ]);
} else {
    $err = $stmt->error;
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Error saving booking: ' . $err]);
}
