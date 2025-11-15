<?php
session_start();
$message = '';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'conn.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs (example)
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $emailmobile = filter_input(INPUT_POST, 'emailmobile', FILTER_SANITIZE_EMAIL);
    $product_comparison_rating = filter_input(INPUT_POST, 'product-comparison-rating', FILTER_VALIDATE_INT);
    $pricing_rating = filter_input(INPUT_POST, 'pricing-rating', FILTER_VALIDATE_INT);
    $user_experience_rating = filter_input(INPUT_POST, 'user-experience-rating', FILTER_VALIDATE_INT);
    $overall_feedback_rating = filter_input(INPUT_POST, 'overall-feedback-rating', FILTER_VALIDATE_INT);
    $suggestions = filter_input(INPUT_POST, 'suggestions', FILTER_SANITIZE_STRING);

    // Check for required fields
    if (!$full_name || !$emailmobile) {
        die('<h3 style="text-transform: inherit;color:#ff4b2b;">Error: Full Name and Email/Mobile are required.</h3>');
    }

    // Insert feedback into database
    $sql = "INSERT INTO feedback (full_name, emailmobile) VALUES ('$full_name', '$emailmobile')";
    
    if (mysqli_query($conn, $sql)) {
        $message = '<h3 style="text-transform: inherit;color:#5E62DE;">Feedback submitted successfully!</h3>';
        $hiddenClass = 'hidden';
        // Send email to admin
        $mail = new PHPMailer();
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
            $mail->addAddress('yoitv278@gmail.com');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Feedback Received';
            $mail->Body = "<p>New feedback has been received:</p>
                           <p><strong>Full Name:</strong> $full_name</p>
                           <p><strong>Email/Mobile:</strong> $emailmobile</p>
                           <p><strong>Product Comparison Rating:</strong> $product_comparison_rating</p>
                           <p><strong>Pricing Rating:</strong> $pricing_rating</p>
                           <p><strong>User Experience Rating:</strong> $user_experience_rating</p>
                           <p><strong>Overall Feedback Rating:</strong> $overall_feedback_rating</p>
                           <p><strong>Suggestions:</strong> $suggestions</p>";

            $mail->send();
        } catch (Exception $e) {
            $message = '<h3 style="text-transform: inherit;color:#ff4b2b;">Mailer Error: ' . $mail->ErrorInfo . '</h3>';
        }

    } else {
        $message = "<h3 style='text-transform: inherit;color:#ff4b2b;'>Error: " . mysqli_error($conn) . "</h3>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="robots" content="noindex, nofollow">

  <title>Feedback</title>
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

    <!-- Gigle -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8084887510639288"
     crossorigin="anonymous"></script>

     
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
    .contact .my-email-form {
        /* box-shadow: 0 0 30px rgb(140 0 255 / 64%); */
        padding: 30px;
        background: #fff;
    }
    .contact .my-email-form input {
        padding: 10px 15px;
    }
    .contact .my-email-form input, .contact .my-email-form textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
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

  label {
    color: #141414;
    display: inline-block;
    font-weight: 500;
}
  </style>
   
</head>
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
            <h2 class="" style="text-transform: inherit;">Feedback</h2>
           <?php echo $message; ?>
            <p></p>
        </div>
        <div class="row <?php echo $hiddenClass; ?>">
            <div class="col-lg-12">
                
                <!-- Feedback Form -->
                <form id="signup-form" action="" method="post" role="form" class="my-email-form">
                  <div class="row">
                      <div class="col-md-12 form-group">
                          <input type="text" name="full_name" class="form-control" id="name" placeholder="Full Name" required>
                      </div>
                      <div class="col-md-12 form-group mt-3">
                          <input type="text" class="form-control" name="emailmobile" id="email" placeholder="Email or mobile no." required>
                      </div>
                  </div>
                  
                  <div class="form-group mt-3">
                      <label for="product-comparison">How would you rate our product in comparison to other OTT service providers?</label>
                      <div id="product-comparison" class="star-rating text-center">
                          <span class="star" data-value="1">&#9733;</span>
                          <span class="star" data-value="2">&#9733;</span>
                          <span class="star" data-value="3">&#9733;</span>
                          <span class="star" data-value="4">&#9733;</span>
                          <span class="star" data-value="5">&#9733;</span>
                          <input type="hidden" name="product-comparison-rating" id="product-comparison-rating" value="">
                      </div>
                  </div>
              
                  <div class="form-group mt-3">
                      <label for="pricing">Does the pricing of our service meet your expectations and provide good value for money?</label>
                      <div id="pricing" class="star-rating text-center">
                          <span class="star" data-value="1">&#9733;</span>
                          <span class="star" data-value="2">&#9733;</span>
                          <span class="star" data-value="3">&#9733;</span>
                          <span class="star" data-value="4">&#9733;</span>
                          <span class="star" data-value="5">&#9733;</span>
                          <input type="hidden" name="pricing-rating" id="pricing-rating" value="">
                      </div>
                  </div>
              
                  <div class="form-group mt-3">
                      <label for="user-experience">How user-friendly do you find our platform?</label>
                      <div id="user-experience" class="star-rating text-center">
                          <span class="star" data-value="1">&#9733;</span>
                          <span class="star" data-value="2">&#9733;</span>
                          <span class="star" data-value="3">&#9733;</span>
                          <span class="star" data-value="4">&#9733;</span>
                          <span class="star" data-value="5">&#9733;</span>
                          <input type="hidden" name="user-experience-rating" id="user-experience-rating" value="">
                      </div>
                  </div>
              
                  <div class="form-group mt-3">
                      <label for="overall-feedback">What is your overall feedback on our product?</label>
                      <div id="overall-feedback" class="star-rating text-center">
                          <span class="star" data-value="1">&#9733;</span>
                          <span class="star" data-value="2">&#9733;</span>
                          <span class="star" data-value="3">&#9733;</span>
                          <span class="star" data-value="4">&#9733;</span>
                          <span class="star" data-value="5">&#9733;</span>
                          <input type="hidden" name="overall-feedback-rating" id="overall-feedback-rating" value="" required>
                      </div>
                  </div>
                  <div class="form-group mt-3">
                    <label for="suggestions">Any suggestions or recommendations?</label>
                    <textarea class="form-control" name="suggestions" id="suggestions" rows="4" placeholder="Your words matter a lot for us" required></textarea>
                </div>
            
              
                  <div class="btn-wrap text-center mt-3">
                      <button type="submit" class="btn btn-buy">Submit</button>
                  </div>
              </form>
              
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      const starRatings = document.querySelectorAll('.star-rating');
                      starRatings.forEach(rating => {
                          const stars = rating.querySelectorAll('.star');
                          stars.forEach(star => {
                              star.addEventListener('click', () => {
                                  const value = star.getAttribute('data-value');
                                  const hiddenInput = rating.querySelector('input[type="hidden"]');
                                  hiddenInput.value = value;
                                  stars.forEach(s => {
                                      if (s.getAttribute('data-value') <= value) {
                                          s.classList.add('glow');
                                      } else {s.classList.remove('glow');  }
                                  });
                              });
                          });
                      });
                  });
                  const style = document.createElement('style');
                  style.innerHTML = `
                      .star {
                          font-size: 2em;
                          color: gray;
                          cursor: pointer;
                      }
                      .star.glow {
                          color: gold;
                      }
                  `; document.head.appendChild(style);
              </script>
              
                </div>
        </div>
    </div>
