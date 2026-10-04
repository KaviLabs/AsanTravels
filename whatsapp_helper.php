<?php
/**
 * ─────────────────────────────────────────────────────────────────────────────
 *  AsanTravels – WhatsApp Helper Module
 *  Handles formatting, message template generation, direct wa.me link generation,
 *  and automated API notifications (Twilio, UltraMsg, Meta WhatsApp Cloud API).
 * ─────────────────────────────────────────────────────────────────────────────
 */

if (!file_exists(__DIR__ . '/config.php')) {
    // Fallback constants if config.php is missing
} else {
    require_once __DIR__ . '/config.php';
}

if (!defined('WHATSAPP_PROVIDER'))       define('WHATSAPP_PROVIDER', 'none');
if (!defined('ADMIN_WHATSAPP_NUMBERS'))  define('ADMIN_WHATSAPP_NUMBERS', ['+94762087707', '+94713378264']);
if (!defined('ADMIN_WHATSAPP_NUMBER'))   define('ADMIN_WHATSAPP_NUMBER', '+94762087707');
if (!defined('ULTRAMSG_INSTANCE_ID'))   define('ULTRAMSG_INSTANCE_ID', '');
if (!defined('ULTRAMSG_TOKEN'))         define('ULTRAMSG_TOKEN', '');
if (!defined('TWILIO_ACCOUNT_SID'))     define('TWILIO_ACCOUNT_SID', '');
if (!defined('TWILIO_AUTH_TOKEN'))      define('TWILIO_AUTH_TOKEN', '');
if (!defined('TWILIO_WHATSAPP_NUMBER')) define('TWILIO_WHATSAPP_NUMBER', '');
if (!defined('META_WHATSAPP_PHONE_ID'))     define('META_WHATSAPP_PHONE_ID', '');
if (!defined('META_WHATSAPP_ACCESS_TOKEN'))define('META_WHATSAPP_ACCESS_TOKEN', '');

/**
 * Format a phone number into international standard (E.164 without spaces/dashes)
 * e.g., "076 208 7707" -> "+94762087707"
 */
function formatWhatsAppNumber($phone, $defaultCountryCode = '94') {
    if (empty($phone)) return '';
    // Strip everything except digits and plus sign
    $cleaned = preg_replace('/[^\d+]/', '', trim($phone));
    if (empty($cleaned)) return '';

    // If starts with +, return cleaned
    if (str_starts_with($cleaned, '+')) {
        return $cleaned;
    }

    // If starts with local 0 (e.g. 0762087707), replace leading 0 with country code
    if (str_starts_with($cleaned, '0')) {
        return '+' . $defaultCountryCode . substr($cleaned, 1);
    }

    return '+' . $cleaned;
}

/**
 * Build a structured WhatsApp notification message for booking update
 */
function buildBookingUpdateMessage($booking) {
    $id             = str_pad($booking['id'] ?? 0, 4, '0', STR_PAD_LEFT);
    $name           = $booking['name']           ?? 'Valued Guest';
    $package        = $booking['package_name']   ?? $booking['Package'] ?? 'Custom Sri Lanka Tour';
    $startDate      = !empty($booking['start_date']) ? date('d M Y', strtotime($booking['start_date'])) : 'TBD';
    $endDate        = !empty($booking['end_date'])   ? date('d M Y', strtotime($booking['end_date']))   : 'TBD';
    $adults         = intval($booking['num_adults']   ?? $booking['passengers'] ?? 1);
    $children       = intval($booking['num_children'] ?? 0);
    $status         = $booking['status']         ?? 'Pending';
    $total          = number_format(floatval($booking['total'] ?? 0), 2);
    $payOnArrival   = number_format(floatval($booking['pay_on_arrival'] ?? 0), 2);
    $specialRequest = !empty($booking['special_request']) ? $booking['special_request'] : 'None';

    $statusEmoji = match($status) {
        'Confirmed'   => '✅ *CONFIRMED*',
        'In Progress' => '⏳ *IN PROGRESS*',
        'Completed'   => '🎉 *COMPLETED*',
        'Canceled'    => '❌ *CANCELED*',
        default       => '📌 *PENDING*'
    };

    $message = "🌴 *ASANTRAVELS BOOKING UPDATE* 🌴\n\n";
    $message .= "Hello *$name*,\n";
    $message .= "Your booking details have been updated in our system:\n\n";
    $message .= "🆔 *Booking Ref:* #$id\n";
    $message .= "📦 *Package:* $package\n";
    $message .= "📅 *Dates:* $startDate to $endDate\n";
    $message .= "👥 *Guests:* $adults Adult(s)" . ($children > 0 ? ", $children Child(ren)" : "") . "\n";
    $message .= "📊 *Status:* $statusEmoji\n";
    $message .= "💰 *Total Amount:* $$total USD\n";
    $message .= "💳 *Pay on Arrival:* $$payOnArrival USD\n";
    if ($specialRequest !== 'None') {
        $message .= "📝 *Notes:* $specialRequest\n";
    }
    $message .= "\nIf you have any questions or require modifications, please feel free to reach out to us.\n\n";
    $message .= "Best regards,\n";
    $message .= "*AsanTravels Team*\n";
    $message .= "📞 +94 76 208 7707 | 🌐 asantravels.com";

    return $message;
}

/**
 * Generate a 1-Click WhatsApp Direct Chat link (wa.me)
 */
function generateWhatsAppClickLink($phone, $message) {
    $formattedPhone = ltrim(formatWhatsAppNumber($phone), '+');
    if (empty($formattedPhone)) return '#';
    return "https://wa.me/{$formattedPhone}?text=" . rawurlencode($message);
}

