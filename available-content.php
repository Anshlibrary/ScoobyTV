<?php
// Jellyfin server URL
$server_url = "http://stv1.xyz:53570";
$username = "JellyfinApiTest";
$password = "Scoobytv@310";

// Prepare the payload as a JSON string
$auth_payload = json_encode([
    "Username" => $username,
    "Pw" => $password,
    "App" => "ScoobyTV" // Optional, replace with your app name if required by Jellyfin
]);

// HTTP context options for POST request
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                    "Content-Length: " . strlen($auth_payload) . "\r\n",
        'content' => $auth_payload,
        'ignore_errors' => true // To capture error responses
    ]
];

// Create the context for the HTTP request
$context = stream_context_create($options);

// Perform the HTTP request
$response = file_get_contents("$server_url/Users/AuthenticateByName", false, $context);

// Check for errors in the request
if ($response === false) {
    die("Error: Unable to connect to the Jellyfin server.");
}

// Decode the response
$response_data = json_decode($response, true);

// Display the raw response (for debugging purposes)
echo "<pre>Auth Response: " . htmlspecialchars($response) . "</pre>";

// Check if the response contains an access token
if (!isset($response_data['AccessToken'])) {
    die("Authentication failed! Check your username/password or server configuration.");
}

// Extract the access token
$access_token = $response_data['AccessToken'];

// Output the access token
echo "<p>Access Token: $access_token</p>";
?>
