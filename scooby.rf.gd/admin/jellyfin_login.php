<?php

function getAuthKey($serverUrl, $username, $password, $passwordSha1) {
    $url = $serverUrl . '/Users/AuthenticateByName';
    
    // Build the JSON payload
    $authData = json_encode([
        'Username' => $username,
        'Password' => $passwordSha1, // or use $password if required
        'Pw' => $password
    ]);
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $authData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Authorization: Emby UserId="' . $username . '", Client="media_cleaner", Device="media_cleaner", DeviceId="media_cleaner", Version="0.2", Token=""'
    ]);

    // Execute the request and get the response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error: $error\n";
        return null;
    }

    if ($httpCode !== 200) {
        echo "HTTP Code: $httpCode\nResponse: $response\n";
        return null;
    }

    // Decode the JSON response
    $data = json_decode($response, true);
    return isset($data['AccessToken']) ? $data['AccessToken'] : null;
}

// Example usage
$serverUrl = 'http://stv1.xyz:53570';
$username = 'Ankit';
$password = 'Ankit@3119';
$passwordSha1 = '8dd84bdbf504f52bdcccb08b1dd456d1fdf000c3';

$authKey = getAuthKey($serverUrl, $username, $password, $passwordSha1);
if ($authKey) {
    echo "Access Token: $authKey\n";
} else {
    echo "Failed to authenticate user\n";
}
?>
