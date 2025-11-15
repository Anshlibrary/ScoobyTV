<?php
session_start(); // Start a session to store status messages
require '../conn.php'; // Include your connection file

// Function to fetch expired users
function getExpiredUsers($conn) {
    $sql = "SELECT id, full_name, email, modified, plan_valid_for, paid_amt, purchase, plan_expiry, username, txn_id, jellyfin_status
            FROM users 
           WHERE (txn_id = '1_day_trial' OR txn_id REGEXP '^[0-9]{1,16}$') 
        AND purchase = 'Successfull & Verified' 
        AND plan_expiry < NOW()"; 

    $result = $conn->query($sql);
    
    $expiredUsers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expiredUsers[] = $row;
        }
    }
    return $expiredUsers;
}

// Function to update Jellyfin status in the database
function updateJellyfinStatus($conn, $userId, $status) {
    $sql = "UPDATE users SET jellyfin_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $userId);
    return $stmt->execute();
}

// Function to disable the user by sending a request to Jellyfin
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
function disableUsers($conn, $serverUrl, $apiKey, $usernames) {
    $statusMessages = [];
    foreach ($usernames as $username) {
        // Get the user ID from the database
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($dbUserId);
        $stmt->fetch();
        $stmt->close();

        if ($dbUserId) {
            // Get Jellyfin user ID by username
            $jellyfinUserId = getUserIdByUsername($serverUrl, $apiKey, $username);
            if ($jellyfinUserId) {
                if (disableUserByUrl($serverUrl, $apiKey, $jellyfinUserId)) {
                    // Update the Jellyfin status in the database using the database user ID
                    updateJellyfinStatus($conn, $dbUserId, "disabled - $jellyfinUserId");
                    $statusMessages[] = "User '$username' successfully disabled.";
                } else {
                    $statusMessages[] = "Failed to disable user '$username' via Jellyfin API.";
                }
            } else {
                $statusMessages[] = "Jellyfin user ID for '$username' not found.";
            }
        } else {
            $statusMessages[] = "User '$username' not found in the database.";
        }
    }
    return $statusMessages;
}

// Handling form submission to disable users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_users'])) {
    $serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
    $apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key

    // Disable users based on the fetched usernames
    $usernamesToDisable = $_POST['usernames'] ?? [];
    $statusMessages = disableUsers($conn, $serverUrl, $apiKey, $usernamesToDisable);
    
    // Store messages in session for displaying later
    $_SESSION['status_messages'] = $statusMessages;
    
    // Redirect to the same page to avoid form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
        .status-message {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid green;
            background-color: #e9ffe9;
        }
    </style>
    <script>
        function toggleSelectAll(source) {
            checkboxes = document.getElementsByName('usernames[]');
            for (var i = 0; i < checkboxes.length; i++) {
                if (!checkboxes[i].disabled) { // Only check if not disabled
                    checkboxes[i].checked = source.checked;
                }
            }
        }
    </script>
</head>
<body>

<h1>Expired Users</h1>

<!-- Display status messages -->
<?php if (isset($_SESSION['status_messages'])): ?>
    <div class="status-message">
        <h2>Status Messages:</h2>
        <ul>
            <?php foreach ($_SESSION['status_messages'] as $message): ?>
                <li><?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['status_messages']); // Clear messages after displaying ?>
<?php endif; ?>

<?php if (!empty($expiredUsers)): ?>
    <form method="post">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"> Select All</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Plan Expiry</th>
                    <th>Paid Amt</th>
                    <th>Jellyfin Status</th>
                    <th>Txn id</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiredUsers as $user): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="usernames[]" value="<?= htmlspecialchars($user['username']) ?>" <?= str_starts_with($user['jellyfin_status'], 'disabled') ? 'disabled' : '' ?>  >
                        </td>
                        
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['plan_expiry']) ?></td>
                        <td><?= htmlspecialchars($user['paid_amt']) ?></td>
                        <td <?= str_starts_with($user['jellyfin_status'], 'disabled') ? 'style="color:red;"' : 'style="color:green;"' ?>><?= htmlspecialchars($user['jellyfin_status']) ?></td>
                        <td><?= htmlspecialchars($user['txn_id']) ?></td>
                    
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
