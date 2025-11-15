<?php

session_start();
include '../conn.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$first_name = '';
$allocated_ip = '';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch the first name and allocated IP from the database
    $query = "SELECT first_name, allocated_ip FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($first_name, $allocated_ip);
        $stmt->fetch();
        $stmt->close();
    }
}

// Assuming you have a series_id and the file name for the current page
$series_id = 1; // Replace with the actual series ID or fetch dynamically
$file_name = 'love_next_door_2024.php';

// Update the view count or insert a new entry if it doesn't exist
$query = "SELECT views_count FROM blog_views WHERE blog_id = ? AND file_name = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('is', $series_id, $file_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Record exists, update the view count
        $stmt->bind_result($views_count);
        $stmt->fetch();
        $views_count++;

        $update_query = "UPDATE blog_views SET views_count = ?, last_viewed = NOW() WHERE blog_id = ? AND file_name = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('iis', $views_count, $series_id, $file_name);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Record doesn't exist, insert a new row
        $insert_query = "INSERT INTO blog_views (blog_id, file_name, views_count) VALUES (?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('is', $series_id, $file_name);
        $insert_stmt->execute();
        $insert_stmt->close();
        $views_count = 1; // Since it's a new record
    }

    $stmt->close();
} else {
    $views_count = 0; // Default value if no record found
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Love Next Door 2024</title>
  <meta content="" name="Love Next Door 2024">
  <meta content="" name="Love Next Door 2024">

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
        /* overflow: hidden; */
    }
    .swiper-container {
        width: 90%;
        height: 60%;
    }/*Custom slider css*/
    .poster-img {
  max-width: 100%;
  height: auto;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

 /*Custom slider css*/
    .blog-header{
background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 20px 0;
    }
    .blog-body{
background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 20px 0;
    }
    .breadcrumbs{
        margin-top:0;
    }
    .logo-img{
        max-height: 40px;
    }
     .my-breadcrumb-list {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .my-breadcrumb-list li {
    display: inline;
    margin-right: 10px;
  }
.testimonial-item {
  text-align: center;
}

@media (max-width: 768px) {
  .swiper-container {
    display: none;
  }
  .testimonial-item {
    display: inline-block;
    width: calc(50% - 10px);
    margin: 0 5px;
    vertical-align: top;
    text-align: center;
  }
}
.hidden{
    display:none;
}
@media (max-width: 576px) {
  .testimonial-item {
    width: calc(100% - 10px);
    margin: 0 5px;
  }
}

@media (min-width: 992px) {
  .box {
    max-width: 400px; /* Adjust the maximum width of the boxes */
    margin: 0 auto; /* Center align the boxes */
  }
}
.subscriptions-icons {
  display: flex; /* Align icons in a row */
  justify-content: center;
    align-items: center;
    padding: 20px;   
}
.subscriptions-icons img {
  width: 30px; /* Set the same width for all icons */
  margin-right: 10px; /* Add some space between icons */
}
.offerbtn-disabled{
background: linear-gradient(to bottom, #4a4b4d, #2c2d2f);
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
 @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }

        @keyframes blink {
            50% { border-color: transparent; }
        }

        #animated-text {
            overflow: hidden;
            white-space: nowrap;
            border-right: 0.15em solid orange;
            font-size: 1.5em;
            animation:
                typing 4s steps(30, end),
                blink 0.75s step-end infinite;
        }
@media (max-width: 576px) {
  .signin_mobile{
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
  }
}
</style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center justify-content-between">
      <!--<h1 class="logo"><a href="index.html"></a></h1> Text logo -->
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="../index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="../index.php#movies">Movies</a></li>
          <li><a class="nav-link scrollto" href="../index.php#series">Series</a></li>
          <li><a class="nav-link scrollto " href="../index.php#pricing">Pricing</a></li>
          <li><a class="nav-link scrollto" href="../index.php#faq">FAQ's</a></li>
          
          <li>
            <?php if ($is_logged_in): ?>
                        <a class="getstarted scrollto signin_mobile" href="../profile.php"><?php echo htmlspecialchars($first_name); ?></a>
                    <?php else: ?>
                        <a class="getstarted scrollto signin_mobile" href="../signin.php">Sign In</a>
                    <?php endif; ?>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->


  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
    <br>
    <br>
 

    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body">
  <div class="container">
   <h2 style="text-align: left;">Love Next Door (2024)</h2> <p style="text-align: right;"> views: <?php echo $views_count; ?></p>
  <h3 style="text-align: left;"></h3>
  
  <p>Choi Seung Hyo is widely regarded as one of the most promising young architects in Korea, leading his own architecture studio, "In Atelier." Not only is he exceptional in his craft, but he's also known for his striking appearance and charming demeanor. However, there are certain memories Choi Seung Hyo wishes he could erase, most of which involve Bae Seok Ryu. Their mothers became close friends when the two were just four years old, which led to them spending a lot of time together, including awkward moments like bathing together at a women's bathhouse. Now, as adults, Choi Seung Hyo and Bae Seok Ryu cross paths once again.

Bae Seok Ryu had a life that seemed to unfold effortlessly. Throughout her school years, she consistently ranked at the top of her class and approached everything with passion and enthusiasm. After graduating from university, she secured a position at a prestigious company and thrived as a project manager. Yet, despite her success, she unexpectedly left her job and has been unemployed ever since. Now, she finds herself reunited with Choi Seung Hyo.
.</p>
  
  <img style="display: block; margin-left: auto; margin-right: auto; max-width: 100%; height: auto;border-radius:10px;cursor: pointer;" src="../assets/img/series/love_next_door.webp" alt="Love Next Door (2024)" data-toggle="modal" data-target="#signupModal">
  </div>

  <!-- Modal -->


  <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="upiModalLabel" >Signup to Continue</h5>
               <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 20px;"></button>-->
            </div>
            <div class="modal-body">
                
                <div class="mb-3">
                     <div class="btn-wrap text-center mt-3">
                        <button type="submit" class="btn btn-buy" id="signUpButton" onclick="window.location.href='index.php#pricing';">Sign up</button>
                    </div>
                   
                </div>
              
                
              
            </div>
            
        </div>
    </div>
</section>


  </main><!-- End #main -->

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
          <!-- <div class="credits">
           
            Designed by <a href=""></a>
          </div> -->
        </div>
        <div class="social-links text-center text-md-right pt-3 pt-md-0">
          <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
          <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
          <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
          <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
          <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
        </div>
      </div>

    </div>
  </footer><!-- End Footer -->
  
 <!--
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div> -->

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

    <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



</body>

</html>