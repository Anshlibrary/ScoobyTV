<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, full_name, email, password, picture, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$classdata='';
if ($result->num_rows === 0) {
    echo "<h2 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>No user found</h2>";
    exit;
}

$user = $result->fetch_assoc();
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
        font-family: Arial, sans-serif;
        background-color: #141414;
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
    }
    
    .profile-container {
        background: linear-gradient(45deg, rgb(90 85 85 / 90%) 0%, rgb(41 41 41 / 90%) 100%);
        padding: 20px;
        border-radius: 10px;
    }
    
    .profile-picture {
        border-radius: 50%;
        width: 100px;
        margin-bottom: 5px;
        object-fit: cover;
    }
    .blog-picture {
        display: block;
        max-width: 100%;
        margin-bottom: 5px;
        height:300px;
        margin-top:5px;
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
    .section-bg{
        background: linear-gradient(85deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);
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
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
color: #fff;
}
.btn-buy{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
    display: inline-block;
    padding: 5px 10px;
    
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}
.hidden{
    display:none;
}
  .btn-buy:hover{
        background: linear-gradient(42deg, #1e1289 0%, #2c0656 100%);
color: #fff;
}
.btn-series{
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    display: inline-block;
    padding: 5px 10px;
    margin-left:5px;
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}
.btn-series:hover{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
color: #fff;
}
.sent-message {
    display: none;
    color: #fff;
    background: #18d26e;
    text-align: center;
    padding: 15px;
    font-weight: 600;
}
.img-cntr{
    display: flex;
    align-content: center;
    justify-content: center;
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
.cust_feedback a {
    color: #edf12f;
    font-weight: 700;
    text-decoration: none;
}

.cust_feedback a:hover {
    color: #5846f9; 
    text-decoration: underline;
}
    @media (max-width: 768px) {
        .profile-details table th, .profile-details table td {
            display: block;
            width: 100%;
        }
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo"><img src="assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="index.php#movies">Movies</a></li>
          <li><a class="nav-link scrollto" href="index.php#series">Series</a></li>
          <li><a class="nav-link scrollto " href="index.php#pricing">Pricing</a></li>
          <li><a class="nav-link scrollto" href="index.php#faq">FAQ's</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <!-- End Header -->

  <main id="main">
    <section id="contact" class="contact section-bg" style="padding-bottom:50px;" >
      <div class="container profile-container" data-aos="fade-up">
        
        <div class="profile-header mb-1">
        <?php
$picture = ($user['picture'] != 'not available') ? $user['picture'] : 'assets/img/profile-user.png';
?>
          <img src="<?php echo htmlspecialchars($picture); ?>" alt="User Picture" class="profile-picture">
       
          <div class="section-title pb-1">
          <h2 style="margin-bottom:0;padding-bottom:2px;"><?php echo htmlspecialchars($user['full_name']); ?></h2>
        </div>
        </div>
        

        <div class="profile-details">
          <table>
            <tr >
             <th>Status</th>

                <td style="background-color: <?php echo ($user['purchase'] === 'Successfull' || $user['purchase'] === 'Successfull & Verified') ? '#1fff1f' : '#fa5252'; ?>" class="<?php echo $user['purchase'] === 'Successfull' ? 'loading' : ''; ?>">
            <?php
  $planExpiry = new DateTime($user['plan_expiry']);
  $currentDate = new DateTime();
if ($currentDate > $planExpiry) {
     $ExpiredHide='hidden';
// If purchase is expired
echo '<span style="background-color:#fa5252; color: #fff;">Plan Expired</span> <a href="payment.html" class="btn btn-buy">Renew Now</a>';
} 
else {
    if ($user['purchase'] === 'Payment not made') {
    $classdata='hidden';
// If purchase is 'Payment not made', show 'Plan not Active' in red
echo '<span style="background-color:#fa5252; color: #fff;">Plan not Active</span> <a href="payment.html" class="btn btn-buy">Pay Now</a>';
  }
  else if($user['purchase'] === 'Successfull'){
    echo '<span style="background-color:#1fff1f; color: #fff;font-weight:bold;">Activating the OTT-ID</span><br>';
    echo '<span style="margin-left:39px;">please wait for sometime...</span>';
     echo '<script type="text/javascript">
            setTimeout(function(){
                window.location.reload(1);
            }, 5000);
          </script>';
    }
    else if($user['purchase'] === 'Successfull & Verified'){
echo '<span style="background-color:#1fff1f; color: #fff;font-weight:bold;">Plan Active</span><ul>
        <li>To request content - Goto ScoobyTV</li>
        <li>To watch content - Click Watch Now</li>
         </ul>
        </td></tr>';
echo '<tr><th></th><td><a href="http://stv1.xyz:53575" class="btn btn-buy" style="margin-left:10px;">Goto ScoobyTV</a><a href="http://stv1.xyz:53570" class="btn btn-buy" style="margin-left:10px;">Watch Now</a><p></p>';
echo '<a style="margin-top:30px;margin-left:10px;font-weight:bold;" href="#how-to-watch" onclick="scrollToHowToWatch()">How to download app? <img src="assets/img/google-play.png" alt="Icon" style="width:20px;height:20px;vertical-align:middle;margin-left:5px;"><img src="assets/img/ios.png" alt="Icon" style="width:20px;height:20px;vertical-align:middle;margin-left:5px;"><img src="assets/img/windows.png" alt="Icon" style="width:20px;height:20px;vertical-align:middle;margin-left:5px;"></a>';
 echo '<script type="text/javascript">
            function scrollToHowToWatch() {
                document.getElementById("how-to-watch").scrollIntoView({ behavior: "smooth" });
            }
          </script>';
    }
   else {
// Otherwise, show the purchase status
echo '<span style="background-color:#fa5252; color: #fff;">Payment Verification Failed.</span> <a href="payment.html" class="btn btn-buy">Pay Now</a>'; }}?>
</td>
</tr>

 <tr>
              <th>Username</th>
              <td style="background-color:#868e96;display:flex;" >
              
              <input type="text" class="form-control" id="usernamecopy" placeholder="" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="width:80%;margin-right:20px;">
                         <a type="button" class="btn btn-outline-secondary" id="copyButton" onclick="copyUPI()" style="background: linear-gradient(42deg, #4a47619c 0%, #241f29 100%);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
</svg>
                        </a>
                       
              </td>
            </tr>
            <tr>
              <th>Email</th>
              <td style="background-color:#868e96;"><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
              <th>Password</th>
              <td style="background-color:#868e96;">
                <div class="password-container">
                  <input type="password" id="password" value="<?php echo htmlspecialchars($user['password']); ?>" readonly>
                  <i class="fa fa-eye" id="togglePassword"></i>
                </div>
              </td>
              </tr>
              <tr>
              <th>Full Name</th>
              <td><?php echo htmlspecialchars($user['full_name']); ?></td>
            </tr>
            <tr class=" <?php echo $user['phone'] === 'Not available' ? 'hidden' : ''; ?>">
              <th>Phone</th>
              <td><?php echo htmlspecialchars($user['phone']); ?></td>
            </tr>
            <tr class="<?php echo htmlspecialchars($classdata); ?>">
              <th>Created</th>
              <td>
              
              <?php 
                $datetime2 = new DateTime($user['created']);
    echo htmlspecialchars($datetime2->format('Y-m-d'));
    ?>
              </td>
            </tr>
            <tr class="<?php echo htmlspecialchars($classdata); ?> <?php echo htmlspecialchars($ExpiredHide); ?>">
              <th>Plan Valid For</th>
              <td>
             <?php
if ($user['txn_id'] === '3_day_trial') {
    echo htmlspecialchars('1 day');
} else {
    echo htmlspecialchars($user['plan_valid_for'] . ' month');
}
?>
              </td>
            </tr>
            <tr class="<?php echo htmlspecialchars($classdata); ?> <?php echo htmlspecialchars($ExpiredHide); ?>">
              <th>Plan Amount</th>
              <td><?php echo htmlspecialchars('₹'.$user['plan_amount'].' - Premium'); ?></td>
            </tr>
            <tr class="<?php echo htmlspecialchars($ExpiredHide); ?>">
              <th>Purchase</th>
              <td style="color: <?php echo ($user['purchase'] === 'Successfull' || $user['purchase'] === 'Successfull & Verified') ? '#1fff1f' : '#fa5252'; ?>">
              <b><?php echo htmlspecialchars($user['purchase']); ?></b>
              </td>
            </tr>
            <tr class="<?php echo htmlspecialchars($classdata); ?>">
              <th>Plan Expiry</th>
              <td>
             <?php 
    $datetime = new DateTime($user['plan_expiry']);
    echo htmlspecialchars($datetime->format('Y-m-d'));
    ?>
              </td>
            </tr>
            <tr class="<?php echo htmlspecialchars($classdata); ?> <?php echo htmlspecialchars($ExpiredHide); ?>"> 
              <th>No. of Devices</th>
              <td><?php echo htmlspecialchars($user['no_of_devices'] . ' Allowed'); ?></td>
            </tr>
          
            
          </table>
        </div>
        <div class="btn-wrap text-center mt-3" >
                <a href="signout.php" class="btn btn-sign">Sign out</a>
                
              </div>
  <div class="btn-wrap text-center mt-3 cust_feedback <?php echo ($user['purchase'] === 'Successfull' || $user['purchase'] === 'Successfull & Verified') ? '' : 'hidden'; ?>" ><a href="feedback.php">Submit you feedback</a></div> 
      </div>
      
    </section>
    <section id="" class="section-bg p-1 <?php echo htmlspecialchars($classdata); ?>" >
      <div class="container profile-container" data-aos="fade-up">
        <div class="section-title" style="padding-bottom:0px;">
          <h2 style="margin-bottom:0;" id="how-to-watch">How to Watch?</h2>
        </div>
       
        <div class="profile-header" style="text-align:left;">
         <h2 class="mt-3"><b>1. How to watch in Mobile?</b></h2>
        <span>
    <ul>
        <li>Please wait for few minutes to activate your OTT-ID (this usually takes 2-5 minutes, but can take up to 20 minutes).</li>
        <li>Once activated, you will see a "Go to ScoobyTV" button. Click on this button.</li>
        <li>Log in using the username and password provided in your profile to access your favorite content on the web.</li>
        <li><b>For better experience download our app, follow below instructions:-</b></li>
        </ul>
        
        <div class="d-flex justify-content-center" style="align-items: center;gap:20px;">
 <a href="https://play.google.com/store/apps/details?id=dev.jdtech.jellyfin"  target="_blank"><img src="assets/img/playstore.png" alt="Icon" style="width:140px;vertical-align:middle;"></a>
        <a href="https://apps.apple.com/us/app/jellyfin-mobile/id1480192618"  target="_blank"><img src="assets/img/applestore.png" alt="Icon" style="width:140px;vertical-align:middle;"></a>
        </div>
  
   <div class="container mt-4">
        <h3>Server IP Information</h3>
        <p>If one IP does not work, try the alternate IP provided.</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Server IP</th>
                        <th>Alternate IP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>stv1.xyz:53570</td>
                        <td>156.67.110.241:53570</td>
                    </tr>
                   
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>


       
        <ul>
        <li>Download our partner app <b>Findroid</b> for android devices from the Google Play Store or for iOS - download <b>Jellyfin</b> from Apple Store on your mobile device.</li>
        <li>Open the Jellyfin/Findroid app and select "Choose server".</li>
        <li>Enter the IP address: <b>stv1.xyz:53570</b></li>
        <li>Now Log in using the username and password provided in your profile.</li>
    </ul>

         <div class="row" style="margin-bottom:20px";>
            <div class="col-md-4">
                <div class="text-center img-cntr">
         <img src="assets/img/findroid1.webp" alt="login Steps" class="blog-picture">
        </div>
              
            </div>
            <div class="col-md-4">
                <div class="text-center img-cntr">
         <img src="assets/img/findroid2.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>
            <div class="col-md-4">
        <div class="text-center img-cntr">
         <img src="assets/img/findroid3.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>
        </div>
   <span> <i>Cheers!</i> Now you can enjoy all ScoobyTV content according to your subscription plan.
</span>
      


        <h2 class="mt-3"><b>2. How to watch in TV?</b></h2>
        <span>Download our partner app '<b>Jellyfin</b>' from Google Playstore in your AndroidTv or SmartTv. Then in Choose server put IP - <b>stv1.xyz:53570</b>. After filling the IP, use your username and password [<i>provided in your profile above</i>] to login. 
        <i>Cheers</i>, Now you can see All ScoobyTV content as per your plan.</span>


        <div class="row" style="margin-top:20px";>
            <div class="col-md-6">
                 <div class="text-center img-cntr">
         <img src="assets/img/login1.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>

            <div class="col-md-6">
                <div class="text-center img-cntr">
         <img src="assets/img/login2.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>

        </div>

        <h2 class="mt-3"><b>3. How to watch in Laptop?</b></h2>
        <ul><li>To watch in Laptop you can Just click on Goto ScoobyTV button above. On Next Page, use your username and password [<i>provided in your profile</i>] to login. </li></ul>
         <ul><li><b>For best experience: </b>You can also download our partner Jellyfin media Player in Laptop or PC.</li></ul>
 <div class="container mt-4">
        <h3>Jellyfin Media Player</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>64-bit</th>
                        <th>32-bit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
   <td><a href="https://github.com/jellyfin/jellyfin-media-player/releases/download/v1.11.1/JellyfinMediaPlayer-1.11.1-windows-x64.exe" class="download-button" download>Download</a></td>
   <td><a href="https://github.com/jellyfin/jellyfin-media-player/releases/download/v1.11.1/JellyfinMediaPlayer-1.11.1-windows-x86.exe" class="download-button" download>Download</a></td>
                    </tr>
                   
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
         <ul>
         <li>After successfull installation, fill the Server IP provided.</li>
         <li>Now, fill your username and password.</li>
         </ul>
  
        </div>
       
        
           <div class="container mt-4">
        <h3>VLC media player</h3>
        <p><i>Do you know? you can also watch scoobytv in VLC Media player, follow below guide:-</i> </p>
        <p style="margin-bottom:0;">Download VLC from below if not downloaded yet.</i> </p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>64-bit</th>
                        <th>32-bit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
   <td><a href="https://www.videolan.org/vlc/download-windows.html" class="download-button" download>Download</a></td>
   <td><a href="https://www.videolan.org/vlc/download-windows.html" class="download-button" download>Download</a></td>
                    </tr>
                   
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
     <ul>
     <li>Open the VLC Media Player after successfull installtion.</li>
     <li>Click Media - > Open Network Stream.</li>
      <div class="text-center img-cntr">
         <img src="assets/img/vlc1.webp" alt="login Steps" class="blog-picture"  style="margin-bottom:20px;">
        </div>
     <li>Now copy the Stream url of content whatever you want to watch from Scoobytv.</li>
        <div class="text-center img-cntr">
         <img src="assets/img/vlc2.webp" alt="login Steps" class="blog-picture">
        </div>
           <div class="text-center img-cntr">
         <img src="assets/img/vlc3.webp" alt="login Steps" class="blog-picture" style="margin-bottom:20px;">
        </div>
      <li>Now Paste the stream url in VLC Media Player and click on Play button.</li>
         <div class="text-center img-cntr">
         <img src="assets/img/vlc4.webp" alt="login Steps" class="blog-picture" style="margin-bottom:5px;">
        </div>
     </ul>
<span> <i>Cheers</i>, Now you can see the ScoobyTV content in VLC Media Player.</span>

        <div class="btn-wrap text-center mt-3" >
        <p>if you're facing any issue, donot hesitate to Chat to our support Team-
                <a href="support.php" class="btn btn-buy">Support</a>
        </div>
      </div>
      
    </section>

  </main>
  <!-- End #main -->

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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
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
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
  </script>

  <script>
    function copyUPI() {
      var copyText = document.getElementById("usernamecopy");
      copyText.disabled = false; // Enable the input field to select the text
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices
      document.execCommand("copy");
      copyText.disabled = true; // Disable the input field again
      alert("Copied: " + copyText.value);
    }
    </script>
</body>
</html>
