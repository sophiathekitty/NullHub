<?php
// Get the host IP address
$hostIpAddress = $_SERVER['SERVER_ADDR'];

// Redirect to the /index.html page
header("Location: http://$hostIpAddress/about.html");
exit;
?>
