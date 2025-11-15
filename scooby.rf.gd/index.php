<?php
session_start();
include 'conn.php';

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>ScoobyTV</title>
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
    pointer-events: none;
    opacity: 0.6; /* Makes the button appear disabled */
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

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center justify-content-between">
      <!--<h1 class="logo"><a href="index.html"></a></h1> Text logo -->
      <a href="index.php" class="logo"><img src="assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="#content">Content</a></li>
          <li><a class="nav-link scrollto" href="#series">Series</a></li>
          <li><a class="nav-link scrollto " href="#pricing">Pricing</a></li>
          <li><a class="nav-link scrollto" href="#faq">FAQ's</a></li>
          
          <li>
            <?php if ($is_logged_in): ?>
                        <a class="getstarted scrollto signin_mobile" href="profile.php"><?php echo htmlspecialchars($first_name); ?></a>
                    <?php else: ?>
                        <a class="getstarted scrollto signin_mobile" href="signin.php">Sign In</a>
                    <?php endif; ?>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center">
    
    <div class="container-fluid" data-aos="fade-up">
      <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 pt-3 pt-lg-0 order-2 order-lg-1 d-flex flex-column justify-content-center">
          <h1>Bettter OTT Experience With Us</h1>
         
          <h2>We are here to provide you premium content at minimal cost</h2>
           <h2 id="animated-text">NAVARATRI SPECIAL SALE is LIVE</h2>
         <!--   <h2 style="font-size: 16px;color: #32cd5c;"><i>Use Coupon - VEDANTA50OFF at checkout, and get flat ₹10 OFF!</i></h2>
    <h2 style="font-size: 16px;" class="<?php echo $is_logged_in ? 'hidden' : ''; ?>"><i>1 day free trial, Offer Ends on 15th July</i></h2>-->
          <div>
