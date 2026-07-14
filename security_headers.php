<?php
// security_headers.php
// Enforce strict host-level isolation between the admin panel and the public website.

// Clean the HTTP host to prevent host header manipulation tricks
$host = isset($_SERVER['HTTP_HOST']) ? strtolower(trim($_SERVER['HTTP_HOST'])) : '';
$host_no_port = preg_replace('/:\d+$/', '', $host);

$admin_domain = 'asanadmin.xo.je';
$website_domain = 'asantravels.lk';

$current_script = basename($_SERVER['SCRIPT_NAME']);

// List of admin-only scripts
$admin_scripts = [
    'admin.php',
    'admin_bookings.php',
    'asn_admin_loging.php',
    'asn_custom_admin.php',
    'asn_Bookings.php',
    'asn_Contact.php',
    'asn_Gallery.php',
    'asn_Reviews.php',
    'asn_subscribers.php',
    'asn_galary.php',
    'logout.php'
];

$is_admin_script = in_array($current_script, $admin_scripts);
$is_accessing_via_admin_domain = ($host_no_port === $admin_domain || $host_no_port === 'www.' . $admin_domain);

if ($is_accessing_via_admin_domain) {
    // If accessing via the admin domain, they are NOT allowed to browse the main website pages.
    if (!$is_admin_script) {
        header("Location: /asn_admin_loging.php");
        exit();
    }
} else {
    // If NOT using the admin domain (e.g. using asantravels.lk), they are NOT allowed to access admin scripts.
    if ($is_admin_script) {
        http_response_code(403);
        echo "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body style='font-family:sans-serif; text-align:center; padding-top:50px;'><h1>403 Forbidden</h1><p>Access denied. The admin panel must be accessed via its dedicated domain.</p></body></html>";
        exit();
    }
}
