<?php
// Redirect extensionless folder URL directly to the PHP page with query string preserved.
$qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: /custom_activities.php' . $qs, true, 302);
exit;