</section>
  </main>
 <!-- ======= Footer ======= -->
 <footer id="footer">

  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-lg-3 col-md-6 footer-contact">
          <!-- <h3>ScoobyTV</h3> -->
          <a href="index.php" class="logo mb-5">
            <img src="assets/img/logo.png" alt="" class="img-fluid"  style="width: 80%;margin-bottom: 30px;">
          </a>

          <p>
            <!--<strong>Phone:</strong><br>-->
            <strong>Email:</strong> scoobytv49@gmail.com<br>
          </p>
        </div>

        <div class="col-lg-2 col-md-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="index.php">Home</a></li>
           
            <li><i class="bx bx-chevron-right"></i> <a href="services.html">Services</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="terms.html">Terms</a></li>
           
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="#movies">Content</a></li>
            
            <li><i class="bx bx-chevron-right"></i> <a href="#pricing">Pricing</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="support.php">Support</a></li>
          
          </ul>
        </div>

        <div class="col-lg-4 col-md-6 footer-newsletter">
          <h4>About us</h4>
          <p>ScoobyTV provide premium content at affordable rates. Enjoy Ads-free streaming in Full HD on TV, mobile, and laptop. Access our services through the web or our app for a seamless viewing experience. <span style="color: #ff416c;font-weight: bold;">Premium Entertainment, Affordably Delivered.</span></p>
          <!-- <form action="" method="post">
            <input type="email" name="email"><input type="submit" value="Subscribe">
          </form> -->
        </div>

      </div>
    </div>
  </div>

  <div class="container">

    <div class="copyright-wrap d-md-flex py-4">
      <div class="me-md-auto text-center text-md-start">
        <div class="copyright">
          &copy; Copyright <strong><span>ScoobyTV</span></strong>. All Rights Reserved
        </div>
       
      </div>
     
    </div>

  </div>
</footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>