<a href="<?php echo $is_logged_in ? 'http:stv1.xyz:53575' : '#pricing'; ?>" class="btn-get-started scrollto"><?php echo $is_logged_in ? 'Watch Now' : 'Subscribe Now'; ?></a>
          </div>
        </div>
        <div class="col-xl-4 col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="150">
          <img src="assets/img/logo_ott.png" class="img-fluid animated" alt="">
        </div>
      </div>
    </div>

  </section><!-- End Hero -->

  <main id="main">

    
    <section id="testimonials" class="testimonials section-bg" style="background-color: #141414;padding: 10px 0;margin: 10px 10px;">
      <div class="container" data-aos="fade-up" id="content">
        <div class="section-title" style="padding-bottom: 0px;">
          <h2>Trending Content</h2>
          <p></p>
        </div>
        
        <a href="#pricing"> 
        <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper" id="content-slider">
        
          </div>
          <div class=""></div>
        </div>
        </a>
    
      </div>

      <div class="container mt-5" data-aos="fade-up" id="series">
        <div class="section-title" style="padding-bottom: 0px;">
          <h2>Trending Series</h2>
          <p></p>
        </div>
        
         <a href="#pricing"> 
        <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper" id="series-slider">
        
          </div>
          <!--<div class="swiper-pagination"></div>-->
        </div>
        </a>
    
      </div>
    </section> 

    <!-- ======= Counts Section ======= -->
    <section id="counts" class="counts">
      <div class="container">

        <div class="row counters">

          <div class="col-lg-3 col-6 text-center">
            <span data-purecounter-start="0" data-purecounter-end="2032" data-purecounter-duration="1" class="purecounter"></span>
            <p>Content</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
            <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
            <p>Series</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
            <span data-purecounter-start="0" data-purecounter-end="998" data-purecounter-duration="1" class="purecounter"></span>
            <p>Users</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
            <span data-purecounter-start="0" data-purecounter-end="1300" data-purecounter-duration="1" class="purecounter"></span>
            <p>Watch Time</p>
          </div>

        </div>

      </div>
    </section><!-- End Counts Section -->

   

    <!-- ======= Features Section ======= -->
    <section id="features" class="features">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Features</h2>
          <p>Enjoy seamless, ad-free entertainment on TV, mobile, and laptop. Watch in stunning FHD and HD at 1080P and 720P with our Starter plan. Access via web or enhance with our app for ultimate convenience.</p>
        </div>

        <div class="row">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column align-items-lg-center">
            <div class="icon-box mt-5 mt-lg-0" data-aos="fade-up" data-aos-delay="100">
              <i class="bx bx-receipt"></i>
              <h4>TV, Mobile & Laptop</h4>
              <p>All devices support - Watch your favorite shows in TV, Mobile & Laptop</p>
            </div>
            <div class="icon-box mt-5" data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-cube-alt"></i>
              <h4>Ads-Free</h4>
              <p>No Disturbing Ads</p>
            </div>
            <div class="icon-box mt-5" data-aos="fade-up" data-aos-delay="300">
              <i class="bx bx-images"></i>
              <h4>FHD & HD</h4>
              <p>Watch at 1080P and 720P with Starter plan only</p>
            </div>
            <div class="icon-box mt-5" data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-shield"></i>
              <h4>Web and App Support</h4>
              <p>No need to install any app, However if you want app experience, we also provide App</p>
            </div>
          </div>
          <div class="image col-lg-6 order-1 order-lg-2 " data-aos="zoom-in" data-aos-delay="100">
            <img src="assets/img/features.webp" alt="" class="img-fluid">
          </div>
        </div>

      </div>
    </section><!-- End Features Section -->

   

    <!-- ======= Pricing Section ======= -->
    <section id="pricing" class="pricing section-bg" style="background: linear-gradient(to bottom right, #3b3b3b, #202020);color:#fff;">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>ScoobyTV Subscriptions</h2>
          <p>Well Curated Plans for you 🙂</p>
        </div>

        <div class="row">


          <div class="col-lg-4 col-md-8 mt-4 mt-md-0" data-aos="fade-up" data-aos-delay="200">
            <div class="box featured">
              <h3 style="background: linear-gradient(to bottom, #7FFF00, #32CD32);">Starter (51% OFF)</h3>
              <h4 style="margin-bottom: 0;"><sup>₹</sup>99<span> / month</span></h4>
              <ul style="margin-bottom: 0;">
                <li class="na" style="color:#e03131;">₹199/mon</li>
                <li>TV, Laptop, & Mobile</li>
                <li>Ads-Free</li>
                <li>No ⬇️</li>
                <li>All Premium content</li>
                <li>1 Device</li>
                <li style="padding-bottom:0;">HD Quality</li>
                <li style="padding-bottom:0;"> 
                  <div class="subscriptions-icons">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0072.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0071.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0127.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0073.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0074.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0079.svg">
                  </div>
                  <!-- Add more icons as needed -->
                  </li>
              </ul>
              <div class="btn-wrap" style="margin-top: 0;background: #adb5bd;">
                <a href="/signup.php?price=99" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-8 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
            <div class="box">
              <span class="advanced">Gold plan</span>
              <h3 style="background: linear-gradient(to bottom, #FFD700, #FFA500);">Premium (59% OFF)</h3>
              <h4 style="margin-bottom: 0;"><sup>₹</sup>139<span> / month</span></h4>
              <ul style="margin-bottom: 0;">
                <li class="na" style="color:#e03131;">₹289/mon</li>
                <li>TV, Laptop, & Mobile</li>
                <li>Ads-Free</li>
                <li>Unlimited ⬇️</li>
                <li>All Premium content</li>
                <li>2 Device</li>
                <li style="padding-bottom:0;">FHD & HD Quality</li>
                <li style="padding-bottom:0;"> 
                  <div class="subscriptions-icons">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0072.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0071.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0127.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0073.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0074.svg">
                    <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0079.svg">
                  </div>
                  <!-- Add more icons as needed --></li>
              </ul>
              <div class="btn-wrap" style="margin-top: 0;background: #adb5bd;">
                <a href="/signup.php?price=139" class="btn-buy">Buy Now</a>
              </div>
            </div>

          </div>
   

           
