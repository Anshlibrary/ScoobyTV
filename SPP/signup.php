<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('session.gc_maxlifetime', 2592000); // 30 days
session_start();

// Check if the user is already logged in
if (isset($_SESSION['associate_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Check if the user clicked "Edit email?" link
if (isset($_POST['edit_email'])) {
    session_unset();
    session_destroy();
    header("Location: signup.php");
    exit;
}

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../conn.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$signup_class = "";
$signup_success = "";
// Function to generate coupon code based on invite code
// function generateUserType($conn, $invite_code) {
//     // Here you can implement logic to check the invite code against the database

//     // Prepare a statement to get user_type from the Associate table
//     $stmt = $conn->prepare("SELECT user_type FROM Associate WHERE coupon_code = ?");
//     $stmt->bind_param("s", $invite_code);
//     $stmt->execute();
//     $stmt->bind_result($user_type);
//     $stmt->fetch();
//     $stmt->close();

//     // Determine the user type based on the result
//     if ($user_type === 'super_user') {
//         return 'client_user';
//     } else {
//         return 'normal_user';
//     }
// }

function generateUserType($conn, $invite_code) {     
    // Prepare a statement to get user_type from the Associate table     
    $stmt = $conn->prepare("SELECT user_type FROM Associate WHERE coupon_code = ?");     
    $stmt->bind_param("s", $invite_code);     
    $stmt->execute();     
    $stmt->bind_result($user_type);     
    $found = $stmt->fetch();  // fetch() returns true if a result was found     
    $stmt->close();      

    // Check if coupon_code was found
    if ($found) {
        // Determine the user type based on the result     
        if ($user_type === 'super_user') {         
            return 'client_user';     
        } else {         
            return 'normal_user';     
        }
    } else {
        // Show alert and redirect using JavaScript
        echo "<script>
                alert('Invalid coupon code. Please try again.');
                window.location.href = 'signup.php';
              </script>";
        exit;  // Stop further PHP execution after the redirect
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Handle email and password submission
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
       
        $fullname = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $state = $_POST['state'];
        $country = $_POST['country'];
        $payment_option = $_POST['payment_option'];
        $bank_name = isset($_POST['bank_name']) ? $_POST['bank_name'] : null;
        $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : null;
        $ifsc_code = isset($_POST['ifsc_code']) ? $_POST['ifsc_code'] : null;
        $upi_id = isset($_POST['upi-id']) ? $_POST['upi-id'] : null;
        $invite_code = isset($_POST['invite-code']) ? trim($_POST['invite-code']) : null;
        if (empty($invite_code)) {
    // If the invite code is blank, set a JavaScript alert
      echo "<script>
                alert('Please fill the invite code.');
                window.location.href = 'signup.php';
              </script>";
        exit;
   
    exit(); // Stop the script from executing further
}

        $coupon_code = generateCouponCode(); // Generate or get the coupon code

 $user_type = generateUserType($conn, $invite_code);
if ($user_type === 'normal_user') {
        $coupon_code = $coupon_code;
    } else {
        $coupon_code = $invite_code;
    }
        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT associate_id FROM Associate WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Email already exists, prompt user to sign in
            $message = "<span style='color:red'>Email already exists.</span> Please <a class='text-blue' href='login.php'>Sign in</a> instead.";
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Store OTP in session
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['mobile'] = $mobile;
            $_SESSION['state'] = $state;
            $_SESSION['country'] = $country;
            $_SESSION['payment_option'] = $payment_option;
            $_SESSION['bank_name'] = $bank_name;
            $_SESSION['account_number'] = $account_number;
            $_SESSION['ifsc_code'] = $ifsc_code;
            $_SESSION['upi_id'] = $upi_id;
            $_SESSION['coupon_code'] = $coupon_code;
             $_SESSION['invite-code'] =  $invite_code;
            $_SESSION['user_type'] = $user_type;
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
            $password = $_SESSION['password'];
            $fullname = $_SESSION['fullname'];
            $mobile = $_SESSION['mobile'];
            $state = $_SESSION['state'];
            $country = $_SESSION['country'];
            $payment_option = $_SESSION['payment_option'];
            $bank_name = $_SESSION['bank_name'];
            $account_number = $_SESSION['account_number'];
            $ifsc_code = $_SESSION['ifsc_code'];
            $upi_id = $_SESSION['upi_id'];
            $coupon_code = $_SESSION['coupon_code'];
            $created_at = date("Y-m-d H:i:s");
            $invite_code = $_SESSION['invite-code']; 
            $user_type = $_SESSION['user_type'];
            //crete a usertype
            // Generate coupon code

            // Get the first part of the fullname
$first_name = explode(' ', trim($fullname))[0];
$first_name = ucfirst(strtolower($first_name));
$username_base = "Assoc" . $first_name;
$username = $username_base;

// Check if the username already exists in the database
$count = 1;
while (true) {
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM Associate WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->bind_result($exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($exists == 0) {
        break; // Username is available
    } else {
        // If the username exists, append a number to make it unique (AssocAnkit2, AssocAnkit3, etc.)
        $count++;
        $username = $username_base . $count;
    }
}

            // Insert the associate details into the database
           $stmt = $conn->prepare("INSERT INTO Associate (fullname, mobile, email, password, state, country, payment_option, bank_name, account_number, ifsc_code, upi_id, user_type, coupon_code, created_at, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssssss", $fullname, $mobile, $email, $password, $state, $country, $payment_option, $bank_name, $account_number, $ifsc_code, $upi_id, $user_type, $coupon_code, $created_at, $username);

            if ($stmt->execute()) {
                  session_regenerate_id(true); // Generate a new session ID

    // Get the new session ID
    $new_session_id = session_id();

    // Set associate ID and other details in the session
    $_SESSION['associate_id'] = $stmt->insert_id; // Set associate ID in session
    $_SESSION['email'] = $email;
    $_SESSION['fullname'] = $fullname;
    $_SESSION['session_id'] = $new_session_id;
    // Set session expiry time (1 day from now)
    $session_expiry = date('Y-m-d H:i:s', strtotime('+30 day'));

    // Update the session_id and session_expiry in the database for this newly created user
    $update_session_stmt = $conn->prepare("UPDATE Associate SET session_id = ?, session_expiry = ? WHERE associate_id = ?");
    $update_session_stmt->bind_param("ssi", $new_session_id, $session_expiry, $_SESSION['associate_id']);
    $update_session_stmt->execute();
    $update_session_stmt->close();
    $conn->close();
        // Set secure cookie parameters
    $cookieParams = session_get_cookie_params();
    setcookie(session_name(), session_id(), time() + (30*86400), // Cookie expires in 1 day (86400 seconds)
              $cookieParams["path"], $cookieParams["domain"],
              true, // Secure - true for HTTPS
              true  // HttpOnly - true to prevent JavaScript access
    );

                $signup_class = "hidden";
                $signup_success = "<h2 style='color:#5E62DE;'>Signup successful</h2>";
                $message = "<span class='loading'>logging in....<span>";
                header("Refresh: 2; url=login.php");
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

// Function to generate a coupon code (example implementation)
function generateCouponCode() {
    return strtoupper(substr(md5(time() . rand()), 0, 8));
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
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto|Poppins" rel="stylesheet">
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
   @media (max-width: 576px) {
  .signin_mobile{
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
  }
}
     @media (min-width: 950px) {
  section {
    padding: 100px 450px;
  }
}

  </style>
   <script>
function toggleButton() {
    var checkbox = document.getElementById("agreeTerms");
    var button = document.getElementById("signUpButton");

    // Enable or disable the button based on the checkbox state
    if (checkbox.checked) {
        button.disabled = false;
    } else {
        button.disabled = true;
    }
}
</script>
</head>
<body>
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="../index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="../index.php#movies">Movies</a></li>
          <li><a class="nav-link scrollto" href="../index.php#series">Series</a></li>
          <li><a class="nav-link scrollto" href="../index.php#faq">FAQ's</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <main id="main">
   <section id="signup" class="contact section-bg" style="background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2 style="margin-bottom:0;">Join ScoobyTV Partner Program</h2>
            <?php echo $signup_success; ?>
            <p><?php echo $message; ?></p>
            <?php if (isset($_SESSION['otp_sent'])): ?>
                <form id="edit-email-form" action="signup.php" method="post" style="display:inline;">
                    <input type="hidden" name="edit_email" value="1">
                    <button type="submit" class="btn btn-link" id="editEmailButton">Edit email?</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="row <?php echo $signup_class; ?>">
            <div class="col-lg-12">
                <?php if (!isset($_SESSION['otp_sent'])): ?>
                <!-- Email and Password Form -->
                <form id="signup-form" action="signup.php" method="post" role="form" class="signup-email-form" style="background:linear-gradient(to bottom right, #716d6d, #141414);color:#fff;border-radius:20px;">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Full Name" required pattern="[A-Za-z\s]+" title="Please enter A-Z or a-z letters only." required>
                        </div>
                        <div class="col-md-12 form-group mt-2">
    <input type="tel" name="mobile" class="form-control" id="Mobile" placeholder="Mobile no" pattern="[6-9]\d{9}" title="Please enter a valid 10-digit mobile number" maxlength="10" required>

                        </div>
                        <div class="col-md-12 form-group mt-2">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Create Password" minlength="8" required>
                    </div>
                    <div class="form-group mt-3">
    
    <select id="state" name="state" class="form-control" required>
     <option value="Select State">Select State</option>
        <option value="Andhra Pradesh">Andhra Pradesh</option>
        <option value="Arunachal Pradesh">Arunachal Pradesh</option>
        <option value="Assam">Assam</option>
        <option value="Bihar">Bihar</option>
        <option value="Chhattisgarh">Chhattisgarh</option>
        <option value="Goa">Goa</option>
        <option value="Gujarat">Gujarat</option>
        <option value="Haryana">Haryana</option>
        <option value="Himachal Pradesh">Himachal Pradesh</option>
        <option value="Jharkhand">Jharkhand</option>
        <option value="Karnataka">Karnataka</option>
        <option value="Kerala">Kerala</option>
        <option value="Madhya Pradesh">Madhya Pradesh</option>
        <option value="Maharashtra">Maharashtra</option>
        <option value="Manipur">Manipur</option>
        <option value="Meghalaya">Meghalaya</option>
        <option value="Mizoram">Mizoram</option>
        <option value="Nagaland">Nagaland</option>
        <option value="Odisha">Odisha</option>
        <option value="Punjab">Punjab</option>
        <option value="Rajasthan">Rajasthan</option>
        <option value="Sikkim">Sikkim</option>
        <option value="Tamil Nadu">Tamil Nadu</option>
        <option value="Telangana">Telangana</option>
        <option value="Tripura">Tripura</option>
        <option value="Uttar Pradesh">Uttar Pradesh</option>
        <option value="Uttarakhand">Uttarakhand</option>
        <option value="West Bengal">West Bengal</option>
        <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
        <option value="Chandigarh">Chandigarh</option>
        <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
        <option value="Delhi">Delhi</option>
        <option value="Lakshadweep">Lakshadweep</option>
        <option value="Puducherry">Puducherry</option>
        <option value="Ladakh">Ladakh</option>
        <option value="Jammu and Kashmir">Jammu and Kashmir</option>
    </select>
</div>

                    <div class="form-group mt-2">
                        <select id="plan" name="country" class="form-control">
                           
                            <option value="India">India</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
    <label for="payment-option" style="color:#141414;margin-left:5px;margin-bottom:5px;color:#fff;">Payment Option <span style="color: #ff4b2b;">*</span></label>
    <select id="payment-option" name="payment_option" class="form-control" onchange="togglePaymentOptions()" required>
        <option value="">Select an option</option>
        <option value="bank">Bank Account Details</option>
        <option value="upi">UPI ID</option>
    </select>
</div>

<div id="bank-details" class="form-group mt-2" style="display:none;">
     <input type="text" id="bank_name" name="bank_name" class="form-control mt-1" placeholder="Bank Name">
    <input type="text" id="account_number" name="account_number" class="form-control mt-1" placeholder="Account Number">
    <input type="text" id="ifsc" name="ifsc" class="form-control mt-1" placeholder="IFSC Code">
</div>

<div id="upi-details" class="form-group mt-2" style="display:none;">
    <input type="text" id="upi-id" name="upi-id" class="form-control" placeholder="Enter your UPI ID">
    <div id="upi-validation-message" class="mt-2"></div>
</div>
<label for="payment-option" style="color:#141414;margin-left:5px;margin-bottom:5px;margin-top:15px;color:#fff;">INVITE CODE <span style="color: #ff4b2b;">*</span></label>
<div id="upi-details" class="form-group" style="">
    <input type="text" id="invite-code" name="invite-code" class="form-control" placeholder="Enter Invite Code" minlength="8" required>
    <div id="upi-validation-message" class="mt-2"></div>
</div>
<script>
    function validateUPI(upiId) {
        var upiRegex = /^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/;
        return upiRegex.test(upiId);
    }

    // Example usage
    var upiInput = document.getElementById('upi-id');
    upiInput.addEventListener('input', function() {
        var upiId = upiInput.value;
        var messageElement = document.getElementById('upi-validation-message');
        
        if (validateUPI(upiId)) {
            messageElement.textContent = "Valid UPI ID";
            messageElement.style.color = "green";
        } else {
            messageElement.textContent = "Invalid UPI ID";
            messageElement.style.color = "red";
        }
    });
</script>
<script>
    function togglePaymentOptions() {
        var paymentOption = document.getElementById('payment-option').value;
        var bankDetails = document.getElementById('bank-details');
        var upiDetails = document.getElementById('upi-details');
        
        if (paymentOption === 'bank') {
            bankDetails.style.display = 'block';
            upiDetails.style.display = 'none';
        } else if (paymentOption === 'upi') {
            bankDetails.style.display = 'none';
            upiDetails.style.display = 'block';
        } else {
            bankDetails.style.display = 'none';
            upiDetails.style.display = 'none';
        }
    }
</script>


                     <div class="form-check d-flex justify-content-center mt-3" style="align-items: flex-end;">
        <input class="form-check-input" type="checkbox" id="agreeTerms" required onclick="toggleButton()">
        <label class="form-check-label ms-2" for="agreeTerms" style="color:#fff;">
            I agree to all <a href="apply.php" target="_blank" style="color:#4db8ff;">Terms</a>
        </label>
    </div>
                    <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-buy" id="signUpButton" disabled>Sign up</button>
                    </div>
                     <div class="text-center mt-3">
                <span class="">Already have an account?</span><a href="login.php" class="text-blue"> Sign in</a>
              </div>
                    
                </form>
                <?php else: ?>
                <!-- OTP Form -->
                <form id="otp-form" action="signup.php" method="post" role="form" class="signup-email-form">
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="otp" id="otp" placeholder="Enter OTP" maxlength="6" required>
                    </div>
                    <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-buy" id="verifyOtpButton">Verify OTP</button>
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
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>
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
        } else if (price == 119) {
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
