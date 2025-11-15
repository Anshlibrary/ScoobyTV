<?php
session_start();

// Set session parameters for enhanced security
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript from accessing session cookie
ini_set('session.use_strict_mode', 1);  // Prevent session fixation
ini_set('session.cookie_lifetime', 2592000); // 1 month session lifetime
ini_set('session.gc_maxlifetime', 2592000); // Set garbage collection lifetime to 1 month
session_set_cookie_params(2592000); // 1 month cookie lifetime

// Check session expiration (max 1 month)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 2592000)) {
    // If the session has expired
    session_unset();
    session_destroy();
    header("Location: signin.php");
    exit;
}
$_SESSION['last_activity'] = time(); // Update last activity time

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

// CSRF token generation and validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Create a CSRF token
}

// Logout/Session Reset on clicking "Edit email?"
if (isset($_POST['edit_email'])) {
    session_unset();
    session_destroy();
    header("Location: trial_test.php");
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
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Sanitize user input
        $full_name = trim(htmlspecialchars($_POST['fullname']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $mobile = preg_replace('/[^0-9]/', '', $_POST['Mobile']);
        $password = $_POST['password'];  // Will hash this before storing
        $trial = $_POST['trial'] ?? '';

        // Extract first and last name from full name
        $name_parts = explode(' ', $full_name);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

        // Define plan details
        $plan_amount = 0;
        $month = 0;
        $days = 1;
        $no_of_devices = 1;

        $plan_valid_for = $month;
        $currentDateTime = date('Y-m-d H:i:s');
        $expiryDateTime = date('Y-m-d H:i:s', strtotime("+1 days"));
        $plan_expiry = $expiryDateTime;

        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $message = "<span style='color:red'>Email already exists.</span> Please <a class='text-blue' href='signin.php'>Sign in</a> instead.";
        } else {
            // Generate and store OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['Mobile'] = $mobile;
            $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $_SESSION['full_name'] = $full_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['plan_valid_for'] = $plan_valid_for;
            $_SESSION['plan_amount'] = $plan_amount;
            $_SESSION['plan_expiry'] = $plan_expiry;
            $_SESSION['no_of_devices'] = $no_of_devices;

            // Send OTP using PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'scoobytv49@gmail.com';
                $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail App password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'OTP Verification';
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif;'>
                        <h2>Your OTP is $otp</h2>
                        <p>Please use this OTP to complete your registration.</p>
                        <p>Regards,<br>ScoobyTV Team</p>
                    </div>";

                $mail->send();
                $message = "<span style='color:#5E62DE;font-weight:bold;'>OTP has been sent to Email id -</span> <br>" . htmlspecialchars($email);
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
            // OTP verified, proceed with user creation
            $email = $_SESSION['email'];
            $mobile = $_SESSION['Mobile'];
            $hashed_password = $_SESSION['password']; // Already hashed
            $full_name = $_SESSION['full_name'];
            $first_name = $_SESSION['first_name'];
            $last_name = $_SESSION['last_name'];
            $plan_valid_for = $_SESSION['plan_valid_for'];
            $plan_amount = $_SESSION['plan_amount'];
            $plan_expiry = $_SESSION['plan_expiry'];
            $no_of_devices = $_SESSION['no_of_devices'];
            $purchase = 'Successful & Verified';
            $txn_id = '1_day_trial';

            // Generate unique username
            $username = $first_name;

            function generateUniqueUsername($conn, $username) {
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

            $username = generateUniqueUsername($conn, $username);


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
        
        //echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
     
       //echo "HTTP Code: $httpCode\nResponse: $response\n";
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
     
        //echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
       
        //echo "HTTP Code: $httpCode\nResponse: $response\n";
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
$jellyuser=$username;
$jellypass=$password;
$jellyfin_status = "";
$userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
$userId = $userResponse['Id'];

if (isset($userId)) {
    $jellyfin_status = "active - $userId"; 
    //echo "User created with ID: $userId\n";
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
   // echo "Permissions set for user ID: $userId\n";
} else {
     $jellyfin_status = "Failed to create user"; 
   // echo "Failed to create user\n";
   // var_dump($userResponse); // Output the response for debugging
}
/*jellyfin ends here*/

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, first_name, last_name, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, Phone, username, txn_id, jellyfin_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssissss", $email, $hashed_password, $full_name, $first_name, $last_name, $plan_valid_for, $plan_amount, $purchase, $plan_expiry, $no_of_devices, $mobile, $username, $txn_id, $jellyfin_status);

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
    <title>OTP Verification</title>
    <style>
        body {font-family: Arial, sans-serif; color: #333333; }
        .container {            width: 85%;            padding: 20px;            text-align: center;            background-color: #f9f9f9;            border: 1px solid #dddddd;        }
        .header {            font-size: 24px;            margin-bottom: 20px;        }
        .otp {            font-size: 36px;            color: #555555;            font-weight: bold;            margin: 20px 0;        }
        .footer {            font-size: 14px;            color: #777777;            margin-top: 30px;        }
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
                $message = "<span style='color:red;font-weight:bold;'>Error: Could not create the user.</span>";
            }
                // Clear the session to prevent resubmission
             unset($_SESSION['otp_sent']);
            $stmt->close();
        } else {
            $message = "<span style='color:red'>Invalid OTP. Please try again.</span>";
        }
    }
}

$conn->close();
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
                <form id="edit-email-form" action="trial_test.php" method="post" style="display:inline;">
                    <input type="hidden" name="edit_email" value="1">
                    <button type="submit" class="btn btn-link" id="editEmailButton">Edit email?</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="row <?php echo $signup_class; ?>">
            <div class="col-lg-12">
                <?php if (!isset($_SESSION['otp_sent'])): ?>
                <!-- Email and Password Form -->
                <form id="signup-form" action="trial_test.php" method="post" role="form" class="signup-email-form">
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
                <form id="otp-form" action="trial_test.php" method="post" role="form" class="signup-email-form">
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
