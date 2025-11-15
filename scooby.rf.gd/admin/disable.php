<?php
function getUserIdByUsername($serverUrl, $apiKey, $username) {
    $url = $serverUrl . "Users?searchTerm=" . urlencode($username);

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: $apiKey"
            ],
            'method' => 'GET',
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        echo "Error: Unable to fetch user information.\n";
        return null;
    }

    $users = json_decode($response, true);
    foreach ($users as $user) {
        if (strcasecmp($user['Name'], $username) == 0) {
            return $user['Id'];
        }
    }

    echo "User $username not found.\n";
    return null;
}

function disableUserByUrl($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Policy";
    $data = json_encode([
        "IsDisabled" => true, 
        "PasswordResetProviderId" => "default", 
        "AuthenticationProviderId" => "default"
    ]);

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: $apiKey"
            ],
            'method' => 'POST',
            'content' => $data,
        ],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        echo "Error: Unable to disable user.\n";
    } else {
        echo "User with ID $userId has been disabled.\n";
        echo $result;  // For debugging purposes
    }
}

function disableUserByUsername($serverUrl, $apiKey, $username) {
    $userId = getUserIdByUsername($serverUrl, $apiKey, $username);
    if ($userId) {
        disableUserByUrl($serverUrl, $apiKey, $userId);
    }
}

// Example usage:
$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$username = 'DisTest'; // Replace with the actual username

disableUserByUsername($serverUrl, $apiKey, $username);


?>




