<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}
// Check if the user clicked "Edit email?" link
if (isset($_POST['edit_email'])) {
    session_unset();
    session_destroy();
    header("Location: trial_auto.php");
    exit;
}
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'conn.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$signup_class = "";
$signup_success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Handle email and password submission
         $full_name =($_POST['fullname']);
        $email = $_POST['email'];
         $Mobile = $_POST['Mobile'];
        $password = $_POST['password'];
        $trial = $_POST['trial'] ?? '';
 
        // Extract first and last name from full name
        $name_parts = explode(' ', $full_name);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        // Get selected plan and subscription duration
        $plan_amount = 0;// varchar
        $month = 0; // int
        $days=1;
        $no_of_devices = 1; // For ₹49 plan
        
        // Calculate plan validity based on the selected subscription duration
        $plan_valid_for = $month;
        $currentDateTime = date('Y-m-d H:i:s');
        // Date and time three days from now
        $expiryDateTime = date('Y-m-d H:i:s', strtotime("+1 days"));
        // Assign to $plan_expiry
        $plan_expiry = $expiryDateTime;

        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Email already exists, prompt user to sign in
            $message = "<span style='color:red'>Email already exists.</span> Please <a class='text-blue' href='signin.php'>Sign in</a> instead.";
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Store OTP in session
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['Mobile'] = $Mobile;
            $_SESSION['password'] = $password;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['plan_valid_for'] = $plan_valid_for;
            $_SESSION['plan_amount'] = $plan_amount;
            $_SESSION['plan_expiry'] = $plan_expiry;
            $_SESSION['no_of_devices'] = $no_of_devices;
            
            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'scoobytv49@gmail.com'; // Your Gmail address
                $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password or App password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'OTP Verification';
                $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
        }
        .container {
            width: 85%;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border: 1px solid #dddddd;
        }
        .header {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .otp {
            font-size: 36px;
            color: #555555;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            font-size: 14px;
            color: #777777;
            margin-top: 30px;
        }
        
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
        </div>
        <p style='color: #222;'>Thank you for registering with ScoobyTV.<br> Please use the below OTP to complete your registration:</p>
        <div class='otp'>
            $otp
        </div>
        
        <div class='footer'>
            Regards,<br>
            ScoobyTV Team
        </div>
    </div>
</body>
</html>
";
                $mail->send();
                $message = "<span style='color:#5E62DE;font-weight:bold;'>OTP has been sent to Email id -</span> <br>" . $email;
                // Show OTP form
                $_SESSION['otp_sent'] = true;
            } catch (Exception $e) {
                $message = "<span style='color:red;font-weight:bold;'>Message could not be sent.</span> Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } elseif (isset($_POST['otp'])) {
        // Handle OTP submission
        $entered_otp = $_POST['otp'];
        $stored_otp = $_SESSION['otp'];

        if ($entered_otp == $stored_otp) {
            // OTP verified successfully
            $email = $_SESSION['email']; 
            $Mobile = $_SESSION['Mobile'];
            $password = $_SESSION['password'];
            $full_name = $_SESSION['full_name'];
            $first_name = $_SESSION['first_name'];
            $last_name = $_SESSION['last_name'];
            $plan_valid_for = $_SESSION['plan_valid_for'];
            $plan_amount = $_SESSION['plan_amount'];
            $plan_expiry = $_SESSION['plan_expiry'];
            $no_of_devices = $_SESSION['no_of_devices'];
            $purchase='Successfull & Verified';
            $txn_id='1_day_trial';

            $username = $first_name;
            // Function to generate a unique username
            function generateUniqueUsername($conn, $username)
            {
                $originalUsername = $username;
                $count = 2;
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                do {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $stmt->bind_result($existing_count);
                    $stmt->fetch();
                    if ($existing_count > 0) {
                        $username = $originalUsername . $count;
                        $count++;
                    } else {
                        break;
                    }
                } while (true);
                $stmt->close();
                return $username;
            }

            // Generate a unique username
            $username = generateUniqueUsername($conn, $username);

                
/*Jellyfin create user*/
/* Jellyfin create user */
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
        // Handle error
    }

    if ($httpCode !== 200) {
        // Handle non-200 response
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
        'EnableVideoPlaybackTranscoding' => true
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
        // Handle error
    }

    if ($httpCode !== 200) {
        // Handle non-200 response
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
        // Handle error
    }

    if ($httpCode !== 200) {
        // Handle non-200 response
    }

    return json_decode($response, true);
}

