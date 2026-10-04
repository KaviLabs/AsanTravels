<?php
// ── debug_booking_notify.php ──────────────────────────────────────────────
// Simulates exactly what happens when a booking is placed
// Visit: http://your-site/debug_booking_notify.php
// DELETE THIS FILE AFTER TESTING

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/whatsapp_helper.php';

$results = [];

// 1. Check if cURL is available on THIS server
$results['curl_available'] = function_exists('curl_init') ? '✅ YES' : '❌ NO - cURL is disabled on this server!';

// 2. Check provider setting
$results['provider'] = WHATSAPP_PROVIDER;

// 3. Check admin numbers
$results['admin_numbers'] = ADMIN_WHATSAPP_NUMBERS;

// 4. Check Green API instance status
if (function_exists('curl_init')) {
    $ch = curl_init("https://api.green-api.com/waInstance" . GREEN_API_INSTANCE_ID . "/getStateInstance/" . GREEN_API_TOKEN);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    $results['green_api_status'] = $err ? "❌ cURL Error: $err" : $resp;
} else {
    $results['green_api_status'] = '❌ cURL not available, cannot test';
}

// 5. Try sending a LIVE test message to admin number
if (function_exists('curl_init')) {
    $testMsg = "🧪 *LIVE TEST from AsanTravels server*\nIf you receive this, WhatsApp notifications are working!\nTime: " . date('d M Y H:i:s');
    $sendResult = sendWhatsAppApiMessage(ADMIN_WHATSAPP_NUMBER, $testMsg);
    $results['test_send_result'] = $sendResult;
} else {
    $results['test_send_result'] = ['success' => false, 'message' => 'cURL not available'];
}

// 6. Check DB connection to see last booking
$conn = new mysqli("sql206.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og");
if (!$conn->connect_error) {
    $row = $conn->query("SELECT id, name, email, phone, status, booking_date FROM booking ORDER BY id DESC LIMIT 1")->fetch_assoc();
    $results['last_booking'] = $row ?: 'No bookings found';
    $conn->close();
} else {
    $results['last_booking'] = 'DB Error: ' . $conn->connect_error;
}

header('Content-Type: text/plain');
echo "=== AsanTravels WhatsApp Debug Report ===\n\n";
foreach ($results as $key => $val) {
    echo strtoupper($key) . ":\n";
    echo (is_array($val) ? print_r($val, true) : $val) . "\n\n";
}
