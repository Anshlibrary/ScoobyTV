<?php
session_start();
require '../conn.php';

// Initialize status messages array if not set
if (!isset($_SESSION['status_messages'])) {
    $_SESSION['status_messages'] = [];
}

// Check if associate is logged in
if (!isset($_SESSION['associate_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: login.php');
    exit;
}

$associate_id = $_SESSION['associate_id'];

// Query to fetch associate's full name and credits

$associate_sql = "SELECT paid_amt FROM Associate WHERE associate_id = ?";
$associate_stmt = $conn->prepare($associate_sql);
if (!$associate_stmt) {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    exit;
}
$associate_stmt->bind_param('i', $associate_id);
if (!$associate_stmt->execute()) {
    $_SESSION['status_messages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    exit;
}
$associate_result = $associate_stmt->get_result();

if ($associate_result->num_rows === 0) {
    $_SESSION['status_messages'][] = "Associate not found.";
    exit;
}

$associate = $associate_result->fetch_assoc();
$paid_amt = htmlspecialchars($associate['paid_amt']);

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
 .profile-details table {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        border-collapse: collapse;
    }
    .profile-details table th, .profile-details table td {
        padding: 10px;
        text-align: left;
    }
    .profile-details table th {
        background-color: #222;
        color: #fff;
    }
    .profile-details table td {
        background-color: #343a40;
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
          <li><a href="pricing.php">Pricing</a></li>
          <li><a href="signout.php" class="btn btn-sign" style="">Sign out</a></li>
        </ol>
        
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <!-- ======= Header ======= -->
  <header id="header" style="padding:0;">
    <div class="container d-flex align-items-center justify-content-between">
      <!--<h1 class="logo"><a href="index.html"></a></h1> Text logo -->
      <h2 style="font-size: 18px;"><a href="dashboard.php" class="logo"><i class="fa fa-arrow-left"></i></a></h2>
    </div>
  </header><!-- End Header -->


  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
     
      <div class="section-title mt-0" style="padding-bottom:0;">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;">Associate Pricing</h2>
        <div class="my-3">
<?php
if (isset($_SESSION['status_messages']) && count($_SESSION['status_messages']) > 0) {
    foreach ($_SESSION['status_messages'] as $message) {
        echo "<div class='status-message'>$message</div>";
    }
    // Clear messages after displaying
    $_SESSION['status_messages'] = [];
}
 ?>
 </div>
      </div>
         
<div class="row">
      <div class="col-lg-12 mb-3">
     
    <!-- ======= Pricing Section ======= -->
    <section id="pricing" class="pricing section-bg" style="background: linear-gradient(to bottom right, #3b3b3b, #202020);color:#fff;">
      <div class="container" data-aos="fade-up">

        <div class="section-title" style="padding-bottom:5px;">
          <p>Well Curated Credits Plans for you 🙂</p>
        </div>

        <div class="row">


          <div class="col-lg-4 col-md-8 mt-4 mt-md-0" data-aos="fade-up" data-aos-delay="200">
            <div class="box featured">
              <h3 style="background: linear-gradient(to bottom, #7FFF00, #32CD32);">10 Credits (51% OFF)</h3>
              <h4 style="margin-bottom: 0;"><sup>₹</sup><?php echo $paid_amt * 10; ?><span> / 10 Credits</span></h4>
              <ul style="margin-bottom: 0;">
              <li class="na" style="color:#e03131;">₹<?php echo floor(($paid_amt * 10) / (1 - 0.51)); ?> / 10 Credits</li>
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
               <!-- <hr style="margin-top: 0;color:#000;"> -->
              <div class="btn-wrap" style="margin-top: 0;background: #adb5bd;">
                <a href="payment_v3.php?price=<?php echo $paid_amt * 10; ?>&credits=10" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-8 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
            <div class="box">
              <span class="advanced">Gold plan</span>
              <h3 style="background: linear-gradient(to bottom, #FFD700, #FFA500);color:#fff;">20 Credits (59% OFF)</h3>
              <h4 style="margin-bottom: 0;"><sup>₹</sup><?php echo $paid_amt * 20; ?><span> / 20 Credits</span></h4>
              <ul style="margin-bottom: 0;">
               <li class="na" style="color:#e03131;">₹<?php echo floor(($paid_amt * 20) / (1 - 0.59)); ?> / 20 Credits</li>
                <li>TV, Laptop, & Mobile</li>
                <li>Ads-Free</li>
                <li>Unlimited ⬇️</li>
                <li>All Premium content</li>
                <li>1 Device</li>
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
                <a href="payment_v3.php?price=<?php echo $paid_amt * 20; ?>&credits=20" class="btn-buy">Buy Now</a>
              </div>
            </div>

          </div>
   

           
<div class="col-lg-4 col-md-8 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
  <div class="box">
    <span class="advanced">Diamond</span>
    <h3 style="background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%);color:#fff;">50 Credits (71% OFF)</h3>
    <h4 style="margin-bottom: 0;"><sup>₹</sup><?php echo $paid_amt * 50; ?><span> / 50 Credits</span></h4>
    <ul style="margin-bottom: 0;">
      <li class="na" style="color:#e03131;">₹<?php echo floor(($paid_amt * 50) / (1 - 0.71)); ?> / 50 Credits</li>
      <li>TV, Laptop, & Mobile</li>
      <li>Ads-Free</li>
      <li>Unlimited ⬇️</li>
      <li>All Premium content</li>
      <li>1 Device</li>
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
     <a href="payment_v3.php?price=<?php echo $paid_amt * 50; ?>&credits=50" class="btn-buy">Buy Now</a>

      <!-- <button href="" class="offerbtn-disabled" disabled>Start Trial</button>-->
      
    </div>
  </div>
  
</div>


        </div>

      </div>
    </section><!-- End Pricing Section -->
</div>


 


      <div class="row" style="background: linear-gradient(to bottom right, #716d6d, #141414); border-radius:20px;padding:10px;">
<div class="col-lg-12 mb-3 table-responsive" >                     
                
    </div>
</div>

</div>
    </div>
    
  </section>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>