<div class="col-lg-4 col-md-8 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
  <div class="box">
    <span class="advanced">Free Trial</span>
    <h3 style="background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%);color:#fff;">1 day trial </h3>
    <h4 style="margin-bottom: 0;"><sup>₹</sup>0<span></span></h4>
    <ul style="margin-bottom: 0;">
      <li class="na" style="color:#e03131;">₹199/mon</li>
      <li>TV, Laptop, & Mobile</li>
      <li>Ads-Free</li>
      <li>No ⬇️</li>
      <li>All Premium content</li>
      <li>1 Device</li>
      <li style="padding-bottom:0;">HD Quality</li>
      <li style="padding-bottom:0;"> 
        <div class="subscriptions-icons">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0072.svg">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0071.svg">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0127.svg">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0073.svg">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0074.svg">
          <img class="Subscriptions_img__2dyha" alt="Subscription" src="https://jep-asset.akamaized.net/jiocom/static/images/Z0079.svg">
        </div>
        <!-- Add more icons as needed --></li>
    </ul>
    <div class="btn-wrap" style="margin-top: 0;background: #adb5bd;">
     <a href="trial.php" class="btn-buy <?php echo $is_logged_in ? 'offerbtn-disabled' : ''; ?>">Start Trial</a>

      <!-- <button href="" class="offerbtn-disabled" disabled>Start Trial</button>-->
      
    </div>
  </div>
  
