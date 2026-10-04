<?php
// ── debug_booking_notify.php ──────────────────────────────────────────────
// DELETE THIS FILE AFTER TESTING
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/plain');

echo "=== AsanTravels WhatsApp Debug Report ===\n\n";

// 1. config.php
$configPath = __DIR__ . '/config.php';
if (file_exists($configPath)) {
    echo "1. config.php: YES - EXISTS on this server\n";
    require_once $configPath;
} else {
    echo "1. config.php: MISSING! This is why WhatsApp is not working.\n";
    echo "   You must manually upload config.php to the live server via FTP.\n\n";
}

// 2. cURL
echo "2. cURL extension: " . (function_exists('curl_init') ? "YES - Available\n" : "NO - NOT available - server blocks outgoing API calls\n");

// 3. Provider
echo "3. WhatsApp Provider: " . (defined('WHATSAPP_PROVIDER') ? WHATSAPP_PROVIDER : "NOT DEFINED (config.php missing)") . "\n";

// 4. Green API Instance
echo "4. GREEN_API_INSTANCE_ID: " . (defined('GREEN_API_INSTANCE_ID') ? (empty(GREEN_API_INSTANCE_ID) ? "EMPTY!" : "Set (" . substr(GREEN_API_INSTANCE_ID,0,4) . "...)") : "NOT DEFINED") . "\n";
echo "5. GREEN_API_TOKEN: "       . (defined('GREEN_API_TOKEN')       ? (empty(GREEN_API_TOKEN)       ? "EMPTY!" : "Set")                                                                            : "NOT DEFINED") . "\n";

// 5. Test Green API connection
if (function_exists('curl_init') && defined('GREEN_API_INSTANCE_ID') && !empty(GREEN_API_INSTANCE_ID)) {
    $ch = curl_init("https://api.green-api.com/waInstance" . GREEN_API_INSTANCE_ID . "/getStateInstance/" . GREEN_API_TOKEN);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "6. Green API State: " . ($err ? "Error: $err" : $resp) . "\n";

    // 6. Send test message
    if (defined('ADMIN_WHATSAPP_NUMBER')) {
        require_once __DIR__ . '/whatsapp_helper.php';
        $r = sendWhatsAppApiMessage(ADMIN_WHATSAPP_NUMBER, "Test from asantravels.lk - " . date('H:i:s'));
        echo "7. Test Send to " . ADMIN_WHATSAPP_NUMBER . ": " . ($r['success'] ? "SENT!" : "Failed: " . $r['message']) . "\n";
    }
} else {
    echo "6. Green API Test: SKIPPED (cURL or config missing)\n";
}

// 7. Last booking in DB
echo "\n--- Last Booking in Database ---\n";
try {
    $conn = @new mysqli("sql206.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og");
    if ($conn->connect_error) {
        echo "DB: ERROR - " . $conn->connect_error . "\n";
    } else {
        $r = $conn->query("SELECT id, name, phone, status, booking_date FROM booking ORDER BY id DESC LIMIT 1");
        $row = $r ? $r->fetch_assoc() : null;
        echo $row ? print_r($row, true) : "No bookings yet\n";
        $conn->close();
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
