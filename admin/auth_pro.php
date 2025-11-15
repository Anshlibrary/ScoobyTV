<?php
// Replace with your Jellyfin server URL and API key
$jellyfinUrl = 'http://stv1.xyz:53570';
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2';

// Set the request URL
$requestUrl = "$jellyfinUrl/Auth/Providers";

// Set the HTTP headers
$options = [
    'http' => [
        'header' => "X-Emby-Authorization: MediaBrowser Token=$apiKey\r\n"
    ]
];

// Create the context for the request
$context = stream_context_create($options);

// Make the request and get the response
$response = file_get_contents($requestUrl, false, $context);

// Check for errors
if ($response === FALSE) {
    die('Error occurred while fetching authentication providers.');
}

// Decode the JSON response
$authProviders = json_decode($response, true);

// Print the authentication providers
print_r($authProviders);
?>