/**
 * Send WhatsApp Message via API (Twilio, UltraMsg, or WhatsApp Cloud API)
 */
function sendWhatsAppApiMessage($toPhone, $messageText) {
    $formattedTo = formatWhatsAppNumber($toPhone);
    $provider    = strtolower(trim(WHATSAPP_PROVIDER));

    if (empty($formattedTo) || $provider === 'none') {
        return ['success' => false, 'message' => 'WhatsApp API provider disabled or empty recipient number.'];
    }

    // 1. UltraMsg API Integration
    if ($provider === 'ultramsg') {
        $instanceId = ULTRAMSG_INSTANCE_ID;
        $token      = ULTRAMSG_TOKEN;
        if (empty($instanceId) || empty($token)) {
            return ['success' => false, 'message' => 'UltraMsg credentials not configured in config.php.'];
        }

        $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";
        $data = [
            'token' => $token,
            'to'    => $formattedTo,
            'body'  => $messageText
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "UltraMsg cURL Error: $err"];
        }
        $resData = json_decode($response, true);
        if (isset($resData['sent']) && ($resData['sent'] === 'true' || $resData['sent'] === true)) {
            return ['success' => true, 'message' => 'UltraMsg WhatsApp message sent successfully!'];
        }
        return ['success' => false, 'message' => 'UltraMsg API Response: ' . ($response ?: 'No response')];
    }

    // 2. Twilio API Integration
    if ($provider === 'twilio') {
        $sid   = TWILIO_ACCOUNT_SID;
        $token = TWILIO_AUTH_TOKEN;
        $from  = TWILIO_WHATSAPP_NUMBER; // e.g. "whatsapp:+14155238886"

        if (empty($sid) || empty($token) || empty($from)) {
            return ['success' => false, 'message' => 'Twilio credentials not configured in config.php.'];
        }

        $recipient = str_starts_with($formattedTo, 'whatsapp:') ? $formattedTo : 'whatsapp:' . $formattedTo;
        $url       = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'From' => $from,
            'To'   => $recipient,
            'Body' => $messageText
        ]));
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Twilio cURL Error: $err"];
        }
        $resData = json_decode($response, true);
        if (isset($resData['sid'])) {
            return ['success' => true, 'message' => 'Twilio WhatsApp message queued! SID: ' . $resData['sid']];
        }
        return ['success' => false, 'message' => 'Twilio Error: ' . ($resData['message'] ?? $response)];
    }

    // 3. Meta WhatsApp Cloud API Integration
    if ($provider === 'whatsapp_cloud') {
        $phoneId     = META_WHATSAPP_PHONE_ID;
        $accessToken = META_WHATSAPP_ACCESS_TOKEN;

        if (empty($phoneId) || empty($accessToken)) {
            return ['success' => false, 'message' => 'Meta WhatsApp Cloud API credentials not configured in config.php.'];
        }

        $cleanPhone = ltrim($formattedTo, '+');
        $url        = "https://graph.facebook.com/v18.0/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $cleanPhone,
            'type'              => 'text',
            'text'              => ['body' => $messageText]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'message' => "Meta API cURL Error: $err"];
        }
        $resData = json_decode($response, true);
        if (isset($resData['messages'][0]['id'])) {
            return ['success' => true, 'message' => 'WhatsApp Cloud API message sent! ID: ' . $resData['messages'][0]['id']];
        }
        return ['success' => false, 'message' => 'Meta API Error: ' . ($response ?: 'Unknown error')];
    }

    return ['success' => false, 'message' => 'Unknown provider configured: ' . $provider];
}

/**
 * Triggered on Booking Update:
 * Fetches booking data, formats message, sends via API if enabled, and returns wa.me link.
 */
function notifyBookingUpdate($bookingId, $conn, $notifyAdmin = true) {
    $stmt = $conn->prepare("SELECT * FROM booking WHERE id = ?");
    if (!$stmt) return ['success' => false, 'message' => 'Database error: ' . $conn->error];
    
    $stmt->bind_param('i', $bookingId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        return ['success' => false, 'message' => "Booking #$bookingId not found."];
    }

    $messageText  = buildBookingUpdateMessage($booking);
    $customerPhone= $booking['phone'] ?? '';
    $customerLink = generateWhatsAppClickLink($customerPhone, $messageText);
    $adminLink    = generateWhatsAppClickLink(ADMIN_WHATSAPP_NUMBER, $messageText);

    $apiResult = ['success' => false, 'message' => 'No API auto-sending attempted.'];

    // Send via API if recipient phone is set and provider enabled
    if (!empty($customerPhone) && WHATSAPP_PROVIDER !== 'none') {
        $apiResult = sendWhatsAppApiMessage($customerPhone, $messageText);
    }

    // Send alert to all admin WhatsApp numbers via API as well
    if ($notifyAdmin && WHATSAPP_PROVIDER !== 'none') {
        $adminNumbers = is_array(ADMIN_WHATSAPP_NUMBERS) ? ADMIN_WHATSAPP_NUMBERS : [ADMIN_WHATSAPP_NUMBER];
        foreach ($adminNumbers as $adminNum) {
            if (!empty($adminNum)) {
                sendWhatsAppApiMessage($adminNum, "🚨 *ADMIN NOTIFICATION*\nBooking #$bookingId was updated in DB!\n\n" . $messageText);
            }
        }
    }

    return [
        'success'       => $apiResult['success'],
        'api_response'  => $apiResult['message'],
        'message_text'  => $messageText,
        'customer_phone'=> $customerPhone,
        'customer_link' => $customerLink,
        'admin_link'    => $adminLink
    ];
}
