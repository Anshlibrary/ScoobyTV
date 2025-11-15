<?php
session_start();

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'conn.php'; // Include your database connection

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

// Include your database connection script
include 'conn.php';
$message = '';
// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'forgot_password') {
    $email = $_POST['email'];
    // Prepare and execute a query to fetch the user details
    $stmt = $conn->prepare("SELECT first_name, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($first_name, $username, $password);
    $stmt->fetch();
    $stmt->close();

    if ($first_name && $username && $password) {
        // Prepare the email content
        $to = $email;
        $subject = "ScoobyTV Password Recovery";
        $message = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                }
                .container {
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .header {
                    font-size: 1.2em;
                    margin-bottom: 20px;
                }
                .content {
                    margin-bottom: 20px;
                }
                .footer {
                    font-size: 0.9em;
                    color: #555;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    Hi $first_name,
                </div>
                <div class='content'>
                    We noticed that you are experiencing issues logging into your ScoobyTV account. Please use the following credentials to log in to your account:<br><br>
                    <b>Email:</b> $email<br>
                    <b>Username:</b> $username<br>
                    <b>Password:</b> $password<br><br>
                    This is an automated message. Please do not reply to it. If the problem persists, kindly initiate a chat at ScoobyTV or contact us at scoobytv49@gmail.com with a screenshot of the error.
                </div>
                <div class='footer'>
                    Thank you for your understanding and cooperation.<br><br>
                    Best regards,<br>
                    The ScoobyTV Team
                </div>
            </div>
        </body>
        </html>
        ";

        // PHPMailer setup
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'scoobytv49@gmail.com'; // Replace with your Gmail address
            $mail->Password = 'pnrejfrmudnytlss'; // Replace with your Gmail password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true); 
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Send email
            if ($mail->send()) {
                $message ="An email has been sent to your registered email address with your credentials.";
            } else {
                $message ="Failed to send email.";
            }
        } catch (Exception $e) {
            $message ="Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
       $message ="No user found with this email address.";
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
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
   <section id="plan-selector" class="contact">
      <div class="container" data-aos="fade-up">
        <div class="tick-container hidden">
        
          <svg class="tick" viewBox="0 0 52 52">
              <circle class="tick-circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="tick-check" fill="none" d="M14 27l7 7 16-16"/>
          </svg>
      </div>
      <div class="section-title text-center">
       
          <h2><?php echo $message; ?></h2>
          <p></p>
      </div>    
 <a href="index.php" class="btn-buy">Go to Home</a>
</div>
    </section>
  

</main><!-- End #main -->

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
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
