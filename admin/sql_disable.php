<?php
require '../conn.php'; // Include your connection file

// Function to fetch expired users
function getExpiredUsers($conn) {
    $sql = "SELECT id, full_name, email, password, modified, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id, jellyfin_status
            FROM users 
            WHERE txn_id = '4_day_trial' and plan_expiry < NOW()";
    $result = $conn->query($sql);
    
    $expiredUsers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expiredUsers[] = $row;
        }
    }
    return $expiredUsers;
}

// Function to get user ID by username and disable the user
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
    
    return $result !== FALSE;
}

// Function to get user ID by username
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
        return null;
    }

    $users = json_decode($response, true);
    foreach ($users as $user) {
        if (strcasecmp($user['Name'], $username) == 0) {
            return $user['Id'];
        }
    }

    return null;
}

// Function to disable users by their usernames
function disableUsers($serverUrl, $apiKey, $usernames) {
    foreach ($usernames as $username) {
        $userId = getUserIdByUsername($serverUrl, $apiKey, $username);
        if ($userId) {
            disableUserByUrl($serverUrl, $apiKey, $userId);
        }
    }
}

// Handling form submission to disable users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_users'])) {
    $serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
    $apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key

    // Disable users based on the fetched usernames
    $usernamesToDisable = $_POST['usernames'] ?? [];
    disableUsers($serverUrl, $apiKey, $usernamesToDisable);
}

// Fetch expired users
$expiredUsers = getExpiredUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Users</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Expired Users</h1>

<?php if (!empty($expiredUsers)): ?>
    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Plan Expiry</th>
                      <th>Jellyfin_Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiredUsers as $user): ?>
                    <tr>
                        <td><input type="checkbox" name="usernames[]" value="<?= htmlspecialchars($user['username']) ?>"></td>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['plan_expiry']) ?></td>
                        <td><?= htmlspecialchars($user['jellyfin_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="disable_users">Disable Selected Users</button>
    </form>
<?php else: ?>
    <p>No expired users found.</p>
<?php endif; ?>

</body>
</html>
