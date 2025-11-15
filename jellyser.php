<?php
// Jellyseerr base URL
$jellyseerrUrl = "http://stv1.xyz:53575"; // Replace with your Jellyseerr URL

// User credentials
$username = "Sonu2"; // Replace with your Jellyseerr username
$password = "Sonu@310"; // Replace with your Jellyseerr password

// Initialize cURL session
$ch = curl_init();

// API login endpoint
$loginEndpoint = $jellyseerrUrl . "/api/v1/auth/jellyfin";

// Prepare POST data
$postData = json_encode([
    'username' => $username,
    'password' => $password
]);

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $loginEndpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Output the response from the server
    echo "Response: " . $response;

    // If response contains auth token or session details, set the cookie manually
    // Example: Set a cookie with domain set to .stv1.xyz
    if (preg_match('/"jellyfinAuthToken":"([^"]+)"/', $response, $matches)) {
        $authToken = $matches[1]; // Extract token from response

        // Set a cookie with domain stv1.xyz
        setcookie("jellyfinAuthToken", $authToken, time() + 3600, "/", "stv1.xyz", false, true);  // 1 hour expiry

        echo "Cookie set for domain .stv1.xyz";
    }
}

// Close cURL session
curl_close($ch);
?>
