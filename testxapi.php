<?php
// Replace with your Jellyfin server URL
$jellyfin_url = 'http://stv1.xyz:53575/';

// User credentials
$username = 'Sonu2';
$password = 'Sonu@310';

// API endpoint
$auth_url = $jellyfin_url . 'api/v1/auth/jellyfin';

// Prepare request data
$data = json_encode([
    'username' => $username,
    'password' => $password
]);

// Path to save cookies (adjust the location as needed)
$cookie_file = '/tmp/cookies.txt';  // Make sure web server has write access to this path

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $auth_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

// Set options to handle cookies
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);  // Save cookies to file
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);  // Read cookies from file

// Disable SSL verification for testing (useful if you're in a dev environment without HTTPS)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Get the response headers to check if cookies are set
$response_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
echo "Response Headers: <pre>" . htmlspecialchars($response_headers) . "</pre>";

// Handle errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    echo "HTTP Status Code: $http_code<br>";
    echo "Raw Response: <pre>" . htmlspecialchars($response) . "</pre>";

    $response_data = json_decode($response, true);
    if ($http_code === 200 && isset($response_data['AccessToken'])) {
        echo "Login successful! Access Token: " . htmlspecialchars($response_data['AccessToken']) . "<br>";
    } else {
        echo "Login failed!<br>";
        echo "Error Details: <pre>" . htmlspecialchars(print_r($response_data, true)) . "</pre>";
    }

    // Display cookies after request
    echo "<br>Cookies saved in $cookie_file:<br>";
    echo "<pre>" . htmlspecialchars(file_get_contents($cookie_file)) . "</pre>";
}

curl_close($ch);
?>
