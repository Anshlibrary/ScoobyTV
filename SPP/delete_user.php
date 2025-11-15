<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function deleteUserInJellyfin($serverUrl, $apiKey, $jellyfinUserId) {
    $url = $serverUrl . "Users/$jellyfinUserId";
    
    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: $apiKey"
            ],
            'method' => 'DELETE',
        ],
    ];
    $context = stream_context_create($options);
    
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        $error = error_get_last();
        echo "<strong>Error deleting user:</strong> " . $error['message'] . "<br>";
        echo "<strong>Request URL:</strong> $url<br>";
        return ["success" => false, "message" => $error['message']];
    }

    echo "<strong>User successfully deleted:</strong> $jellyfinUserId<br>";
    return ["success" => true, "message" => "User successfully deleted"];
}

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
        $error = error_get_last();
        echo "<strong>Error fetching user:</strong> " . $error['message'] . "<br>";
        echo "<strong>Request URL:</strong> $url<br>";
        return null;
    }

    $users = json_decode($response, true);
    foreach ($users as $user) {
        if (strcasecmp($user['Name'], $username) == 0) {
            echo "<strong>User Found:</strong> " . $user['Id'] . "<br>";
            return $user['Id'];
        }
    }
    echo "<strong>No matching user found for:</strong> $username<br>";
    return null;
}

$serverUrl = 'http://stv1.xyz:53570/';
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2';

// Example user input
$username = "JellyfinApiTest";

if (empty($username)) {
    echo "Username cannot be empty.<br>";
    exit();
}

$jellyfinUserId = getUserIdByUsername($serverUrl, $apiKey, $username);

if ($jellyfinUserId === null) {
    echo "User not found in Jellyfin.<br>";
    exit();
}

$result = deleteUserInJellyfin($serverUrl, $apiKey, $jellyfinUserId);

if ($result["success"]) {
    echo $result["message"];
} else {
    echo "Failed to delete user: " . $result["message"];
}
?>
