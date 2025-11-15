<!-- HTML or further processing of $user data -->
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
        max-width: 600px;
        margin: 0 auto;
        border-collapse: separate;
    }
  table th, .profile-details table td {
        padding: 10px;
        text-align: left;
    }
    table th {
        background-color: #222;
        color: #fff;
    }
     table td {
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

.img-cntr{
    display: flex;
    align-content: center;
    justify-content: center;
}

 .blog-picture {
        display: block;
        max-width: 100%;
        margin-bottom: 5px;
        height:300px;
        margin-top:5px;
    }

    .profile-container {
        background: linear-gradient(45deg, rgb(90 85 85 / 90%) 0%, rgb(41 41 41 / 90%) 100%);
        padding: 20px;
        border-radius: 10px;
    }
    .copy-icon {
        margin-left: 10px;
        cursor: pointer;
        color: #007bff;
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
 @media (max-width: 768px) {
        .profile-details table th, .profile-details table td {
            display: block;
            width: 100%;
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
          <li><a href="tutorial.php">Tutorial</a></li>
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

</section>

 <section id="" class="section-bg p-1 <?php echo htmlspecialchars($classdata); ?>" >
      <div class="container profile-container" data-aos="fade-up">
        <div class="section-title" style="padding-bottom:0px;">
          <h2 style="margin-bottom:0;" id="how-to-watch">How to Watch?</h2>
        </div>

         <div class="mt-4">
        <h3 style="text-align:center;">Downloads</h3>
        <div class="table-responsive">
           <table class="table table-bordered">
    <thead>
        <tr>
            <th>Device</th>
            <th>Client</th>
            <th>Link</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan="2">Android</td> <!-- This will merge 2 rows under "Android" -->
            <td>Findroid</td>
            <td><a href="https://play.google.com/store/apps/details?id=dev.jdtech.jellyfin" target="_blank">Playstore</a> <i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i></td>
        </tr>
        <tr>
            <td>Jellyfin</td>
            <td><a href="https://play.google.com/store/apps/details?id=org.jellyfin.mobile" target="_blank">Playstore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i></br>
            <a href="https://www.amazon.com/gp/aw/d/B081RFTTQ9" target="_blank">Amazon AppStore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i>
            </td>
        </tr>
        <tr>
            <td rowspan="2">IOS</td> <!-- This will merge 2 rows under "Android" -->
            <td>Jellyfin</td>
            <td><a href="https://apps.apple.com/us/app/jellyfin-mobile/id1480192618"  target="_blank">Apple AppStore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i></td>
        </tr>
         <tr>
            <td>Infuse</td>
            <td><a href="https://apps.apple.com/app/id1136220934?mt=8" target="_blank">Apple AppStore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i>
            </td>
        </tr>
         <tr>
            <td>Android TV</td> <!-- This will merge 2 rows under "Android" -->
            <td>Jellyfin</td>
            <td><a href="https://play.google.com/store/apps/details?id=org.jellyfin.androidtv"  target="_blank">PlayStore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i></br>
            <a href="https://www.amazon.com/gp/aw/d/B07TX7Z725"  target="_blank">Amazon AppStore</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i>
            
            </td>
        </tr>

          <tr>
            <td>Web OS</td> <!-- This will merge 2 rows under "Android" -->
            <td>Jellyfin</td>
            <td><a href="https://us.lgappstv.com/main/tvapp/detail?appId=1030579"  target="_blank">Content Store</a>
            <i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i>
            </td>
        </tr>

           <tr>
            <td>Windows</td> <!-- This will merge 2 rows under "Android" -->
            <td>Jellyfin</td>
            <td><a href="https://github.com/jellyfin/jellyfin-media-player/releases/download/v1.11.1/JellyfinMediaPlayer-1.11.1-windows-x64.exe"  target="_blank">64_bit</a><i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i></br>
            <a href="https://github.com/jellyfin/jellyfin-media-player/releases/download/v1.11.1/JellyfinMediaPlayer-1.11.1-windows-x86.exe"  target="_blank">32_bit</a>
            <i class="fa-regular fa-clipboard copy-icon" onclick="copyLink(this)"></i>
            </td>
        </tr>
        <!-- Add more devices and clients as needed -->
    </tbody>
</table>

        </div>
    </div>
    <script>
function copyLink(element) {
    // Get the previous sibling which is the <a> element
    var link = element.previousElementSibling;

    // Get the href attribute of the <a> tag (the link URL)
    var textToCopy = link.getAttribute('href');

    // Create a temporary input element to copy the text
    var tempInput = document.createElement("input");
    tempInput.value = textToCopy;

    // Append it to the body and select the text
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices

    // Copy the text to clipboard
    document.execCommand("copy");

    // Remove the temporary input element
    document.body.removeChild(tempInput);

    // Optional: Show alert or feedback that the text has been copied
    alert("Copied: " + textToCopy);
}
</script>

        <div class="container mt-4">
        <h3 style="text-align:center;">Jellyfin Host/Server Information</h3>
        <p style="text-align:center;">If one Host does not work, try the alternate Host provided.</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Host</th>
                        <th>Alternate Host</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>stv1.xyz:53570  <i class="fa-regular fa-clipboard copy-icon" onclick="copyText(this)"></i></td>
                        <td>185.193.19.192:53570 <i class="fa-regular fa-clipboard copy-icon" onclick="copyText(this)"></i></td>
                    </tr>
                   
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
    <script>
function copyText(element) {
    // Get the parent td element
    var td = element.parentElement;

    // Get the text content only (excluding the icon), and trim any extra spaces
    var textToCopy = td.childNodes[0].textContent.trim();

    // Create a temporary input element to copy the text
    var tempInput = document.createElement("input");
    tempInput.value = textToCopy;

    // Append it to the body and select the text
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices

    // Copy the text to clipboard
    document.execCommand("copy");

    // Remove the temporary input element
    document.body.removeChild(tempInput);

    // Optional: Show alert or some feedback that text has been copied
    alert("Copied: " + textToCopy);
}
</script>

        <div class="profile-header" style="text-align:left;">
         <h2 class="mt-3"><b>1. How to watch in Mobile?</b></h2>
      <ul>
        <li>Download our partner app.</li>
        <li>Open the App.</li>
        <li>Enter the Host address: <b>stv1.xyz:53570</b></li>
        <li>Now Log in using the username and password provided in your profile.</li>
    </ul>

         <div class="row" style="margin-bottom:20px";>
            <div class="col-md-4">
                <div class="text-center img-cntr">
         <img src="../assets/img/jelly1.webp" alt="login Steps" class="blog-picture">
        </div>
              
            </div>
            <div class="col-md-4">
                <div class="text-center img-cntr">
         <img src="../assets/img/jelly2.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>
            <div class="col-md-4">
        <div class="text-center img-cntr">
         <img src="../assets/img/jelly3.webp" alt="login Steps" class="blog-picture">
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
         <img src="../assets/img/login1.webp" alt="login Steps" class="blog-picture">
        </div>
            </div>

            <div class="col-md-6">
                <div class="text-center img-cntr">
         <img src="../assets/img/login2.webp" alt="login Steps" class="blog-picture">
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
         <img src="../assets/img/vlc1.webp" alt="login Steps" class="blog-picture"  style="margin-bottom:20px;">
        </div>
     <li>Now copy the Stream url of content whatever you want to watch from Scoobytv.</li>
        <div class="text-center img-cntr">
         <img src="../assets/img/vlc2.webp" alt="login Steps" class="blog-picture">
        </div>
           <div class="text-center img-cntr">
         <img src="../assets/img/vlc3.webp" alt="login Steps" class="blog-picture" style="margin-bottom:20px;">
        </div>
      <li>Now Paste the stream url in VLC Media Player and click on Play button.</li>
         <div class="text-center img-cntr">
         <img src="../assets/img/vlc4.webp" alt="login Steps" class="blog-picture" style="margin-bottom:5px;">
        </div>
     </ul>
<span> <i>Cheers</i>, Now you can see the ScoobyTV content in VLC Media Player.</span>

        <div class="btn-wrap text-center mt-3" >
        <p>if you're facing any issue, donot hesitate to Chat to our support Team-
                <a href="../support.php" class="btn btn-buy">Support</a>
        </div>
      </div>
      <div class="btn-wrap text-center mt-3 cust_feedback mb-3" ><a href="../feedback.php" class="btn btn-buy">Submit you feedback</a></div> 
      
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
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>