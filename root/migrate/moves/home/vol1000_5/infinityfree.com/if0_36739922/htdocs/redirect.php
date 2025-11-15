<?php 
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// header("Access-Control-Allow-Origin: *");
// // Allow the following methods from cross-origin requests
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// // Allow the following headers from cross-origin requests
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
// // Allow cookies to be included in cross-origin requests
// header("Access-Control-Allow-Credentials: true");
$user = $_GET['user'];
if ($user == 'services') {
    header("Location: services.html");
    exit();
    
}
if ($user == 'terms') {
    header("Location: terms.html");
    exit();
}
if ($user == 'contact') {
    header("Location: support.php");
    exit();
}
if ($user == 'profile') {
    header("Location: profile.php");
    exit();
}
if ($user == 'trial') {
    header("Location: trial.php");
    exit();
}
if ($user == 'apply') {
    header("Location: SPP/apply.php");
    exit();
}
if ($user == 'pay') {
    header("Location: payment_v3.php");
    exit();
}
if ($user == 'meet') {
    header("Location: https://meet.google.com/zuu-kduk-rjo");
    exit();
}
if ($user == 'jellyfin') {
    header("Location: http://stv1.xyz:53570");
    exit();
}
if ($user == 'RequestContent') {
    header("Location: http://stv1.xyz:53575");
    exit();
}
if ($user == 'assoc') {
    header("Location: https://scoobytv.com/SPP/login.php");
    exit();
}
else {
    // Show a "not found" message if the user parameter doesn't match any case
    echo "<h2>Page not found</h2>";
    exit();
}

?>