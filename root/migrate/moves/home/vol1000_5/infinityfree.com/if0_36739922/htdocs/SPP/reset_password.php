<?php
session_start();
// Check if the user is already logged in
if (isset($_SESSION['associate_id'])) {
    header("Location: dashboard.php");
    exit;
}
if (isset($_POST['edit_email'])) {
    session_unset();
    session_destroy();
    header("Location: reset_password.php");
    exit;
}
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../conn.php'; // Include your database connection

$message = '';
$msgerr = '';

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] == 'forgot_password') {
        $email = $_POST['email'];

        // Prepare and execute a query to fetch the user details
        $stmt = $conn->prepare("SELECT fullname, email FROM Associate WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($fullname, $email);

        if ($stmt->fetch()) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;

            // Prepare the OTP email content
            $to = $email;
            $subject = "ScoobyTV Associate OTP";
            $message = "

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
        <p style='color: #222;'>Hi $fullname, <br>We noticed that you are experiencing issues logging into your ScoobyTV Associate account. Please use the Below OTP to reset your password:</p>
        <div class='otp'>
            $otp
        </div>
        <div> This is an automated message. Please do not reply to it. If the problem persists, kindly initiate a chat at ScoobyTV or contact us at scoobytv49@gmail.com with a screenshot of the error.
        </div>
        <div class='footer'>
            Best regards,<br>
            The ScoobyTV Team
        </div>
    </div>
</body>
</html>
            ";

            // Send OTP email via PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'scoobytv49@gmail.com';
                $mail->Password = 'pnrejfrmudnytlss';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
                $mail->addAddress($to);

                $mail->isHTML(true); 
                $mail->Subject = $subject;
                $mail->Body = $message;

                if ($mail->send()) {
                    $message = "An OTP has been sent to your email. <br> $to";
                   $_SESSION['otp_sent'] = true; // Set OTP sent flag
                } else {
                    $message = "Failed to send OTP email.";
                    $msgerr = 'hidden';
                }
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                $msgerr = 'hidden';
            }
        } else {
            $message = "No user found with this email address.";
            $msgerr = 'hidden';
        }
        $stmt->close();
    }

    // Handle OTP verification and showing password reset form
    if ($_POST['action'] == 'verify_otp') {
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['otp']) {
            // OTP is correct, show the new password form
            $message = "OTP verified. Please enter a new password.";
            $_SESSION['otp_verified'] = true;
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    }

    // Handle password reset
    if ($_POST['action'] == 'reset_password' && isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash the new password for security
        $email = $_SESSION['email'];
       

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE Associate SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        if ($stmt->execute()) {
             $_SESSION['password_reset'] = true;
            $message = "Your password has been updated successfully.";
            session_destroy(); // Clear the session data
        } else {
            $message = "Failed to update the password.";
        }
        $stmt->close();
    }
}
?>

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
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f4f4f4;
    margin: 0;
}
 .payment-success {
    background: linear-gradient(135deg, #37b24d, #845ef7, #cc5de8);
    color: #ffffff; 
    text-align: center;
    }

    .btn-sign{
background: linear-gradient(to right, #ff416c, #ff4b2b);
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
.btn-sign:hover{
background: linear-gradient(42deg, #5846f963 0%, #7b27d852 100%);
  color: #fff;
}
.btn-buy{
background: linear-gradient(42deg, #5846f963 0%, #7b27d852 100%);
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
.btn-buy:hover{
background: linear-gradient(to right, #ff416c, #ff4b2b);
  color: #fff;
}
.text-pay{
  color: #0c7900;
  font-weight: bold;
  margin-bottom: 0;
}
.success-color{
  color: #00ff00ad;
}

.tick-container {
    width: 100px;
    height: 100px;
}

.tick {
    width: 100%;
    height: 100%;
}

.tick-circle {
    stroke: #038127;
    stroke-width: 2;
    stroke-dasharray: 157;
    stroke-dashoffset: 157;
    animation: circle-animation 1s ease-in-out forwards;
}
.tick-container {
      width: 120px;
      height: 120px;
      margin: 0 auto;
      margin-bottom: 40px;
    }

.tick-check {
    stroke: #038127;
    stroke-width: 4;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: check-animation 0.5s ease-in-out 1s forwards;
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

@keyframes circle-animation {
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes check-animation {
    to {
        stroke-dashoffset: 0;
    }
}
.show{
  display: block;
}
.hidden{
  display: none;
}

</style>
</head>

<body class="payment-success">
<main id="main" class="">
 <h2>ScoobyTV Associate Password Reset</h2>
   <section class="contact" style="padding: 20px 0;">
      <div class="container" data-aos="fade-up">

     
<!-- HTML Forms -->
<p><?php echo $message; ?></p>
<!-- Forgot Password Form -->
<?php if (!isset($_SESSION['otp_verified']) && !isset($_SESSION['otp_sent'])){ ?>
<form method="POST" role="form" class="signup-email-form" id="sendOTP_form">

<div class="row">
 <input type="hidden" name="action" value="forgot_password">
                        <div class="col-md-12 form-group">
                            <input type="email" name="email" placeholder="Enter your associate email" class="form-control" id="name" required>
                        </div>
                          <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-sign" id="sendOTP_Button">Send OTP</button>
                    </div>
 </div>


</form>
   <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("sendOTP_form").addEventListener("submit", function(event) {
            var button = document.getElementById("sendOTP_Button");
            button.disabled = true;
            button.classList.add("loading");
        });
    });
    </script>
<!-- OTP Verification Form -->
<?php } else if (isset($_SESSION['otp_sent']) && $_SESSION['otp_sent'] && !isset($_SESSION['otp_verified'])){ ?>
 <form id="edit-email-form" method="post" style="display:inline;">
                    <input type="hidden" name="edit_email" value="1">
                    <button type="submit" class="btn btn-buy mb-3" id="editEmailButton" style="color: #fff;padding: 0px 10px;border-radius: 50px;">Edit email?</button>
</form>

<form method="POST" role="form" class="signup-email-form" id="verifyOTP_form">  
    <div class="row"> 
    <input type="hidden" name="action" value="verify_otp">
                        <div class="col-md-12 form-group">
                            <input  type="text" name="otp" placeholder="Enter OTP" class="form-control" id="otp" required>
                        </div>
                          <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-sign" id="verifyotp">Verify OTP</button>
                    </div>
 </div>
</form>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("verifyOTP_form").addEventListener("submit", function(event) {
            var button = document.getElementById("verifyotp");
            button.disabled = true;
            button.classList.add("loading");
        });
    });
    </script>
<?php } else if(!isset($_SESSION['password_reset']) ){ ?>

<!-- Password Reset Form -->
<form method="POST" role="form" class="signup-email-form">
 <div class="row"> 
    <input type="hidden" name="action" value="reset_password" id="resetPw_form">
  <div class="col-md-12 form-group d-flex" style="gap:3px;">
     <input type="password" name="new_password" placeholder="Enter new password" id="password" class="form-control" required>
     <div class="input-group-append" onclick="togglePasswordVisibility()" style="align-content: center;background: blueviolet;padding: 10px;border-radius: var(--bs-border-radius);">
            <span id="toggle-icon" class="fas fa-eye"></span>
        </div>
 </div>
 <div class="btn-wrap text-center mt-3">
    <button type="submit" class="btn btn-sign" id="reset_password">Reset Password</button>
</div>
</form>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
 <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("resetPw_form").addEventListener("submit", function(event) {
            var button = document.getElementById("reset_password");
            button.disabled = true;
            button.classList.add("loading");
        });
    });
    </script>
<?php } else { ?>
        <div class="tick-container">
        
          <svg class="tick <?php echo $msgerr; ?>" viewBox="0 0 52 52">
              <circle class="tick-circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="tick-check" fill="none" d="M14 27l7 7 16-16"/>
          </svg>
      </div>
      <div class="section-title text-center">
       
          <p></p>
      </div>  
       <a href="login.php" class="btn-buy">Login Now</a>  <br>
<?php } ?>

  <div class="btn-wrap text-center mt-3">
                       <a href="pw_goback.php" class="btn-buy">Go Back</a>
                    </div>


</div>
    </section>
  

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
<script>
  document.addEventListener("DOMContentLoaded", function() {
  // Function to show the tick animation
    function showTickAnimation(){
      document.querySelector('.tick-container').classList.remove('hidden');
      document.querySelector('.tick-container').classList.add('show');
     }
  //Show the tick animation immediately on page load
    setTimeout(showTickAnimation, 1000);
});
</script>
</body>
</html>
