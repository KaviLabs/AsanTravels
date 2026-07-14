<?php
// index.php
// Fallback router if mod_rewrite is disabled or bypassed.

$host = isset($_SERVER['HTTP_HOST']) ? strtolower(trim($_SERVER['HTTP_HOST'])) : '';
$host_no_port = preg_replace('/:\d+$/', '', $host);

$admin_domain = 'asanadmin.xo.je';

if ($host_no_port === $admin_domain || $host_no_port === 'www.' . $admin_domain) {
    header("Location: /asn_admin_loging.php");
    exit();
} else {
    // Default to serving/redirecting to the main website
    header("Location: /index1.php");
    exit();
}
