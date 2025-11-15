<?php
// Start session
session_start();
// Check if the user is already logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: Babualogin.php");
    exit;
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
        background: linear-gradient(42deg, #1e1289 0%, #2c0656 100%);
color: #fff;
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
.contact .my-email-form .loading:before {
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
</style>
</head>

<body>

  
  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="../index.php">Home</a></li>
          <li>Services</li>
        </ol>
        <a href="../signout.php" class="btn btn-sign" style="">Sign out</a>
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <div class="container">
   <h2 style="margin-left:auto;margin-right: auto;padding-left:10px ;font-size: 18px;"></h2>
   
  </div>
  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;">Select Service</h2>
      </div>
      <div class="row">

        <div class="col-lg-4 mb-3">
          <form action="" method="post" role="form" class="my-email-form">
            <div class="row">
                  <div class="col-md-12 form-group mt-1">
                <div class="btn-wrap text-center mt-3 mb-3" >
                  <p style="color:#141414">Payment Requests</p>
                  <a href="BabuaPanel.php" class="btn btn-buy" >Open</a>
                </div>
              </div> 
            </div>
         </form>
</div>


<div class="col-lg-4 mb-3">
  <form action="" method="post" role="form" class="my-email-form">
    <div class="row">
          <div class="col-md-12 form-group mt-1">
        <div class="btn-wrap text-center mt-3 mb-3" >
          <p style="color:#141414">Disable expired users</p>
          <a href="disable_user.php" class="btn btn-buy">Open</a>
        </div>
      </div> 
    </div>
 </form>
</div>

<div class="col-lg-4 mb-3">
  <form action="" method="post" role="form" class="my-email-form">
    <div class="row">
          <div class="col-md-12 form-group mt-1">
        <div class="btn-wrap text-center mt-3 mb-3" >
          <p style="color:#141414">Users Data</p>
          <a href="users.php" class="btn btn-buy">Open</a>
        </div>
      </div> 
    </div>
 </form>
</div>

<div class="col-lg-4 mb-3">
  <form action="" method="post" role="form" class="my-email-form">
    <div class="row">
          <div class="col-md-12 form-group mt-1">
        <div class="btn-wrap text-center mt-3 mb-3" >
          <p style="color:#141414">Reseller Users</p>
          <a href="resellers.php" class="btn btn-buy" >Open</a>
        </div>
      </div> 
    </div>
 </form>
</div>

<div class="col-lg-4 mb-3">
  <form action="" method="post" role="form" class="my-email-form">
    <div class="row">
          <div class="col-md-12 form-group mt-1">
        <div class="btn-wrap text-center mt-3 mb-3" >
          <p style="color:#141414">All Coupons</p>
          <a href="coupons.php" class="btn btn-buy">Open</a>
        </div>
      </div> 
    </div>
 </form>
</div>
<div class="col-lg-4 mb-3">
  <form action="" method="post" role="form" class="my-email-form">
    <div class="row">
          <div class="col-md-12 form-group mt-1 text-center">
          <small class="text-dark">Donot take backup's frequently otherwise, server will go down; once in a week is fine!!!</small>
        <div class="btn-wrap text-center mt-3 mb-3" >
          <p style="color:#141414">Take Backup</p>
          
          <button class="btn btn-buy" id="backup_btn" disabled>Run</button>
        </div>
      </div> 
    </div>
 </form>
</div>

</div>
    </div>
    
  </section>
</section>
  </main><!-- End #main -->
 <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("backup_btn").addEventListener("click", function(event) {
            var button = document.getElementById("backup_btn");
            button.disabled = true;
            button.classList.add("loading");
            window.location.href = '../backup.php';
        });
    });
    </script>


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
 
</body>
</html>