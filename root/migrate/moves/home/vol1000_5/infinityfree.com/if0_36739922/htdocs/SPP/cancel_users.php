<?php
session_start(); // Start a session to store status messages
require '../conn.php'; // Include your connection file

// Check if is logged in
if (!isset($_SESSION['associate_id'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['session_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: signout.php');
    exit;
}

$statusMessages = [];
$admin_id = $_SESSION['associate_id'];

// Fetch associate's full name, credits, and paid amount
$associate_sql = "SELECT fullname, credits, username, session_id, user_type FROM Associate WHERE associate_id = ?";
$associate_stmt = $conn->prepare($associate_sql);
if (!$associate_stmt) {
    $_SESSION['statusMessages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    header('Location: cancel_users.php');
    exit;
}
$associate_stmt->bind_param('i', $admin_id);
if (!$associate_stmt->execute()) {
    $_SESSION['statusMessages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    header('Location: cancel_users.php');
    exit;
}
$associate_result = $associate_stmt->get_result();
if ($associate_result->num_rows === 0) {
    $_SESSION['statusMessages'][] = "Associate not found.";
    header('Location: cancel_users.php');
    exit;
}
$associate = $associate_result->fetch_assoc();
$current_credits = $associate['credits']; // Associate's current credits
$admin_name = htmlspecialchars($associate['username']);
$user_type = htmlspecialchars($associate['user_type']);
$associate_stmt->close();

// Function to fetch expired users
function getExpiredUsers($conn, $admin_name) {

            $sql = "SELECT id, full_name, email, created, plan_valid_for, plan_expiry, username, txn_id, jellyfin_status 
        FROM users 
        WHERE created >= DATE_SUB(NOW(), INTERVAL 12 HOUR)
        AND txn_id = ?";

    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_name);  // 's' indicates the parameter is a string
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
    
    $expiredUsers = [];
    
    // Fetch the results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expiredUsers[] = $row;
        }
    }
    
    // Close the statement
    $stmt->close();
    return $expiredUsers;
}

// Function to update Jellyfin status in the database
function updateJellyfinStatus($conn, $userId, $status, $months, $adminName) {
    $txn_id="Cancelled_by_".  $adminName;
     $sql = "UPDATE users SET jellyfin_status = ?, plan_expiry = DATE_SUB(NOW(), INTERVAL 1 DAY), plan_valid_for =?, txn_id = ?, updated_by = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $status, $months, $txn_id, $adminName, $userId);
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

// Function to update associate's credits in the database
function updateAssociateCredits($conn, $associate_id, $new_credits) {
    $sql = "UPDATE Associate SET credits = ? WHERE associate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $new_credits, $associate_id);
    return $stmt->execute();
}

// Function to disable users by their usernames
function disableUsers($conn, $serverUrl, $apiKey, $usernames, $admin_name, $current_credits) {
    $statusMessages = [];
    foreach ($usernames as $username) {
         // Get the user ID from the database
        $sql = "SELECT id, plan_valid_for, txn_id, created FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($dbUserId, $plan_valid_for, $txn_id, $created);
        $stmt->fetch();
        $stmt->close();
        $months = $plan_valid_for; // Use plan_valid_for directly for months
        $required_credits = $months; // 1 credit per month
        $months=0;

        $datetime_12_hours_ago = date('Y-m-d H:i:s', strtotime('-12 hours'));

        if ($dbUserId && $txn_id===$admin_name && $created >= $datetime_12_hours_ago) {
            // Get Jellyfin user ID by username
            $jellyfinUserId = getUserIdByUsername($serverUrl, $apiKey, $username);
            if ($jellyfinUserId) {
                if (disableUserByUrl($serverUrl, $apiKey, $jellyfinUserId,)) {
                    // Update the Jellyfin status in the database using the database user ID
                    updateJellyfinStatus($conn, $dbUserId, "disabled - $jellyfinUserId", $months, $admin_name);
                    $statusMessages[] = "User '$username' successfully deactivated. '$required_credits' credits added back to your account.";
                    $current_credits += $required_credits;
                    updateAssociateCredits($conn, $_SESSION['associate_id'], $current_credits);
                } else {
                    $statusMessages[] = "Failed to deactivate user '$username' via Jellyfin API.";
                }
            } else {
                $statusMessages[] = "Jellyfin user ID for '$username' not found.";
            }
        } else {
            $statusMessages[] = "User '$username' not Eligible for Cancellation.";
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
    $statusMessages = disableUsers($conn, $serverUrl, $apiKey, $usernamesToDisable, $admin_name, $current_credits);
    
    // Store messages in session for displaying later
    $_SESSION['status_messages'] = $statusMessages;

    
    // Redirect to the same page to avoid form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch expired users
$expiredUsers = getExpiredUsers($conn, $admin_name);
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

  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
    display:none;
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
.loading:before {
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
 .process-container {
            margin: 0 auto;
            padding: 20px;
            background-color: #dfbddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: linear-gradient(to bottom right, #716d6d, #141414);
        }
          .status-message {
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid green;
            background-color: #337333;
        }
        
.process-step h3, .process-step p {
            color: #141414;
        }
        .process-step {
            margin-bottom: 20px;
        }
        .process-step i {
            font-size: 24px;
            color: #007bff;
            margin-right: 15px;
        }
        .process-step h3 {
            margin: 0;
            font-size: 20px;
            color: #007bff;
        }
        .process-step p {
            margin: 5px 0 0 39px;
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

  .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .profile-details {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .password-container {
        display: flex;
        align-items: center;
    }
    
    .password-container input {
        background: none;
        border: none;
        color: #fff;
        padding: 0;
        margin: 0;
    }
    
    .password-container i {
        cursor: pointer;
        margin-left: 10px;
    }
     .dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #fff !important; /* Change text color for pagination buttons */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.next {
    color: #fff !important; /* Ensure the "Next" button text is white */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.previous {
    color: #fff !important; /* Ensure the "Previous" button text is white */
}

.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label {
    color: #fff; /* Change text color for search and length labels */
}

.dataTables_wrapper .dataTables_info {
    color: #fff; /* Change text color for info text */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #007bff; /* Example background color for active and hover states */
    color: #fff; /* Ensure text is white in these states */
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
  
@media (max-width: 975px) {
  .signin_mobile{
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
  }
}
 @media (max-width: 768px) {
        .profile-details table th, .profile-details table td {
            display: block;
            width: 100%;
        }
    }
</style>
</head>

<body> 
  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      
      <a href="dashboard.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="dashboard.php">Home</a></li>
          <li><a href="cancel_users.php">Cancel_users</a></li>
          <li><a href="signout.php" class="btn btn-sign" style="">Sign out</a></li>
        </ol>
        
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
   <header id="header" style="padding:0;">
    <div class="container d-flex align-items-center justify-content-between">
      <!--<h1 class="logo"><a href="index.html"></a></h1> Text logo -->
       <h2 style="font-size: 18px;"><a href="dashboard.php" class="logo"><i class="fa fa-arrow-left"></i></a></h2>
      <a href="#" class="logo"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto" href="dashboard.php">Dashboard</a></li>
          <li><a class="nav-link scrollto" href="renew_users.php">Renew users</a></li>
           <li><a class="nav-link scrollto active" href="cancel_users.php">Refund</a></li>
          <li><a class="nav-link scrollto" href="tutorial.php">Tutorials</a></li>
          <?php if ($user_type !== 'client_user'): ?>
        <li><a class="nav-link scrollto " href="pricing.php">Pricing</a></li>
    <?php endif; ?>
          <li><a class="nav-link scrollto" href="faq.php">FAQ's</a></li>
          <li>
           <a class="getstarted scrollto signin_mobile" href="profile.php">Credits :&nbsp;&nbsp; <b style="color:#09f109;"> <?php echo htmlspecialchars($associate['credits']); ?></b></a>     
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->


    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;margin-top:10px;">Eligible users for cancellation</h2>
        <div class="">
<!-- Display status messages -->
<?php if (isset($_SESSION['status_messages']) && count($_SESSION['status_messages']) > 0): ?>
    <div class="status-message">
        <ul>
            <?php foreach ($_SESSION['status_messages'] as $message): ?>
                <li><?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['status_messages']); // Clear messages after displaying ?>
<?php endif; ?>
 </div>
      </div>
 <div class="row" style="background: linear-gradient(to bottom right, #716d6d, #141414); border-radius:20px;padding:10px;">
<div class="col-lg-12 mb-3 table-responsive" >                     
                
<?php if (!empty($expiredUsers)): ?>
    <form method="post" id="users_disable">
        <table id="renewuserTable">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)" disabled> Select All</th>
              
                    <th>Username</th>
                    <th>Plan Expiry</th>
                    <th>Validity</th>
                    <th>Jellyfin Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiredUsers as $user): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="usernames[]" value="<?= htmlspecialchars($user['username']) ?>">
                        </td>
                     
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['plan_expiry']) ?></td>
                        <td><?= htmlspecialchars($user['plan_valid_for']) ?> Month</td>
                        <td <?= str_starts_with($user['jellyfin_status'], 'disabled') ? 'style="color:red;"' : 'style="color:green;"' ?>><?= htmlspecialchars($user['jellyfin_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
       <button type="submit" name="disable_users" class="btn btn-buy hidden" id="submit_btn">Disable Selected Users</button>
    </form>
    <button  class="btn btn-buy" id="run_btn">Cancel</button>
<?php else: ?>
    <p style="color:#ff4b2b;">No eligible users found for cancellation.</p>
<?php endif; ?>
    </div>
     
</div>
</div>

</section>

  </main><!-- End #main -->

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

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
  </script>


     


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');

        togglePasswordBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const passwordInput = this.parentNode.querySelector('.password-input');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                   this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    this.innerHTML = '<i class="fa fa-eye"></i>';
                }
            });
        });
    });
</script>

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


  <!-- Vendor JS Files -->
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#renewuserTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>