function loginUser($serverUrl, $jellyuser, $jellypass) {
    $url = $serverUrl . 'Users/AuthenticateByName';
    $data = json_encode([
        'Username' => $jellyuser,
        'Pw' => $jellypass
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        // Handle error
    }

    if ($httpCode !== 200) {
        // Handle non-200 response
    }

    return json_decode($response, true);
}

$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$jellyuser = $username;
$jellypass = $password;
$jellyfin_status = "";

// Create the user
$userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
$userId = $userResponse['Id'];

if (isset($userId)) {
    $jellyfin_status = "active - $userId"; 
    // Set user permissions
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
    // Automatically log in the user
    $loginResponse = loginUser($serverUrl, $jellyuser, $jellypass);

    // If login is successful, store the AccessToken in a session or cookie
    if (isset($loginResponse['AccessToken'])) {
        session_start();
        $_SESSION['jellyfin_token'] = $loginResponse['AccessToken'];  // Store token in session
        $_SESSION['jellyfin_user_id'] = $userId;  // Store user ID in session
        // You can also store in a cookie if needed
        // setcookie('jellyfin_token', $loginResponse['AccessToken'], time() + 3600, "/");
    } else {
        // Handle login failure
        // echo "Failed to log in user\n";
    }
} else {
    $jellyfin_status = "Failed to create user"; 
}

/*jellyfin ends here*/



            // Insert the user details into the database including the username
            $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, first_name, last_name, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, Phone, username, txn_id, jellyfin_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssissss", $email, $password, $full_name, $first_name, $last_name, $plan_valid_for, $plan_amount,$purchase, $plan_expiry, $no_of_devices, $Mobile, $username,$txn_id,$jellyfin_status);

            if ($stmt->execute()) {
                  // Send email to admin
                $admin_mail = new PHPMailer(true);
                try {
                    $admin_mail->isSMTP();
                    $admin_mail->Host = 'smtp.gmail.com';
                    $admin_mail->SMTPAuth = true;
                    $admin_mail->Username = 'scoobytv49@gmail.com'; // Your Gmail address
                    $admin_mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password or App password
                    $admin_mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $admin_mail->Port = 587;

                    // Recipients
                    $admin_mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
                    $admin_mail->addAddress('yoitv278@gmail.com'); // Admin email

                    // Content
                    $admin_mail->isHTML(true);
                    $admin_mail->Subject = 'New User Trial Request';
                    $admin_mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>New User Trial Active</title>
</head>
<body>
    <h2>Hi Admins,</h2>
    <h3>A new user has signed up for a trial:</h3>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Username:</strong> $username</p>
    <p><strong>Full Name:</strong> $full_name</p>
    <p><strong>Phone:</strong> $Mobile</p>
    <p><strong>Jellyfin Status:</strong> $jellyfin_status</p>
     <p><strong>Plan Expiry Date:</strong> $plan_expiry</p>
    <h4>New Trial User Created :)</h4>
</body>
</html>
";
                $admin_mail->send();
                } catch (Exception $e) {
                    // Handle error if admin email could not be sent
                }
               $_SESSION['user_id'] = $stmt->insert_id; // Set user ID in session
               $signup_class = "hidden";
               $signup_success = "<h2 style='color:#5E62DE;'>Signup successfull</h2>";
               $message = "<span class='loading'>logging in....<span>";
               header("Refresh: 2; url=signin.php");
            } else {
                $message = "<span style='color:red;font-weight:bold;'>Error creating account: </span> " . $stmt->error;
            }

            // Clear the session to prevent resubmission
             unset($_SESSION['otp_sent']);
        } else {
            $message = "<span style='color:red;font-weight:bold;'>Invalid OTP. Please try again.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="robots" content="noindex, nofollow">

  <title>ScoobyTV</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto|Poppins" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #141414;
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
    }
    .btn-buy {
        background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%);
        display: inline-block;
        padding: 10px 35px;
        border-radius: 4px;
        color: #fff;
        transition: none;
        font-size: 15px;
        font-weight: 400;
        font-family: "Roboto", sans-serif;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-buy:hover {
        background: linear-gradient(to right, #ff416c, #ff4b2b);
        color: #fff;
    }
    .hidden {
        display: none;
    }
    .contact .signup-email-form {
        /* box-shadow: 0 0 30px rgb(140 0 255 / 64%); */
        padding: 30px;
        background: #fff;
    }
    .contact .signup-email-form input {
        padding: 10px 15px;
    }
    .contact .signup-email-form input, .contact .php-email-form textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
    }
.sent-message {
    display: none;
    color: #fff;
    background: #18d26e;
    text-align: center;
    padding: 15px;
    font-weight: 600;
}

