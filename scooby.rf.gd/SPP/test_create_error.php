<?php
$url = "http://stv1.xyz:53570/Users";
$authToken = "6ca9deb34c874712bbc8ea219dcec6e2"; // Use a valid token

// User data to be sent in the request
$userData = [
    "Name" => "NewUserTesting",
    "Password" => "password123",
    "IsAdministrator" => false,
    "EnableAllDevices" => true,
    // Add other user parameters as required
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "X-Emby-Token: $authToken",
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if the request was successful
if ($httpCode == 201) {
    echo "User created successfully: " . $response;
} else {
    echo "Failed to create user. HTTP Code: $httpCode, Response: " . $response;
}
?>
