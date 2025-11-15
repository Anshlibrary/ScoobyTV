 <?php   
/*Jellyfin create user*/
function createUser($serverUrl, $apiKey, $jellyuser, $jellypass) {
    $url = $serverUrl . 'Users/New';
    $data = json_encode([
        'Name' => $jellyuser,
        'Password' => $jellypass
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
        echo "HTTP Code: $httpCode\nResponse: $response\n";
    }

    return json_decode($response, true);
}

function setUserPermissions($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Policy";
    $data = json_encode([
        'IsAdministrator' => false,
        'IsHidden' => false,
        'IsDisabled' => false,
        'EnableAllDevices' => true,
        'EnableAllChannels' => true,
        'EnablePublicSharing' => true,
        'EnableRemoteAccess' => true,
        'EnableLiveTvManagement' => true,
        'EnableLiveTvAccess' => true,
        'EnableMediaPlayback' => true,
        'EnableAudioPlaybackTranscoding' => true,
        'EnableVideoPlaybackTranscoding' => true,
        'MaxActiveSessions' => 1  // Set max login sessions to 1
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
        echo "HTTP Code: $httpCode\nResponse: $response\n";
    }

       // Set user configuration to disable subtitles by default
    $url = $serverUrl . "Users/$userId/Configuration";
    $configData = json_encode([
        'SubtitleMode' => 'None'  // Ensure subtitles are set to 'None'
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $configData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
       // echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
       // echo "HTTP Code: $httpCode\nResponse: $response\n";
    }

    return json_decode($response, true);
}

$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$username="reseller_test2";
$password="reseller_test2";
$jellyuser=$username;
$jellypass=$password;
$userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
$userId = $userResponse['Id'];

if (isset($userId)) {
    echo "User created with ID: $userId\n";
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
    echo "Permissions set for user ID: $userId\n";
} else {
    echo "Failed to create user\n";
    var_dump($userResponse); // Output the response for debugging
}
/*jellyfin ends here*/
?>