.error-message {
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
   @media (max-width: 576px) {.signin_mobile{background: linear-gradient(90deg, #ff416c, #ff4b2b);}}
     @media (min-width: 950px) {section {padding: 100px 450px;}}
  </style>
   
</head>



<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R8VL44KX1F"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-R8VL44KX1F');
</script>
<!--    Analytics End here-->


<body>
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo"><img src="assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="index.php#movies">Movies</a></li>
          <li><a class="nav-link scrollto" href="index.php#series">Series</a></li>
          <li><a class="nav-link scrollto" href="index.php#pricing">Pricing</a></li>
          <li><a class="nav-link scrollto" href="index.php#faq">FAQ's</a></li>
           <li><a class="getstarted scrollto signin_mobile" href="signin.php">Sign in</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <main id="main">
   <section id="signup" class="contact section-bg" style="background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2 class="<?php echo $signup_class; ?>">Create Account</h2>
            <?php echo $signup_success; ?>
            <p><?php echo $message; ?></p>
            <?php if (isset($_SESSION['otp_sent'])): ?>
                <form id="edit-email-form" action="trial_auto.php" method="post" style="display:inline;">
                    <input type="hidden" name="edit_email" value="1">
                    <button type="submit" class="btn btn-link" id="editEmailButton">Edit email?</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="row <?php echo $signup_class; ?>">
            <div class="col-lg-12">
                <?php if (!isset($_SESSION['otp_sent'])): ?>
                <!-- Email and Password Form -->
                <form id="signup-form" action="trial_auto.php" method="post" role="form" class="signup-email-form">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Full Name" required pattern="[A-Za-z\s]+" title="Please enter A-Z or a-z letters only." required>
                        </div>
                        <div class="col-md-12 form-group mt-3">
        <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
         <small id="emailError" class="text-danger d-none">Use an email address from Gmail, Yahoo, Outlook, or Hotmail.</small>
                        </div>
                    </div>

                    <div class="col-md-12 form-group mt-2 d-flex">
                     <input type="tel" name="+91" class="form-control" id="+91" value="+91" style="width: 40px;padding: 0px 0px 0px 8px;"required disabled>
    <input type="tel" name="Mobile" class="form-control" id="Mobile" placeholder="Mobile no" pattern="[6-9]\d{9}" title="Please enter a valid 10-digit mobile number" required>
                        </div>
                    <div class="form-group mt-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Create Password" required>
                    </div>
                     <div class="form-group mt-3">
                        <input type="" class="form-control" name="trial" id="trial" value="1_day_trial" disabled>
                    </div>
                    
                   
                    <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-buy"  id="signUpButton">Sign up</button>
                    </div>
                    <div class="text-dark text-center mt-3">
                        <span><small>Already have account?</small> </span><a href="signin.php" > Sign in</a> 
                    </div>
                    
                </form>
               <script>
               document.getElementById('email').addEventListener('input', function() {
    const emailInput = this.value;
    const signUpButton = document.getElementById('signUpButton');
    const emailError = document.getElementById('emailError');
    
    // Regex pattern to match allowed domains
    const emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com|hotmail\.com)$/;

    if (emailPattern.test(emailInput)) {
        // If the email matches the pattern, enable the signup button and hide the error message
        signUpButton.disabled = false;
        emailError.classList.add('d-none');
    } else {
        // If the email doesn't match the pattern, disable the signup button and show the error message
        signUpButton.disabled = true;
        emailError.classList.remove('d-none');
    }
});

               </script>
                <?php else: ?>

                <!-- OTP Form -->
                <form id="otp-form" action="trial_auto.php" method="post" role="form" class="signup-email-form">
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="otp" id="otp" placeholder="Enter OTP" maxlength="6" required>
                    </div>
                    <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-buy" id="verifyOtpButton" >Verify OTP</button>
                    </div>
                     <!--<div class="text-center mt-4">
                       <a class="btn-secondary" id="resendOtpButton" onclick="resendOtp()" style="cursor:pointer;">Resend OTP</a>
                    </div>-->
                </form>
                 <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("otp-form").addEventListener("submit", function(event) {
            var button = document.getElementById("verifyOtpButton");
            button.disabled = true;
            button.classList.add("loading");
        });
    });
    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
  </main>
 
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
  <script>
    // Function to parse URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };

    // Function to select plan based on price
    function selectPlanByPrice(price) {
        var planSelect = document.getElementById('plan');
        if (price == 99) {
            planSelect.value = "99";
        }
        else if (price == 119) {
            planSelect.value = "119";
        }
    }

    // Parse URL parameters and select plan
    var priceParam = getUrlParameter('price');
    if (priceParam !== '') {
        selectPlanByPrice(priceParam);
    }
</script>
<script>
document.getElementById("signup-form").addEventListener("submit", function(event) {
    var button = document.getElementById("signUpButton");
    button.disabled = true;
    button.classList.add("loading");
});
</script>

</body>
</html>
