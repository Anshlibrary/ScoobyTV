<?php
session_start(); // Start a session to store status messages
require '../conn.php'; // Include your connection file


// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Babualogin.php');
    exit;
}

$statusMessages = [];
$admin_id = $_SESSION['admin_id'];

// Query to fetch admin's first name
$admin_sql = "SELECT first_name, username FROM admins WHERE id = ?";
$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->bind_param('i', $admin_id);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();

if ($admin_result->num_rows === 0) {
    echo "<h2 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>Admin not found</h2>";
    exit;
}

$admin = $admin_result->fetch_assoc();
$admin_name = htmlspecialchars($admin['username']);

if (isset($_GET['disable']) && $_GET['disable'] == 'true') {
     
}

// Function to fetch expired users
function getExpiredUsers($conn) {
    $sql = "SELECT id, full_name, email, modified, plan_valid_for, paid_amt, purchase, plan_expiry, username, txn_id, jellyfin_status
            FROM users 
           WHERE txn_id NOT LIKE 'guest%'
           AND purchase = 'Successfull & Verified'
        AND jellyfin_status NOT LIKE 'disabled%' 
        AND plan_expiry < NOW()";

        //(txn_id = '1_day_trial' OR txn_id REGEXP '^[0-9]{1,16}$' OR txn_id LIKE 'reseller%' OR txn_id LIKE 'Assoc%') 
         

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
    $apiKey = '9b598fb2508d41c3b0980916a93cf1c8'; // Replace with your API key

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

<!-- HTML or further processing of $user data -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
     body {
        font-family: Arial, sans-serif;
        background-color: #141414;
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
    }
    /*Custom slider css*/
    .blog-header{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 5px 0;}
    .blog-body{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 20px 0;    }
    .breadcrumbs{margin-top:0;    }

    .logo-img{    max-height: 40px;    }
    .section-bg{
        background: linear-gradient(85deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);
    }
    .btn-sign{
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    display: inline-block;
    padding: 5px 5px;
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}
.btn-sign:hover{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
color: #fff;
}
.btn-buy{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
    display: inline-block;
    padding: 5px 10px;
    margin-left:20px;
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}

  .btn-buy:hover{
        background: linear-gradient(42deg, #1e1289 0%, #2c0656 100%);color: #fff;
}
.hidden{
    display:none !important;
}
.my-email-form {
    box-shadow: 0 0 30px rgba(214, 215, 216, 0.1);
    padding: 5px 10px;
    background: #fff;
    border-radius: 10px;
}
section{
  padding: 10px 0;
}
  .status-message {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid green;
            background-color: #337333;
        }
.contact .my-email-form input {
        padding: 10px 15px;
    }
    .contact .my-email-form input, .contact .my-email-form textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
    }
.contact .my-email-form .sent-message {
    display: none;
    color: #fff;
    background: #18d26e;
    text-align: center;
    padding: 15px;
    font-weight: 600;
}
.contact .my-email-form .error-message {
    display: none;
    color: #fff;
    background: #ed3c0d;
    text-align: left;
    padding: 15px;
    font-weight: 600;
}
.contact .my-email-form .loading:before {
    content: "";
    display: inline-block;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    margin: 0 10px -6px 0;
    border: 3px solid #18d26e;
    border-top-color: #eee;
    animation: animate-loading 1s linear infinite;
}
  .form-container {
            display: flex;
            justify-content: center;
        }
        .form-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            max-width: 500px;
            width: 100%;
        }
        .loading:before {
    content: "";
    display: inline-block;
    border-radius: 50%;
    width: 14px;
    height: 14px;
    margin: 0 10px -6px 0;
    border: 3px solid #18d26e;
    border-top-color: #eee;
    animation: animate-loading 1s linear infinite;
} 

 /* Add this style for responsive table */
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
    table th {
        background-color: #222;
        color: #fff;
    }
    table td {
        background-color: #343a40;
    }

        table th, table td {
            border: 1px solid #ddd;
            padding: 5px 10px;
            text-align: left;
        }


@media (min-width: 992px) {
  .box {
    max-width: 400px; /* Adjust the maximum width of the boxes */
    margin: 0 auto; /* Center align the boxes */
  }
}
@media (max-width: 768px) {
    .my-breadcrumb-list {
      display: flex !important;
      justify-content: flex-end;
      align-items: center;
      gap:20px;
    }
  }
</style>
</head>
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

<body> 
  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="../index.php">Home</a></li>
          <li><a href="services.php">Services</a></li>
          <li>AdminPanel</li>
        </ol>
        <a href="../signout.php" class="btn btn-sign" style="">Sign out</a>
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <div class="container">
   <h2 style="margin-left:auto;margin-right: auto;padding-left:10px ;font-size: 18px;">Admin-<?php echo "$admin_name"; ?> </h2>
   
  </div>
  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
       <div class="form-container">
                   
                      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="my-email-form form-wrapper" id="">
                                <select id="all_users" name="all_users" class="form-control" required>
                                    <option value="all">All</option>
                                </select>
                                <select id="users_plan_expired" name="users_plan_expired" class="form-control" required>
                                     <option value="expired" <?php echo ($users_plan_expired == 'expired') ? 'selected' : ''; ?>>Disable Expired Users</option>
                                </select>
                               
                            </form>
                </div>
                
    

       
      </div>
      <div class="row">
        <div class="col-lg-12 mb-3 table-responsive">
        

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
    <form method="post" id="users_disable">
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
       <button type="submit" name="disable_users" class="btn btn-buy hidden" id="submit_btn">Disable Selected Users</button>
    </form>
    <button  class="btn btn-buy" id="run_btn">Run</button>
<?php else: ?>
    <p>No expired users found.</p>
<?php endif; ?>

 
</div>

</div>
    </div>
    
  </section>
</section>
<script>
    document.getElementById('run_btn').addEventListener('click', function() {
        var SubmitBtn = document.getElementById('submit_btn');
        SubmitBtn.click();
          var RunBtn = document.getElementById("run_btn");
            RunBtn.disabled = true;
            RunBtn.classList.add("loading");
              RunBtn.innerHTML = "Processing";


    });
</script>



  </main><!-- End #main -->
 

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>


</body>
</html>