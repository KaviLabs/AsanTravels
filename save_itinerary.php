<?php
$conn = new mysqli("localhost", "root", "", "asantravels_og");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get POST data
$trip_days = isset($_POST['trip_days']) ? intval($_POST['trip_days']) : 0;
$num_adults = isset($_POST['num_adults']) ? intval($_POST['num_adults']) : 0;
$num_children = isset($_POST['num_children']) ? intval($_POST['num_children']) : 0;
$day_plan_data = isset($_POST['day_plan_data']) ? json_decode($_POST['day_plan_data'], true) : [];
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$special_request = isset($_POST['special_request']) ? $conn->real_escape_string($_POST['special_request']) : '';
$start_date = isset($_POST['start_date']) ? $conn->real_escape_string($_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? $conn->real_escape_string($_POST['end_date']) : '';

// Validate required fields
if (empty($name) || empty($email) || empty($start_date) || empty($end_date)) {
    die(json_encode(['success' => false, 'message' => 'Please fill in all required fields']));
}

// Build package name from selected activities
$package_name = "Custom Package - ";
$activity_names = [];
$total_cost = 0;

foreach ($day_plan_data as $day => $day_info) {
    if (isset($day_info['activities']) && count($day_info['activities']) > 0) {
        foreach ($day_info['activities'] as $activity) {
            $activity_names[] = $activity['name'];
            // Calculate cost for this activity
            $adult_cost = floatval($activity['adult']) * $num_adults;
            $child_cost = floatval($activity['child']) * $num_children;
            $total_cost += $adult_cost + $child_cost;
        }
    }
}

if (empty($activity_names)) {
    die(json_encode(['success' => false, 'message' => 'No activities selected']));
}

$package_name .= implode(", ", array_slice($activity_names, 0, 3));
if (count($activity_names) > 3) {
    $package_name .= " and " . (count($activity_names) - 3) . " more";
}

$total_passengers = $num_adults + $num_children;
$room_option = "To be determined";
$base_price = $total_cost;
$extras = 0;
$pay_on_arrival = round($total_cost / 2, 2);

// Insert into database
$query = "INSERT INTO booking (Package, start_date, end_date, passengers, room_option, optional_tours, base_price, extras, total, pay_on_arrival, name, email, special_request, status)
          VALUES ('$package_name', '$start_date', '$end_date', $total_passengers, '$room_option', '" . json_encode($day_plan_data) . "', $base_price, $extras, $total_cost, $pay_on_arrival, '$name', '$email', '$special_request', 'Pending')";

if ($conn->query($query) === TRUE) {
    $booking_id = $conn->insert_id;
    die(json_encode([
        'success' => true, 
        'message' => 'Booking saved successfully!',
        'booking_id' => $booking_id,
        'total_cost' => number_format($total_cost, 2)
    ]));
} else {
    die(json_encode(['success' => false, 'message' => 'Error saving booking: ' . $conn->error]));
}

$conn->close();
?>