</div>


        </div>

      </div>
    </section><!-- End Pricing Section -->

    <!-- ======= Frequently Asked Questions Section ======= -->
    <section id="faq" class="faq" style="color: #3b3b3b;">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Frequently Asked Questions</h2>
          <p> </p>
        </div>

        <div class="faq-list">
          <ul>
            <li data-aos="fade-up" data-aos="fade-up" data-aos-delay="100">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" class="collapse" data-bs-target="#faq-list-1">What makes ScoobyTV different from other OTT platforms?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-1" class="collapse show" data-bs-parent=".faq-list">
                <p>
                ScoobyTV stands out by offering a personalized viewing experience through our content request feature. In addition to our extensive library of content, if you can't find the content you're looking for, you can simply request it. Our team will work to add the requested content to our collection, ensuring you always have access to the content you want to watch. This unique feature transforms your viewing experience into a customizable TV, tailored to your preferences.
                </p>
              </div>
            </li>
             <li data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-4" class="collapsed">Is ScoobyTV Safe to Use?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-4" class="collapse" data-bs-parent=".faq-list">
                <p>
                The ScoobyTV is 100% safe to use with no detected malware and reasonable permission requests. It is available in WebApp and MobileApp both, but ensure to access WebApp or Mobile App from our reliable sources. 
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-2" class="collapsed">Can I ⬇️ for offline viewing? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-2" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Yes, you can ⬇️ for offline viewing.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="300">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-3" class="collapsed">Is SccobyTV App available for PC or iOS or Smart TV? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-3" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Yes, ScoobyTV is available on Android Devices, including Android phones, Android tablets, and Android TVs. ScoobyTV also support PC, Window OS, iOS, and any other non-Android OS smart TVs.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-4" class="collapsed">In how many devices ScoobyTV work? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-4" class="collapse" data-bs-parent=".faq-list">
                <p>
                 Scooby TV works in Multiple devices depending on plan.
                </p>
              </div>
            </li>


          </ul>
        </div>

      </div>
    </section><!-- End Frequently Asked Questions Section -->

   

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
              <li><i class="bx bx-chevron-right"></i> <a href="#content">Content</a></li>
              
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
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/667e18ceeaf3bd8d4d153167/1i1e91qar';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
<script>
    document.addEventListener('DOMContentLoaded', () => {
  const TMDB_API_KEY = '41fc7631250b71f97af40cde60e7bfb0';
  const TMDB_API_URL = `https://api.themoviedb.org/3/trending/movie/day?api_key=${TMDB_API_KEY}&page=1`;
  const TMDB_TV_URL = `https://api.themoviedb.org/3/trending/tv/day?api_key=${TMDB_API_KEY}&page=1`;
 initializeSwiperWithFallback('content-slider', 'assets/img/movie');
 initializeSwiperWithFallback('series-slider', 'assets/img/series');

  fetch(TMDB_API_URL)
    .then(response => response.json())
    .then(data => {
      initializeSwiper(data.results); 
    })
    //.catch(error => console.error('Error fetching data:', error));
    .catch(error => {
      console.error('Error fetching data:', error);
      initializeSwiperWithFallback('content-slider', 'assets/img/movie');
    });


    fetch(TMDB_TV_URL)
      .then(response => response.json())
      .then(data => {
        initializeSwiperSeries(data.results);
      })
      //.catch(error => console.error('Error fetching series data:', error));
       .catch(error => {
      console.error('Error fetching series data:', error);
      initializeSwiperWithFallback('series-slider', 'assets/img/series');
    });

function initializeSwiperWithFallback(sliderId, imgPath) {
    const slider = document.getElementById(sliderId);
    for (let i = 1; i <= 10; i++) {
      const slide = document.createElement('div');
      slide.className = 'swiper-slide';
      slide.innerHTML = `
        <div class="testimonial-item">
          <img src="${imgPath}/${i}.jpg" class="poster-img" alt="Fallback Image ${i}">
        </div>`;
      slider.appendChild(slide);
    }

    initializeSwiperInstance();
  }
function initializeSwiperInstance() {
    new Swiper('.testimonials-slider', {
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      allowSlidePrev: false,
      breakpoints: {
        1024: {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        576: {
          slidesPerView: 1,
          spaceBetween: 10,
        }
      }
    });
  }

function initializeSwiperSeries(series) {
    const slider = document.getElementById('series-slider');
    series.forEach(serie => {
      const slide = document.createElement('div');
      slide.className = 'swiper-slide';
      const year =new Date(serie.first_air_date).getFullYear();
      slide.innerHTML = `
        <div class="testimonial-item">
          <img src="https://image.tmdb.org/t/p/w500${serie.poster_path}" class="poster-img" alt="${serie.title}">
         
        </div>`;
      slider.appendChild(slide);
    });

    const swiper = new Swiper('.testimonials-slider', {
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      breakpoints: {
        1024: {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        576: {
          slidesPerView: 1,
          spaceBetween: 10,
        }
      }
    });
  }
  function initializeSwiper(content) {
    const slider = document.getElementById('content-slider');
    content.forEach(movie => {
      const slide = document.createElement('div');
      slide.className = 'swiper-slide';
      const year =new Date(movie.release_date).getFullYear();
      slide.innerHTML = `
        <div class="testimonial-item">
          <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" class="poster-img" alt="${movie.title}">
          
        </div>`;
      slider.appendChild(slide);
    });

    const swiper = new Swiper('.testimonials-slider', {
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      breakpoints: {
        1024: {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        576: {
          slidesPerView: 1,
          spaceBetween: 10,
        }
      }
    });
  }
});
  </script>
  <script>
var removeBranding = function() {
    try {
        var iframe = document.querySelector("iframe[title*=chat]:nth-child(2)");
        if (iframe) {
            var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
            var element = iframeDocument.querySelector("a[class*=tawk-branding]");
            var elementt = iframeDocument.querySelector("a[class*=tawk-button-small]");
            if (element) {
                element.remove();
            }
            if (elementt) {
                elementt.remove();
            }
        }
    } catch (e) {
        console.error(e);
    }
}

var tick = 100;
setInterval(removeBranding, tick);

  </script>
</body>

</html>